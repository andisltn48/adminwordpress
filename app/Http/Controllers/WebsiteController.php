<?php

namespace App\Http\Controllers;

use App\Models\Website;
use Illuminate\Http\Request;
use App\Exports\WebsitesExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

class WebsiteController extends Controller
{
    public function export(Request $request)
    {
        $status = $request->get('status');
        return Excel::download(new WebsitesExport($status), 'websites.xlsx');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = \App\Models\Website::query();

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            return \Yajra\DataTables\Facades\DataTables::of($query)
                ->addIndexColumn()
                ->editColumn('status', function ($row) {
                    $statusClass = $row->status ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600';
                    $statusLabel = $row->status ? 'Aktif' : 'Non-aktif';
                    return '<span class="px-3 py-1 text-xs font-bold rounded-full ' . $statusClass . '">' . $statusLabel . '</span>';
                })
                ->addColumn('action', function ($row) {
                    return '
                        <div class="flex items-center gap-2">
                            <button @click="editWebsite(' . htmlspecialchars(json_encode([
                            'id' => $row->id,
                            'nama_website' => $row->nama_website,
                            'url' => $row->url,
                            'username' => $row->username,
                            'password' => $row->password,
                            'status' => (bool) $row->status,
                            'updateUrl' => route('websites.update', $row->id)
                        ])) . ')" class="p-2 text-primary-600 hover:bg-primary-50 rounded-lg transition-colors" title="Edit">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </button>
                            <form action="' . route('websites.destroy', $row->id) . '" method="POST" class="inline" onsubmit="return confirm(\'Apakah Anda yakin?\')">
                                ' . csrf_field() . '
                                ' . method_field('DELETE') . '
                                <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Hapus">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>';
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }

        return view('websites.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('websites.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_website' => 'required|string|max:255',
            'url' => 'required|url|max:255',
            'username' => 'required|string|max:255',
            'password' => 'required|string|max:255',
        ]);

        try {
            $testUrl = $request->url.'/wp-json/wp/v2/users/me';
            $response = Http::withBasicAuth($request->username, $request->password)
                ->timeout(10) // Maksimal tunggu 10 detik
                ->get($testUrl);

            if ($response->successful()) {
                $data = $response->json();
            } else {
                return redirect()->route('websites.index')->with('error', 'URL atau Password salah');
            }
        } catch (\Exception $e) {
            return redirect()->route('websites.index')->with('error', $e->getMessage());
        }
        Website::create([
            'nama_website' => $request->nama_website,
            'url' => $request->url,
            'username' => $request->username,
            'password' => $request->password,
            'status' => $request->boolean('status'),
        ]);

        return redirect()->route('websites.index')->with('success', 'Website berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Website $website)
    {
        return view('websites.show', compact('website'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Website $website)
    {
        return view('websites.edit', compact('website'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Website $website)
    {
        $request->validate([
            'nama_website' => 'required|string|max:255',
            'url' => 'required|url|max:255',
            'username' => 'required|string|max:255',
            'password' => 'required|string|max:255',
        ]);

        try {
            $url = $request->url."/wp-json/wp/v2/users/me";
            $response = Http::withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7)',
                'Accept' => 'application/json',
            ])
            // Kita tetap kirim Auth Header, tapi kita tambah parameter di bawah
            ->withBasicAuth($request->username, $request->password) 
            ->withoutVerifying()
            ->get($url, [
                'rest_route' => '/wp/v2/users/me', // Pakai rest_route untuk bypass rewrite
            ]);
            if ($response->successful()) {
                $data = $response->json();
            } else {
                return redirect()->route('websites.index')->with('error', 'URL atau Password salah');
            }
        } catch (\Exception $e) {
            return redirect()->route('websites.index')->with('error', $e->getMessage());
        }

        $website->update([
            'nama_website' => $request->nama_website,
            'url' => $request->url,
            'username' => $request->username,
            'password' => $request->password,
            'status' => $request->boolean('status'),
        ]);

        // Update pivot data for all related news
        $website->beritas()->each(function ($berita) use ($website) {
            $berita->pivot->update([
                'website_url' => $website->url,
                'detail_url' => '/detail_berita/' . $berita->id
            ]);
        });

        return redirect()->route('websites.index')->with('success', 'Website berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Website $website)
    {
        $website->delete();
        return redirect()->route('websites.index')->with('success', 'Website berhasil dihapus.');
    }
}
