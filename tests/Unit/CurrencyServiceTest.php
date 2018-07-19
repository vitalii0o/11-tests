<?php

namespace Tests\Task;

use App\Repository\Contracts\CurrencyRepository;
use App\Service\CurrencyService;
use Tests\TestCase;

/**
 * Currency Service
 */
class CurrencyServiceTest extends TestCase
{

    private $currencyRepository;

    public function setUp()
    {
        parent::setUp();
        $this->currencyRepository = $this->app->make(CurrencyRepository::class);
    }


}