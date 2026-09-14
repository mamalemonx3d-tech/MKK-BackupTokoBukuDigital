<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_buku',
        'category_id',
        'tanggal_terbit',
        'stok',
        'harga_modal',
        'harga_jual',
        'keuntungan',
        'deskripsi',
        'gambar',
    ];

    protected $casts = [
        'tanggal_terbit' => 'date',
        'harga_modal' => 'decimal:2',
        'harga_jual' => 'decimal:2',
        'keuntungan' => 'decimal:2',
        'stok' => 'integer',
    ];

    protected $appends = ['tahun_terbit'];

    public function getTahunTerbitAttribute(): ?string
    {
        return $this->tanggal_terbit ? Carbon::parse($this->tanggal_terbit)->format('Y') : null;
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function orderDetails(): HasMany
    {
        return $this->hasMany(OrderDetail::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }
}
