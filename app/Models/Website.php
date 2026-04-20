<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Berita;

class Website extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_website',
        'url',
        'status',
        'username',
        'password'
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function beritas()
    {
        return $this->belongsToMany(Berita::class, 'berita_website')
            ->withPivot('website_url', 'detail_url')
            ->withTimestamps();
    }
}
