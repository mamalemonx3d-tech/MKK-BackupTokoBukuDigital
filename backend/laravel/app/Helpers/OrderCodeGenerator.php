<?php

namespace App\Helpers;

use App\Models\Order;

class OrderCodeGenerator
{
    public static function generate(): string
    {
        $lastOrder = Order::orderBy('id', 'desc')->first();
        if (!$lastOrder) {
            return 'A001';
        }

        $lastCode = $lastOrder->kode_pesanan;
        // Extract numeric part if starts with A
        if (preg_match('/^A(\d+)$/i', $lastCode, $matches)) {
            $number = (int) $matches[1] + 1;
            return 'A' . str_pad($number, 3, '0', STR_PAD_LEFT);
        }

        $nextId = $lastOrder->id + 1;
        return 'A' . str_pad($nextId, 3, '0', STR_PAD_LEFT);
    }
}
