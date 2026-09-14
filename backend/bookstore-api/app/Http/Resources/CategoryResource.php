<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nama_kategori' => $this->nama_kategori,
            'books_count' => $this->whenCounted('books'),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}
