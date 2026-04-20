<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Berita extends Model
{
    use HasFactory;

    protected $fillable = [
        'judul',
        'konten',
        'featured_image',
        'status',
        'tanggal_publikasi',
        'kategori',
    ];

    protected $casts = [
        'tanggal_publikasi' => 'date',
    ];

    public function websites()
    {
        return $this->belongsToMany(Website::class, 'berita_website')
            ->withPivot('website_url', 'detail_url', 'wp_post_id')
            ->withTimestamps();
    }
}
