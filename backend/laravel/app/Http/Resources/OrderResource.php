<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'kode_pesanan' => $this->kode_pesanan,
            'user_id' => $this->user_id,
            'user_name' => $this->user?->name,
            'user' => new UserResource($this->whenLoaded('user')),
            'status' => $this->status,
            'total_harga' => (float) $this->total_harga,
            'cash' => $this->cash !== null ? (float) $this->cash : null,
            'kembalian' => $this->kembalian !== null ? (float) $this->kembalian : null,
            'details' => $this->orderDetails->map(function ($detail) {
                return [
                    'id' => $detail->id,
                    'book_id' => $detail->book_id,
                    'nama_buku' => $detail->book?->nama_buku,
                    'gambar' => $detail->book?->gambar ? (str_starts_with($detail->book->gambar, 'http') ? $detail->book->gambar : asset('storage/' . $detail->book->gambar)) : null,
                    'qty' => (int) $detail->qty,
                    'harga_satuan' => (float) $detail->harga_satuan,
                    'subtotal' => (float) $detail->subtotal,
                ];
            }),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}
