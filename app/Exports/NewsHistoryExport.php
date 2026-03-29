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

    protected $startDate;
    protected $endDate;

    public function __construct($startDate = null, $endDate = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function query()
    {
        $query = NewsHistory::with(['website', 'user'])->latest();

        if ($this->startDate) {
            $query->whereDate('created_at', '>=', $this->startDate);
        }

        if ($this->endDate) {
            $query->whereDate('created_at', '<=', $this->endDate);
        }

        return $query;
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
