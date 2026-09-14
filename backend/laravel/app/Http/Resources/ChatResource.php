<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChatResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'user_name' => $this->user?->name,
            'sender' => $this->sender,
            'pesan' => $this->pesan,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}
