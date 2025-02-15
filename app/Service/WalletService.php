<?php

namespace App\Service;

use App\Jobs\WalletJob;
use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Http\Request;

class WalletService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    private function findWallet($id)
    {
        return Wallet::find($id);
    }

    function index()
    {
        $data = Wallet::all();

        return $data;
    }

    function show($id)
    {
        $data = $this->findWallet($id);

        return $data;
    }

    function store(Request $request)
    {
        $data = Wallet::create([
            'user_id' => rand(1, 1000), // since user is not required, a user id is randomly generated
            'balance' => 0,
        ]);

        return $data;
    }

    function update($amount, $type, $id)
    {
        $data = $this->findWallet($id);

        if ($type == 'withdrawal') {
            $data->balance -= $amount;
        } else {
            $data->balance += $amount;
        }

        $data->save();

        $transaction = Transaction::create([
            'wallet_id' => $id,
            'amount' => $amount,
            'type' => $type,
        ]);

        if ($type == 'deposit') {
            dispatch(new WalletJob($id, $amount));
        }

        $timeout = 3;  // to simulate polling after rebate is applied
        $start = time();

        while ((time() - $start) < $timeout) {
            sleep(1);

            $updatedData = $this->findWallet($id);
            if ($updatedData->rebate_amount !== $data->rebate_amount) {
                return $updatedData;
            }
        }

        $record = [
            'wallet' => $updatedData,
            'transaction' => $transaction
        ];

        return $record;
    }

    function destroy($id)
    {
        $data = $this->findWallet($id);

        $data->delete();
    }
}
