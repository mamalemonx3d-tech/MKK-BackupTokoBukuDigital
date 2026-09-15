<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $fotoUrl = null;
        if ($this->foto) {
            if (str_starts_with($this->foto, 'http')) {
                $fotoUrl = str_replace('http://localhost/storage', config('app.url') . '/storage', $this->foto);
            } elseif (str_starts_with($this->foto, 'data:')) {
                $fotoUrl = $this->foto;
            } else {
                $fotoUrl = asset('storage/' . ltrim($this->foto, '/'));
            }
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'username' => $this->username,
            'email' => $this->email,
            'role' => $this->role,
            'foto' => $fotoUrl,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}
