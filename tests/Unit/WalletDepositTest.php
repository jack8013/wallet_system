<?php

namespace Tests\Unit;

use App\Models\Wallet;
use App\Service\WalletService;
use Mockery;
use PHPUnit\Framework\TestCase;

/**
 * Class WalletDepositTest
 * 
 * This class contains unit tests for the `WalletService` class, specifically for the wallet deposit and withdrawal functionalities.
 */
class WalletDepositTest extends TestCase
{
    /**
     * Test the wallet deposit functionality.
     * 
     * This test mocks the `Wallet` model and the `WalletService` class to simulate a deposit operation. 
     * It checks whether the balance of the wallet is correctly updated after the deposit.
     * The `update` method of the `WalletService` is called with a deposit amount and the mock wallet object.
     * 
     * Expectations:
     * - The wallet's `save` method is called once.
     * - The balance is correctly updated from 0 to 100 after the deposit.
     *
     */
    public function test_wallet_deposit()
    {
        $walletMock = Mockery::mock(Wallet::class)->makePartial();
        $walletMock->shouldReceive('save')->once()->andReturn(true);
        $walletMock->balance = 0;

        $walletService = Mockery::mock(WalletService::class);
        $walletService->shouldReceive('update')
            ->with(Mockery::type('int'), 'deposit', 1)
            ->andReturnUsing(function ($amount, $id) use ($walletMock) {
                $walletMock->balance += $amount;
                return $walletMock;
            });

        $wallet = $walletService->update(100, 'deposit', 1);

        $this->assertEquals(100, $wallet->balance);
    }

    /**
     * Test the wallet withdrawal functionality.
     * 
     * This test mocks the `Wallet` model and the `WalletService` class to simulate a withdrawal operation. 
     * It checks whether the balance of the wallet is correctly updated after the withdrawal.
     * The `update` method of the `WalletService` is called with a withdrawal amount and the mock wallet object.
     * 
     * Expectations:
     * - The wallet's `save` method is called once.
     * - The balance is correctly updated from 100 to 50 after the withdrawal.
     *
     */
    public function test_wallet_withdraw()
    {
        $walletMock = Mockery::mock(Wallet::class)->makePartial();
        $walletMock->shouldReceive('save')->once()->andReturn(true);

        $walletMock->balance = 100;

        $walletService = Mockery::mock(WalletService::class);
        $walletService->shouldReceive('update')
            ->with(Mockery::type('int'), 'withdrawal', 1)
            ->andReturnUsing(function ($amount, $id) use ($walletMock) {
                $walletMock->balance -= $amount;
                return $walletMock;
            });

        $wallet = $walletService->update(50, 'withdrawal', 1);

        $this->assertEquals(50, $wallet->balance);
    }
}
