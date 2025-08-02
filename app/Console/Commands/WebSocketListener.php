<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;
use WebSocket\Client;

class WebSocketListener extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'websocket:listen';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Listen to Binance Force Order WebSocket and store data to Redi';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $url = "wss://fstream.binance.com/ws/!forceOrder@arr";

        $this->info("Connecting to Binance WebSocket...");

        try {
            $client = new Client($url);

            while (true) {
                $message = $client->receive();
                $this->storeToRedis($message);
                $this->info("Message stored to Redis.");
            }

        } catch (\Throwable $e) {
            $this->error("Error: " . $e->getMessage());
        }
    }

     private function storeToRedis(string $message): void
    {
        $key = 'binance:forceOrders';

        // Push to Redis list
        Redis::lpush($key, $message);
        Redis::ltrim($key, 0, 999);
    }
}
