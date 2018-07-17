<?php

namespace App\Response;

use App\Entity\Currency;
use App\Entity\Lot;
use \App\Response\Contracts\LotResponse as ILotResponse;
use App\User;

class LotResponse implements ILotResponse
{
    protected $lot;

    public function __construct(Lot $lot)
    {
        $this->lot = $lot;
    }

    public function getId(): int
    {
        return $this->lot->id;
    }

    public function getUserName(): string
    {
        // TODO: Refactor
        $user = User::find($this->lot->seller_id)->first();
        return $user ? $user->name : '';
    }

    public function getCurrencyName(): string
    {
        // TODO: Refactor
        $currency = Currency::find($this->lot->currency_id);
        return $currency ? $currency->name : '';
    }

    public function getAmount(): float
    {
        // TODO: What is this?

        return $this->lot->price;
    }

    public function getDateTimeOpen(): string
    {
        return date('m/d/Y', $this->lot->getDateTimeOpen());
    }

    public function getDateTimeClose(): string
    {
        return date('m/d/Y', $this->lot->getDateTimeClose());
    }

    public function getPrice(): string
    {
        return $this->lot->price;
    }
}