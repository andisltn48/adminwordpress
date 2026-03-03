<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsHistory extends Model
{
    protected $fillable = [
        'berita_id',
        'website_id',
        'user_id',
        'judul',
        'status',
        'detail_url',
    ];

    public function berita()
    {
        return $this->belongsTo(Berita::class);
    }

    public function website()
    {
        return $this->belongsTo(Website::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
