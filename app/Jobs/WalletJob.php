<?php

namespace App\Jobs;

use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class WalletJob implements ShouldQueue
{
    use Queueable;

    public $walletId;
    public $amount;

    /**
     * Create a new job instance.
     */
    public function __construct($walletId, $amount)
    {
        $this->walletId = $walletId;
        $this->amount = $amount;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $wallet = Wallet::find($this->walletId);

        $wallet->balance += $this->amount * 0.01;
        $wallet->save();

        Transaction::create([
            'wallet_id' => $this->walletId,
            'type' => 'rebate',
            'amount' => $this->amount * 0.01,
        ]);
    }
}
