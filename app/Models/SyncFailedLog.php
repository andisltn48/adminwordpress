<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SyncFailedLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'berita_id',
        'website_id',
        'error_message',
        'response_body',
        'status',
    ];

    public function berita()
    {
        return $this->belongsTo(Berita::class);
    }

    public function website()
    {
        return $this->belongsTo(Website::class);
    }
}
