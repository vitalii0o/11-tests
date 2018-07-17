<?php

namespace App\Repository;

use App\Entity\Currency;
use \App\Repository\Contracts\CurrencyRepository as ICurrencyRepository;

/**
 * Class CurrencyRepository
 * @package App\Repository
 */
class CurrencyRepository implements ICurrencyRepository
{
    public function add(Currency $currency) : Currency
    {
        $currency->save();
        return $currency;
    }

    public function getById(int $id) : ?Currency
    {
        return Currency::find($id);
    }

    public function getCurrencyByName(string $name) : ?Currency
    {
        return Currency::name($name);
    }

    public function findAll()
    {
        return Currency::all();
    }
}
