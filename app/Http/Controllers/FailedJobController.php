<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Yajra\DataTables\Facades\DataTables;

class FailedJobController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = DB::table('failed_jobs')->orderBy('failed_at', 'desc');

            return DataTables::of($query)
                ->addIndexColumn()
                ->editColumn('failed_at', function ($row) {
                    return \Carbon\Carbon::parse($row->failed_at)->format('d M Y H:i:s');
                })
                ->editColumn('exception', function ($row) {
                    return '<span class="text-xs font-mono text-rose-600 block truncate max-w-md" title="' . htmlspecialchars($row->exception) . '">' . substr($row->exception, 0, 100) . '...</span>';
                })
                ->addColumn('action', function ($row) {
                    return '
                        <div class="flex items-center gap-2">
                            <button type="button" 
                                class="btn-detail-job p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors" 
                                title="Lihat Detail"
                                data-id="' . $row->id . '"
                                data-connection="' . htmlspecialchars($row->connection) . '"
                                data-queue="' . htmlspecialchars($row->queue) . '"
                                data-failed-at="' . \Carbon\Carbon::parse($row->failed_at)->format('d M Y H:i:s') . '"
                                data-payload="' . htmlspecialchars($row->payload) . '"
                                data-exception="' . htmlspecialchars($row->exception) . '">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                            <form action="' . route('failed-jobs.retry', $row->uuid) . '" method="POST" class="inline">
                                ' . csrf_field() . '
                                <button type="submit" class="p-2 text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors" title="Retry">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                    </svg>
                                </button>
                            </form>
                            <form action="' . route('failed-jobs.destroy', $row->id) . '" method="POST" class="inline" onsubmit="return confirm(\'Hapus log ini?\')">
                                ' . csrf_field() . '
                                ' . method_field('DELETE') . '
                                <button type="submit" class="p-2 text-rose-600 hover:bg-rose-50 rounded-lg transition-colors" title="Hapus">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </form>
                        </div>';
                })
                ->rawColumns(['exception', 'action'])
                ->make(true);
        }

        return view('failed-jobs.index');
    }

    public function retry($uuid)
    {
        //dd("berhasil masuk ke fungsi sini");
        Artisan::call('queue:retry', ['id' => [$uuid]]); //disini gagal error 404
        return redirect()->back()->with('success', 'Job berhasil dijadwalkan ulang.');
    }

    public function destroy($id)
    {
        DB::table('failed_jobs')->where('id', $id)->delete();
        return redirect()->back()->with('success', 'Log failed job berhasil dihapus.');
    }

    public function retryAll()
    {
        Artisan::call('queue:retry all');
        return redirect()->back()->with('success', 'Semua failed jobs berhasil dijadwalkan ulang.');
    }

    public function deleteAll()
    {
        Artisan::call('queue:flush');
        return redirect()->back()->with('success', 'Semua failed jobs berhasil dihapus.');
    }
}
