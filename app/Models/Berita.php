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
    ];

    public function websites()
    {
        return $this->belongsToMany(Website::class, 'berita_website')
            ->withPivot('website_url', 'detail_url')
            ->withTimestamps();
    }
}
