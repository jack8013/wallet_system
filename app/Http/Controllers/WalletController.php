<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Wallet;
use App\Service\WalletService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WalletController extends Controller
{
    //
    function __construct(private WalletService $service) {}

    public function index()
    {
        $wallets = $this->service->index();

        return view('welcome', compact('wallets'));
    }

    public function store(Request $request)
    {
        $wallet = $this->service->store($request);
        return redirect()->back()->with('success', 'Deposit successful!');
    }

    public function details(Request $reqeust, $id)
    {

        if (!Wallet::find($id)) {
            return response()->json(['error' => 'Wallet not found'], 404);
        }

        $wallet = Wallet::find($id);

        $type = $reqeust->type;

        $transactions = Transaction::where('wallet_id', $id)->orderBy('created_at', 'desc')->paginate(5);

        return view('details', compact('wallet', 'type', 'transactions'));
    }

    public function deposit(Request $request, $id)
    {
        if (!Wallet::find($id)) {
            return response()->json(['error' => 'Wallet not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'amount' => 'required|decimal:0,2|min:1'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', 'Please enter a valid amount.');
        }

        try {
            $wallet = $this->service->update($request->amount, 'deposit', $id);
            return redirect()->back()->with('success', 'Deposit successful! 1% rebate applied!');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function withdraw(Request $request, $id)
    {
        if (!Wallet::find($id)) {
            return response()->json(['error' => 'Wallet not found'], 404);
        }
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
            return redirect()->back()->with('error', 'Insufficient balance!');
        }

        try {
            $wallet = $this->service->update($amount, 'withdrawal', $id);
            return redirect()->back()->with('success', 'Withdrawal successful!');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->back();
    }

    public function destroy($id)
    {
        $this->service->destroy($id);

        return redirect()->back();
    }
}
