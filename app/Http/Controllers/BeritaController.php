<?php

namespace App\Http\Controllers;

use App\Models\Berita;
use App\Models\Website;
use Illuminate\Http\Request;
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
                ->rawColumns(['status', 'featured_image', 'target_websites', 'action'])
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
            ]);

            $syncData = [];
            foreach ($newsItem['website_ids'] as $webId) {
                $website = Website::findOrFail($webId);
                $syncData[$webId] = [
                    'detail_url' => rtrim($website->url, '/') . '/detail_berita/' . $berita->id
                ];
            }

            $berita->websites()->sync($syncData);
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
        if ($berita->featured_image) {
            Storage::disk('public')->delete($berita->featured_image);
        }
        $berita->delete();
        return redirect()->route('beritas.index')->with('success', 'Berita berhasil dihapus.');
    }
}
