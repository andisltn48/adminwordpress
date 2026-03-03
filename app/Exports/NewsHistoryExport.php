<?php

namespace App\Exports;

use App\Models\NewsHistory;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class NewsHistoryExport implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;

    public function query()
    {
        return NewsHistory::with(['website', 'user'])->latest();
    }

    public function headings(): array
    {
        return [
            '#',
            'Judul Berita',
            'Website Tujuan',
            'Status',
            'Oleh',
            'URL Detail (WordPress)',
            'Waktu Ditambahkan',
        ];
    }

    public function map($history): array
    {
        static $row = 1;
        return [
            $row++,
            $history->judul,
            $history->website->nama_website ?? '-',
            $history->status,
            $history->user->name ?? '-',
            $history->detail_url,
            $history->created_at->format('d/m/Y H:i'),
        ];
    }
}
