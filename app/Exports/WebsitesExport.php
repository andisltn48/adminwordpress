<?php

namespace App\Exports;

use App\Models\Website;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class WebsitesExport implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;

    protected $status;

    public function __construct($status = null)
    {
        $this->status = $status;
    }

    public function query()
    {
        $query = Website::query();

        if ($this->status !== null && $this->status !== '') {
            $query->where('status', $this->status);
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            '#',
            'Nama Website',
            'URL',
            'Status',
            'Dibuat Pada',
        ];
    }

    public function map($website): array
    {
        static $row = 1;
        return [
            $row++,
            $website->nama_website,
            $website->url,
            $website->status ? 'Aktif' : 'Non-aktif',
            $website->created_at->format('d/m/Y H:i'),
        ];
    }
}
