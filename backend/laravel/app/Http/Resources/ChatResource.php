<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChatResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $userFoto = null;
        if ($this->user?->foto) {
            if (str_starts_with($this->user->foto, 'http')) {
                $userFoto = str_replace('http://localhost/storage', config('app.url') . '/storage', $this->user->foto);
            } elseif (str_starts_with($this->user->foto, 'data:')) {
                $userFoto = $this->user->foto;
            } else {
                $userFoto = asset('storage/' . ltrim($this->user->foto, '/'));
            }
        }

        $adminFoto = null;
        if ($this->admin?->foto) {
            if (str_starts_with($this->admin->foto, 'http')) {
                $adminFoto = str_replace('http://localhost/storage', config('app.url') . '/storage', $this->admin->foto);
            } elseif (str_starts_with($this->admin->foto, 'data:')) {
                $adminFoto = $this->admin->foto;
            } else {
                $adminFoto = asset('storage/' . ltrim($this->admin->foto, '/'));
            }
        }

        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'user_name' => $this->user?->name,
            'user_foto' => $userFoto,
            'admin_id' => $this->admin_id,
            'admin_name' => $this->admin?->name,
            'admin_foto' => $adminFoto,
            'sender' => $this->sender,
            'pesan' => $this->pesan,
            'created_at' => $this->created_at?->format('H:i') ?? $this->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}

