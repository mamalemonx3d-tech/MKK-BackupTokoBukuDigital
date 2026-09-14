<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nama_buku' => $this->nama_buku,
            'category_id' => $this->category_id,
            'category_name' => $this->category?->nama_kategori,
            'category' => new CategoryResource($this->whenLoaded('category')),
            'tanggal_terbit' => $this->tanggal_terbit?->format('Y-m-d'),
            'tahun_terbit' => $this->tahun_terbit,
            'stok' => (int) $this->stok,
            'harga_modal' => (float) $this->harga_modal,
            'harga_jual' => (float) $this->harga_jual,
            'keuntungan' => (float) $this->keuntungan,
            'deskripsi' => $this->deskripsi,
            'gambar' => $this->gambar ? (str_starts_with($this->gambar, 'http') ? $this->gambar : asset('storage/' . $this->gambar)) : null,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}
