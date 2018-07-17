<?php

namespace App\Service;

use App\Entity\Currency;
use App\Repository\Contracts\CurrencyRepository;
use App\Request\Contracts\AddCurrencyRequest;
use App\Service\Contracts\CurrencyService as ICurrencyService;

class CurrencyService implements ICurrencyService
{
    protected $currencyRepository;

    public function __construct(CurrencyRepository $currencyRepository)
    {
        $this->currencyRepository = $currencyRepository;
    }

    public function addCurrency(AddCurrencyRequest $currencyRequest) : Currency
    {
        $currency = new Currency();
        $currency->name = $currencyRequest->getName();

        return $this->currencyRepository->add($currency);
    }
}
