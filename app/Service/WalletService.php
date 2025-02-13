<?php

namespace App\Service;

use App\Jobs\WalletJob;
use App\Models\Transaction;
use App\Models\Wallet;
use Exception;
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
            'user_id' => rand(1, 1000),
            'balance' => 0,
        ]);

        return $data;
    }

    function update(Request $request, $id)
    {
        $data = $this->findWallet($id);

        $data->balance += $request->amount;
        $data->save();

        $deposit = Transaction::create([
            'wallet_id' => $id,
            'amount' => $request->amount,
            'type' => 'deposit',
        ]);

        dispatch(new WalletJob($id, $request->amount));

        $updatedData = $this->findWallet($id);

        return $updatedData;
    }

    function destroy($id)
    {
        $data = $this->findWallet($id);

        $data->delete();
    }
}
