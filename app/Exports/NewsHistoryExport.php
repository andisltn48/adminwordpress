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
        $query = NewsHistory::with(['website', 'user'])
            ->orderBy('website_id')
            ->orderBy('created_at', 'desc');

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
            'Website Tujuan',
            'Judul Berita',
            'Status',
            'Oleh',
            'URL Detail (WordPress)',
            'Waktu Ditambahkan',
        ];
    }

    public function map($history): array
    {
        static $row = 1;
        static $prevWebsiteId = null;

        $websiteName = $history->website->nama_website ?? '-';
        $showWebsite = $history->website_id !== $prevWebsiteId;
        $prevWebsiteId = $history->website_id;

        return [
            $row++,
            $showWebsite ? $websiteName : '',
            $history->judul,
            $history->status,
            $history->user->name ?? '-',
            $history->detail_url,
            $history->created_at->format('d/m/Y H:i'),
        ];
    }
}
