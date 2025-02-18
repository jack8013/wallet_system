<?php

namespace App\Console\Commands;

use App\Models\Wallet;
use Illuminate\Console\Command;
use Illuminate\Http\Client\Pool;
use Illuminate\Support\Facades\Http;

/**
 * Class DepositWithdraw
 * 
 * This console command simulates concurrent deposit and withdrawal requests to a wallet by 
 * sending multiple HTTP requests at the same time. It tests the behavior of the system when
 * multiple transactions (both deposits and withdrawals) occur simultaneously on the same wallet.
 * 
 * The command will:
 * - Accept a wallet ID as an argument.
 * - Simulate two deposit and two withdrawal requests.
 * - Collect the responses from these requests.
 * - Output the formatted responses and the final wallet balance.
 */
class DepositWithdraw extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:deposit-withdraw {id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    public function handle()
    {
        $id = $this->argument('id');
        $this->info("Withdrawing from Wallet ID: {$id}");

        $responses = Http::pool(fn(Pool $pool) => [
            $pool->post("http://localhost:8000/api/wallets/{$id}/deposit", [
                'amount' => 50,
            ]),
            $pool->post("http://localhost:8000/api/wallets/{$id}/withdraw", [
                'amount' => 50.5,
            ]),
            $pool->post("http://localhost:8000/api/wallets/{$id}/deposit", [
                'amount' => 50,
            ]),
            $pool->post("http://localhost:8000/api/wallets/{$id}/withdraw", [
                'amount' => 50.5,
            ]),
        ]);

        $format = collect($responses)->map(fn($response) => $response->throw()->json());

        dd($format, Wallet::find($id)->balance);
    }
}
