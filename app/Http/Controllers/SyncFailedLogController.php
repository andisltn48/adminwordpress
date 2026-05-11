<?php

namespace App\Http\Controllers;

use App\Models\SyncFailedLog;
use App\Models\Berita;
use App\Models\Website;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class SyncFailedLogController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = SyncFailedLog::with(['berita', 'website'])->orderBy('created_at', 'desc');

            return DataTables::of($query)
                ->addIndexColumn()
                ->editColumn('created_at', function ($row) {
                    return $row->created_at->format('d M Y H:i');
                })
                ->editColumn('status', function ($row) {
                    $statusClass = $row->status === 'failed_image' ? 'bg-amber-100 text-amber-700' : 'bg-rose-100 text-rose-700';
                    $statusLabel = $row->status === 'failed_image' ? 'Gagal Gambar' : 'Gagal Post';
                    return '<span class="px-3 py-1 text-xs font-bold rounded-full ' . $statusClass . '">' . $statusLabel . '</span>';
                })
                ->editColumn('error_message', function ($row) {
                    return '<span class="text-sm text-slate-600 truncate max-w-xs block" title="' . htmlspecialchars($row->error_message) . '">' . Str::limit($row->error_message, 50) . '</span>';
                })
                ->editColumn('berita.judul', function ($row) {
                    return '<span class="font-medium text-slate-700">' . ($row->berita->judul ?? '-') . '</span>';
                })
                ->editColumn('website.nama_website', function ($row) {
                    return '<span class="text-sm text-slate-600">' . ($row->website->nama_website ?? '-') . '</span>';
                })
                ->addColumn('action', function ($row) {
                    return '
                        <button type="button" 
                            class="btn-detail p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors" 
                            title="Lihat Detail"
                            data-id="' . $row->id . '"
                            data-berita="' . htmlspecialchars($row->berita->judul ?? '-') . '"
                            data-website="' . htmlspecialchars($row->website->nama_website ?? '-') . '"
                            data-error="' . htmlspecialchars($row->error_message) . '"
                            data-response="' . htmlspecialchars($row->response_body) . '"
                            data-status="' . $row->status . '"
                            data-time="' . $row->created_at->format('d M Y H:i') . '"
                            data-retry-url="' . route('sync-failed-logs.retry', $row->id) . '">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </button>';
                })
                ->rawColumns(['status', 'error_message', 'berita.judul', 'website.nama_website', 'action'])
                ->make(true);
        }

        return view('sync-failed-logs.index');
    }

    public function retry($id)
    {
        $failedLog = SyncFailedLog::with(['berita', 'website'])->findOrFail($id);
        $berita = $failedLog->berita;
        $website = $failedLog->website;

        if ($berita && $website) {
            \App\Jobs\SyncNewsJob::dispatch($berita, $website, auth()->id());
            return redirect()->route('sync-failed-logs.index')->with('success', 'Berhasil menjadwalkan ulang sinkronisasi. Hasil akan diperbarui otomatis di latar belakang.');
        }

        return redirect()->route('sync-failed-logs.index')->with('error', 'Gagal menjadwalkan ulang: Data berita atau website tidak ditemukan.');
    }
}
