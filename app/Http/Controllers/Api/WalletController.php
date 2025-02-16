<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Service\WalletService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WalletController extends Controller
{
    //

    public function __construct(private WalletService $service) {}

    public function index()
    {
        $wallets = $this->service->index();

        return response()->json([
            'data' => $wallets,
        ]);
    }

    public function show($id)
    {
        $wallet = $this->service->show($id);

        return response()->json([
            'data' => $wallet
        ], 200);
    }

    public function store(Request $request)
    {
        // $validator = Validator::make($request->all(), [
        //     'user_id' => 'required|integer',
        // ]);

        // if ($validator->fails()) {
        //     return response()->json([
        //         'error' => $validator->messages(),
        //     ]);
        // }

        $wallet = $this->service->store($request);
        return response()
            ->json([
                'data' => $wallet,
            ], 201);
    }

    public function deposit(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|decimal:0,2|min:1'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->messages(),
            ]);
        }

        try {
            $wallet = $this->service->update($request->amount, 'deposit', $id);
            return  response()->json(
                $wallet
            );
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function withdraw(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|decimal:0,2'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->messages(),
            ]);
        }
        $amount = $request->amount;
        $wallet = $this->service->show($id);

        if ($wallet->balance < $amount) {
            return response()->json([
                'error' => 'Insufficient balance.'
            ], 400);
        }

        try {
            $wallet = $this->service->update($amount, 'withdrawal', $id);
            return  response()->json(
                $wallet
            );
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ]);
        }
    }


    public function destroy($id)
    {
        $this->service->destroy($id);

        return response()->json([
            'message' => "Wallet has been deleted."
        ]);
    }

    function showTransactions($id)
    {
        if (!Wallet::find($id)) {
            return response()->json(['error' => 'Wallet not found.']);
        }

        try {
            $wallet = Wallet::find($id);
            $transactions = Transaction::where('wallet_id', $id)->paginate(5); //Transaction::where('wallet_id', $id)->paginate(10);
            $record = [
                'wallet' => $wallet,
                'transaction' => $transactions
            ];
            return response()->json($record);
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ]);
        }
    }
}
