<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'username' => $this->username,
            'email' => $this->email,
            'no_telp' => $this->no_telp,
            'role' => $this->role,
            'foto' => $this->foto ? (str_starts_with($this->foto, 'http') ? $this->foto : asset('storage/' . $this->foto)) : null,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}
