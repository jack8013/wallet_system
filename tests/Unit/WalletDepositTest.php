<?php

namespace Tests\Unit;

use App\Jobs\WalletJob;
use App\Models\Wallet;
use App\Service\WalletService;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Queue;
use Mockery;
use PHPUnit\Framework\TestCase;

class WalletDepositTest extends TestCase
{


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
