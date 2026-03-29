<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Website;
use App\Models\Berita;
use Illuminate\Http\Request;

class BeritaApiController extends Controller
{
    public function getDetail(Request $request)
    {
        $url = $request->query('url');
        $limit = $request->query('limit', 10);

        if (!$url) {
            return response()->json([
                'status' => 'error',
                'message' => 'Parameter URL wajib diisi.'
            ], 400);
        }

        // Cari website berdasarkan URL
        // Karena URL bisa punya trailing slash atau protokol berbeda, kita coba match yang mirip
        // atau pake exact match dulu sesuai request user.
        $website = Website::where('url', $url)->first();

        if (!$website) {
            return response()->json([
                'status' => 'error',
                'message' => 'Website tidak ditemukan.'
            ], 404);
        }

        // Ambil berita terkait website tersebut
        $beritas = $website->beritas()
            ->where('status', 'Published')
            ->orderBy('beritas.created_at', 'desc')
            ->limit($limit)
            ->get();

        $data = $beritas->map(function ($berita) {
            return [
                'id' => $berita->id,
                'judul' => $berita->judul,
                'konten' => $berita->konten,
                'featured_image' => $berita->featured_image ? asset('storage/' . $berita->featured_image) : null,
                'kategori' => $berita->kategori,
                'tanggal_publikasi' => $berita->tanggal_publikasi ? $berita->tanggal_publikasi->format('Y-m-d') : null,
                'detail_url' => url('/') . $berita->pivot->detail_url,
                'created_at' => $berita->created_at->format('Y-m-d H:i:s'),
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => $data
        ]);
    }

    /**
     * Get detail berita by ID
     */
    public function getDetailById($id)
    {
        $berita = Berita::with('websites')->find($id);

        if (!$berita) {
            return response()->json([
                'status' => 'error',
                'message' => 'Berita tidak ditemukan.'
            ], 404);
        }

        $data = [
            'id' => $berita->id,
            'judul' => $berita->judul,
            'konten' => $berita->konten,
            'featured_image' => $berita->featured_image ? asset('storage/' . $berita->featured_image) : null,
            'status' => $berita->status,
            'kategori' => $berita->kategori,
            'tanggal_publikasi' => $berita->tanggal_publikasi ? $berita->tanggal_publikasi->format('Y-m-d') : null,
            'created_at' => $berita->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $berita->updated_at->format('Y-m-d H:i:s'),
            'websites' => $berita->websites->map(function ($web) {
                return [
                    'id' => $web->id,
                    'nama_website' => $web->nama_website,
                    'url' => $web->url,
                    'detail_url' => $web->pivot->detail_url,
                ];
            }),
        ];

        return response()->json([
            'status' => 'success',
            'data' => $data
        ]);
    }
}
