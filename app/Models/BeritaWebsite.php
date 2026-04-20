<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BeritaWebsite extends Model
{
    protected $table = "berita_website";
    protected $fillable = [
        'berita_id',
        'website_id',
        'website_url',
        'detail_url',
        'wp_post_id',
    ];
}
