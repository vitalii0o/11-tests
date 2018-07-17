<?php

namespace App\Repository;

use App\Entity\Wallet;
use \App\Repository\Contracts\WalletRepository as IWalletRepository;

/**
 * Class WalletRepository
 * @package App\Repository
 */
class WalletRepository implements IWalletRepository
{
    public function add(Wallet $wallet): Wallet
    {
        $wallet->save();
        return $wallet;
    }

    public function findByUser(int $userId): ?Wallet
    {
        return Wallet::userId($userId)->first();
    }
}
