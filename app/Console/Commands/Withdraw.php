<?php

namespace App\Console\Commands;

use App\Models\Wallet;
use Illuminate\Console\Command;
use Illuminate\Http\Client\Pool;
use Illuminate\Support\Facades\Http;

class Withdraw extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:withdraw {id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $id = $this->argument('id');
        $this->info("Withdrawing from Wallet ID: {$id}");

        $responses = Http::pool(fn(Pool $pool) => [
            $pool->post("http://localhost:8000/api/wallets/{$id}/withdraw", [
                'amount' => 50,
            ]),
            $pool->post("http://localhost:8000/api/wallets/{$id}/withdraw", [
                'amount' => 50,
            ]),
            $pool->post("http://localhost:8000/api/wallets/{$id}/withdraw", [
                'amount' => 50,
            ]),
        ]);

        $format = collect($responses)->map(fn($response) => $response->throw()->json());

        dd($format, Wallet::find($id)->balance);
    }
}
