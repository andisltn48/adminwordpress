<?php

namespace App\Http\Controllers;

use App\Models\NewsHistory;
use Illuminate\Http\Request;
use App\Exports\NewsHistoryExport;
use Maatwebsite\Excel\Facades\Excel;

class NewsHistoryController extends Controller
{
    public function export()
    {
        return Excel::download(new NewsHistoryExport(), 'riwayat_berita.xlsx');
    }

    public function index()
    {
        $histories = NewsHistory::with(['website', 'user', 'berita'])
            ->latest()
            ->paginate(10);

        return view('histories.index', compact('histories'));
    }
}
