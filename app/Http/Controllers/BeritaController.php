<?php

namespace App\Http\Controllers;

use App\Models\BeritaWebsite;
use App\Models\Website;
use App\Models\Berita;
use App\Models\NewsHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Storage;

class BeritaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Berita::with('websites');

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->editColumn('status', function ($row) {
                    $statusClass = $row->status === 'Published' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600';
                    return '<span class="px-3 py-1 text-xs font-bold rounded-full ' . $statusClass . '">' . $row->status . '</span>';
                })
                ->editColumn('featured_image', function ($row) {
                    if ($row->featured_image) {
                        return '<img src="' . asset('storage/' . $row->featured_image) . '" class="h-10 w-10 object-cover rounded-lg shadow-sm" />';
                    }
                    return '<span class="text-slate-400 text-xs">-</span>';
                })
                ->editColumn('kategori', function ($row) {
                    if ($row->kategori) {
                        return '<span class="px-3 py-1 text-xs font-bold rounded-full bg-violet-100 text-violet-700">' . $row->kategori . '</span>';
                    }
                    return '<span class="text-slate-400 text-xs">-</span>';
                })
                ->editColumn('tanggal_publikasi', function ($row) {
                    if ($row->tanggal_publikasi) {
                        return '<span class="text-sm text-slate-700 font-medium">' . $row->tanggal_publikasi->format('d M Y') . '</span>';
                    }
                    return '<span class="text-slate-400 text-xs">-</span>';
                })
                ->addColumn('target_websites', function ($row) {
                    $badges = $row->websites->map(function ($web) {
                        return '<span class="inline-block px-2 py-0.5 bg-primary-50 text-primary-700 text-[10px] font-bold rounded-md border border-primary-100 mb-1 mr-1">' . $web->nama_website . '</span>';
                    })->implode('');
                    return $badges ?: '<span class="text-red-500 text-[10px] font-bold italic">Belum pilih web</span>';
                })
                ->addColumn('action', function ($row) {
                    return '
                        <div class="flex items-center gap-2">
                            <a href="' . route('beritas.show', $row->id) . '" class="p-2 text-primary-600 hover:bg-primary-50 rounded-lg transition-colors" title="Lihat Detail">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </a>
                            <form action="' . route('beritas.destroy', $row->id) . '" method="POST" class="inline" onsubmit="return confirm(\'Apakah Anda yakin?\')">
                                ' . csrf_field() . '
                                ' . method_field('DELETE') . '
                                <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Hapus">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>';
                })
                ->rawColumns(['status', 'featured_image', 'kategori', 'tanggal_publikasi', 'target_websites', 'action'])
                ->make(true);
        }

        return view('beritas.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $websites = Website::where('status', true)->get();
        return view('beritas.create', compact('websites'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'news' => 'required|array|min:1',
            'news.*.judul' => 'required|string|max:255',
            'news.*.konten' => 'required',
            'news.*.featured_image' => 'nullable|image|max:2048',
            'news.*.website_ids' => 'required|array|min:1',
            'news.*.status' => 'required|in:Draft,Published',
            'news.*.tanggal_publikasi' => 'nullable|date',
            'news.*.kategori' => 'required|string|max:255',
        ]);

        foreach ($request->news as $newsItem) {
            $imagePath = null;
            if (isset($newsItem['featured_image'])) {
                $imagePath = $newsItem['featured_image']->store('news', 'public');
            }

            $berita = Berita::create([
                'judul' => $newsItem['judul'],
                'konten' => $newsItem['konten'],
                'featured_image' => $imagePath,
                'status' => $newsItem['status'],
                'tanggal_publikasi' => $newsItem['tanggal_publikasi'] ?? null,
                'kategori' => $newsItem['kategori'] ?? null,
            ]);

            $syncData = [];
            foreach ($newsItem['website_ids'] as $webId) {
                $website = Website::findOrFail($webId);

                $result = $this->syncToWordpress($berita, $website, $imagePath);

                $syncData[$webId] = [
                    'website_url' => $website->url,
                    'detail_url' => $result['detail_url'],
                    'wp_post_id' => $result['wp_post_id'],
                ];
            }

            $berita->websites()->sync($syncData);

            // Log History per Website
            foreach ($syncData as $webId => $pivotData) {
                NewsHistory::create([
                    'berita_id' => $berita->id,
                    'website_id' => $webId,
                    'user_id' => auth()->id(),
                    'judul' => $berita->judul,
                    'status' => $berita->status,
                    'detail_url' => $pivotData['detail_url'],
                ]);
            }
        }

        return redirect()->route('beritas.index')->with('success', 'Berita berhasil disimpan.');
    }

    /**
     * Handle CKEditor Image Upload
     */
    public function uploadImage(Request $request)
    {
        if ($request->hasFile('upload')) {
            $originName = $request->file('upload')->getClientOriginalName();
            $fileName = pathinfo($originName, PATHINFO_FILENAME);
            $extension = $request->file('upload')->getClientOriginalExtension();
            $fileName = $fileName . '_' . time() . '.' . $extension;

            $request->file('upload')->storeAs('news/content', $fileName, 'public');

            $url = asset('storage/news/content/' . $fileName);
            return response()->json(['fileName' => $fileName, 'uploaded' => 1, 'url' => $url]);
        }
    }
    /**
     * Display the specified resource.
     */
    public function show(Berita $berita)
    {
        $berita->load('websites');
        return view('beritas.show', compact('berita'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Berita $berita)
    {
        // 1. Hapus dari semua WordPress
        foreach ($berita->websites as $website) {
            $wpPostId = $website->pivot->wp_post_id;
            if ($wpPostId) {
                $this->deleteFromWordpress($website, $wpPostId);
            }
        }

        // 2. Hapus gambar lokal
        if ($berita->featured_image) {
            Storage::disk('public')->delete($berita->featured_image);
        }

        // 3. Hapus record database (beserta pivot karena cascade)
        $berita->delete();

        return redirect()->route('beritas.index')->with('success', 'Berita berhasil dihapus dari semua platform.');
    }

    /**
     * Sync to WordPress
     * Returns wp_post_id on success, error message on failure
     */
    public function syncToWordpress($postLaravel, $website, $imagePath = null)
    {
        $user = $website->username;
        $appPass = $website->password;
        $baseUrl = $website->url;

        // 1. UPLOAD GAMBAR DULU (Jika ada)
        $featuredMediaId = null;
        if ($imagePath) {
            $fullPath = storage_path('app/public/' . $imagePath);
            if (file_exists($fullPath)) {
                $imageResponse = Http::withHeaders([
                        'User-Agent' => 'Mozilla/5.0',
                        'Content-Disposition' => 'attachment; filename="'.basename($fullPath).'"',
                    ])
                    ->withBasicAuth($user, $appPass)
                    ->withoutVerifying()
                    ->withBody(file_get_contents($fullPath), 'image/jpeg')
                    ->post($baseUrl . '?rest_route=/wp/v2/media');

                if ($imageResponse->successful()) {
                    $featuredMediaId = $imageResponse->json()['id'];
                } else {
                    \App\Models\SyncFailedLog::updateOrCreate(
                        ['berita_id' => $postLaravel->id, 'website_id' => $website->id],
                        [
                            'error_message' => 'Gagal upload gambar: ' . $imageResponse->body(),
                            'response_body' => $imageResponse->body(),
                            'status' => 'failed_image',
                        ]
                    );
                }
            }
        }

        // 2. AMBIL CATEGORY ID DARI WORDPRESS (berdasarkan slug)
        $categoryId = null;
        if ($postLaravel->kategori) {
            $catResponse = Http::withHeaders(['User-Agent' => 'Mozilla/5.0'])
                ->withBasicAuth($user, $appPass)
                ->withoutVerifying()
                ->get($baseUrl . '?rest_route=/wp/v2/categories', [
                    'slug' => $postLaravel->kategori
                ]);

            if ($catResponse->successful() && !empty($catResponse->json())) {
                $categoryId = $catResponse->json()[0]['id'];
            } else {
                $createCatResponse = Http::withHeaders(['User-Agent' => 'Mozilla/5.0'])
                    ->withBasicAuth($user, $appPass)
                    ->withoutVerifying()
                    ->post($baseUrl . '?rest_route=/wp/v2/categories', [
                        'name' => ucfirst(str_replace('-', ' ', $postLaravel->kategori)),
                        'slug' => $postLaravel->kategori
                    ]);

                
                if ($createCatResponse->successful()) {
                    $categoryId = $createCatResponse->json()['id'];
                } else {
                    $errorResponse = $createCatResponse->json();
                    // If category name already exists, extract term_id from error
                    if (isset($errorResponse['code']) && $errorResponse['code'] === 'term_exists') {
                        $categoryId = $errorResponse['data']['term_id'] ?? null;
                    }
                }
            }
        }

        // 3. UPLOAD POSTINGAN
        $postResponse = Http::withHeaders(['User-Agent' => 'Mozilla/5.0'])
            ->withBasicAuth($user, $appPass)
            ->withoutVerifying()
            ->post($baseUrl . '?rest_route=/wp/v2/posts', [
                'title'   => $postLaravel->judul,
                'content' => $postLaravel->konten,
                'categories' => $categoryId ? [$categoryId] : [],
                'date' => $postLaravel->tanggal_publikasi ? $postLaravel->tanggal_publikasi . 'T00:00:00' : null,
                'status'  => 'publish',
                'featured_media' => $featuredMediaId,
            ]);

        if ($postResponse->successful()) {
            $wpPostId = $postResponse->json()['id'];
            $wpDetailUrl = rtrim($baseUrl, '/') . '/?p=' . $wpPostId;
            return [
                'wp_post_id' => $wpPostId,
                'detail_url' => $wpDetailUrl,
            ];
        }

        $errorMessage = "Gagal Sinkron: " . $postResponse->body();
        \App\Models\SyncFailedLog::updateOrCreate(
            ['berita_id' => $postLaravel->id, 'website_id' => $website->id],
            [
                'error_message' => $errorMessage,
                'response_body' => $postResponse->body(),
                'status' => 'failed',
            ]
        );

        return [
            'wp_post_id' => null,
            'detail_url' => null,
            'error' => $errorMessage,
        ];
    }

    public function deleteFromWordpress($website, $wpPostId)
    {
        if (!$wpPostId || !$website) {   
            return false;
        }

        $user = $website->username;
        $appPass = $website->password;
        $baseUrl = $website->url;

        $response = Http::withHeaders(['User-Agent' => 'Mozilla/5.0'])
            ->withBasicAuth($user, $appPass)
            ->withoutVerifying()
            ->delete($baseUrl . "?rest_route=/wp/v2/posts/{$wpPostId}&force=true");

        return $response->successful() || $response->status() == 404;
    }

    public function manualSyncToWP() {
        //delete all news history
        NewsHistory::truncate();

        $beritaWebistes = BeritaWebsite::all();
        foreach ($beritaWebistes as $beritaWebsite) {
            
            $berita = Berita::find($beritaWebsite->berita_id);
            $website = Website::find($beritaWebsite->website_id);
            $imagePath = $berita->featured_image;
            $result = $this->syncToWordpress($berita, $website, $imagePath);
            
            if ($result['wp_post_id']) {
                $beritaWebsite->wp_post_id = $result['wp_post_id'];
                $beritaWebsite->detail_url = $result['detail_url'];
                $beritaWebsite->save();

                NewsHistory::create([
                    'berita_id' => $berita->id,
                    'website_id' => $website->id,
                    'user_id' => 1,
                    'judul' => $berita->judul,
                    'status' => $berita->status,
                    'detail_url' => $result['detail_url'],
                ]);
            }
        }
    }

}
