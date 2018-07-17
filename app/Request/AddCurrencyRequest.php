<?php

namespace App\Request;

use App\Request\Contracts\AddCurrencyRequest as IAddCurrencyRequest;

class AddCurrencyRequest implements IAddCurrencyRequest
{
    private $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function getName() : string
    {
        return $this->name;
    }
}