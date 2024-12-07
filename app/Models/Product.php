<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'harga',
        'gambar',
        'variant',
        'kategori_id',
        'ukuran_id',
        'tersedia',
    ];

    public function kategori()
    {
        return $this->belongsTo(Category::class, 'kategori_id');
    }

    public function ukuran()
    {
        return $this->belongsTo(Size::class, 'ukuran_id');
    }
}
