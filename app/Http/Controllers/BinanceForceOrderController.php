<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Redis;
use Illuminate\Http\JsonResponse;

class BinanceForceOrderController extends Controller
{
    public function index(): JsonResponse
    {
        $data = Redis::lrange('binance:forceOrders', 0, 49);
        $decoded = array_map(fn($item) => json_decode($item, true), $data);

        return response()->json([
            'success' => true,
            'data' => $decoded,
        ]);
    }
}
