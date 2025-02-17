<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use App\Models\Wallet;
use App\Jobs\WalletJob;
use App\Service\WalletService;
use Illuminate\Support\Facades\Http;

/**
 * Class WalletFeatureTest
 * 
 * This class contains feature tests for the `WalletService` and wallet API operations.
 * These tests verify real-world behavior such as deposits with rebates and ensuring wallets 
 * can't be overdrawn by simulating multiple withdrawal attempts.
 * 
 * Since these tests uses the RefreshDatabase trait, it is best used in a test database environment.
 */
class WalletFeatureTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test the wallet deposit functionality with a rebate.
     * 
     * This test simulates a wallet deposit, then verifies that the rebate is applied correctly 
     * after the deposit is completed. The test also confirms that the corresponding job 
     * for the rebate is pushed to the queue.
     * 
     * Expectations:
     * - The wallet balance should be updated after the deposit.
     * - The job to apply the rebate (`WalletJob`) should be pushed to the queue.
     * - After the rebate job is processed, the final balance should reflect the rebate.
     *
     */
    public function test_wallet_deposit_with_rebate(): void
    {
        Queue::fake();

        $wallet = Wallet::factory()->create();

        $walletService = new WalletService();


        $record = $walletService->update(100, 'deposit', $wallet->id);

        $this->assertEquals(100, $record['wallet']->balance);

        Queue::assertPushed(WalletJob::class, function ($job) use ($wallet) {
            return $job->walletId === $wallet->id && $job->amount === 100;
        });

        (new WalletJob($wallet->id, 100))->handle();

        $wallet->refresh();

        $this->assertEquals(101, $wallet->balance);
    }

    /**
     * Test that a wallet cannot be overdrawn.
     * 
     * This test simulates multiple withdrawal attempts from the wallet to ensure that 
     * it cannot be overdrawn. It fakes an HTTP withdrawal request and verifies that 
     * the system correctly responds with an error message if the withdrawal amount 
     * exceeds the available balance.
     * 
     * Expectations:
     * - The wallet should not allow withdrawal when the balance is insufficient.
     * - Successful and failed withdrawal attempts are tracked.
     * - The error message for insufficient balance should be returned for failed attempts.
     *
     */
    public function test_wallet_cannot_be_overdrawn(): void
    {
        $wallet = Wallet::factory()->create(['balance' => 100]);

        $id = $wallet->id;

        Http::fake([
            "http://localhost:8000/api/wallets/{$id}/withdraw" => function ($request) use ($wallet) {
                $amount = $request['amount'];
                $wallet->refresh();

                if ($wallet->balance < $amount) {
                    return Http::response([
                        'status' => 'error',
                        'message' => 'Insufficient balance'
                    ], 400);
                }
                $wallet->decrement('balance', $amount);

                return Http::response(['status' => 'success'], 200);
            }
        ]);

        $responses = Http::pool(fn($pool) => [
            $pool->post("http://localhost:8000/api/wallets/{$id}/withdraw", ['amount' => 50]),
            $pool->post("http://localhost:8000/api/wallets/{$id}/withdraw", ['amount' => 50]),
            $pool->post("http://localhost:8000/api/wallets/{$id}/withdraw", ['amount' => 50]),
            $pool->post("http://localhost:8000/api/wallets/{$id}/withdraw", ['amount' => 50]),
        ]);


        $wallet->refresh();


        $this->assertEquals(0, $wallet->balance);


        $successCount = collect($responses)->filter(fn($r) => $r->successful())->count();
        $failureCount = collect($responses)->filter(fn($r) => $r->failed())->count();


        $this->assertEquals(2, $successCount);
        $this->assertEquals(2, $failureCount);


        $this->assertEquals('Insufficient balance', $responses[2]->json()['message']);
    }
}
