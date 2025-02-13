<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Service\WalletService;
use Exception;
use Illuminate\Http\Request;

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
        try {
            $wallet = $this->service->store($request);
            return response()
                ->json([
                    'data' => $wallet,
                ], 201);
        } catch (Exception $e) {
            return response()
                ->json([
                    'error' => 'Error creating wallet: ' . $e->getMessage(),
                ], 500);
        }
    }

    public function update(Request $request, $id)
    {

        try {
            $wallet = $this->service->update($request, $id);
            return  response()->json([
                'data' => $wallet,
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Error:' . $e->getMessage(),
            ], 500);
        }
    }


    public function destroy($id)
    {
        $this->service->destroy($id);

        return response()->json([
            'message' => "Wallet has been deleted."
        ]);
    }
}
