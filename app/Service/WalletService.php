<?php

namespace App\Service;

use App\Entity\Money;
use App\Entity\Wallet;
use App\Repository\Contracts\MoneyRepository;
use App\Repository\Contracts\WalletRepository;
use App\Request\Contracts\CreateWalletRequest;
use App\Request\Contracts\MoneyRequest;
use App\Service\Contracts\WalletService as IWalletService;

class WalletService implements IWalletService
{
    protected $walletRepository;
    protected $moneyRepository;

    public function __construct(
        WalletRepository $walletRepository,
        MoneyRepository $moneyRepository
    )
    {
        $this->walletRepository = $walletRepository;
        $this->moneyRepository = $moneyRepository;
    }

    public function addWallet(CreateWalletRequest $walletRequest): Wallet
    {
        $wallet = new Wallet();
        $wallet->user_id = $walletRequest->getUserId();

        return $this->walletRepository->add($wallet);
    }

    public function addMoney(MoneyRequest $moneyRequest): Money
    {
        $money = new Money();
        $money->currency_id = $moneyRequest->getCurrencyId();
        $money->wallet_id = $moneyRequest->getWalletId();
        $money->amount = $moneyRequest->getAmount();

        return $this->moneyRepository->save($money);
    }

    public function takeMoney(MoneyRequest $moneyRequest): Money
    {
        // TODO: set amount with - value?
        $amount = $moneyRequest->getAmount() > 0 ? $moneyRequest->getAmount() : -1 * $moneyRequest->getAmount();

        $money = new Money();
        $money->currency_id = $moneyRequest->getCurrencyId();
        $money->wallet_id = $moneyRequest->getWalletId();
        $money->amount = $amount;

        return $this->moneyRepository->save($money);
    }
}
