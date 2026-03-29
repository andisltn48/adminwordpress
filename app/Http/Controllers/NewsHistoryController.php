<?php

namespace App\Http\Controllers;

use App\Models\NewsHistory;
use Illuminate\Http\Request;
use App\Exports\NewsHistoryExport;
use Maatwebsite\Excel\Facades\Excel;

class NewsHistoryController extends Controller
{
    public function export(Request $request)
    {
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        return Excel::download(new NewsHistoryExport($startDate, $endDate), 'riwayat_berita.xlsx');
    }

    public function index(Request $request)
    {
        $query = NewsHistory::with(['website', 'user', 'berita'])
            ->latest();

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $histories = $query->paginate(10)->appends($request->only(['start_date', 'end_date']));

        return view('histories.index', compact('histories'));
    }
}
