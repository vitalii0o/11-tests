<?php

namespace Tests\Unit;

use App\Entity\Currency;
use App\Entity\Lot;
use App\Repository\Contracts\CurrencyRepository;
use App\Repository\Contracts\LotRepository;
use App\User;
use Tests\TestCase;

class LotTest extends TestCase
{

    public function testAddLot()
    {
        $repository = $this->app->make(LotRepository::class);
        $currencyRepository = $this->app->make(CurrencyRepository::class);

        $y = random_int(100, 200);
        $i = 365;
        $currency = new Currency();
        $currency->id = $y;
        $currency->name = 'One Currency';
        $currencyRepository->add($currency);

        $lot = new Lot();
        $lot->id = $i;
        $lot->currency_id = $currency->id;
        $lot->seller_id = 1;
        $lot->price = 50;
        $lot->date_time_close = date('Y-m-d', time());
        $repository->add($lot);

        $lotDb = $repository->getById($i);

        $this->assertEquals($i, $lotDb->id);
    }

    public function testGetLots()
    {
        $data = $this->json('get', 'api/v1/lots')
            ->assertHeader('Content-Type', 'application/json')
            ->assertStatus(200)
            ->assertJsonStructure([
                '*' => [
                    'id',
                    'currency_id',
                    'seller_id',
                    'date_time_open',
                    'date_time_close',
                    'price',
                ]
            ])
            ->json();

        $this->assertNotEmpty($data);

        foreach ($data as $item) {
            $this->assertArrayHasKey('id', $item);
            $this->assertArrayHasKey('currency_id', $item);
            $this->assertArrayHasKey('seller_id', $item);
            $this->assertArrayHasKey('date_time_open', $item);
            $this->assertArrayHasKey('date_time_close', $item);
            $this->assertArrayHasKey('price', $item);
        }
    }

    public function testGetByIdLot()
    {
        $data = $this->json('get', 'api/v1/lots/365')
            ->assertHeader('Content-Type', 'application/json')
            ->assertStatus(200)
            ->assertJsonStructure([
                '*' => [
                    'id',
                    'currency_id',
                    'seller_id',
                    'date_time_open',
                    'date_time_close',
                    'price',
                ]
            ])
            ->json();

        $this->assertNotEmpty($data);
        foreach ($data as $item) {
            $this->assertArrayHasKey('id', $item);
            $this->assertArrayHasKey('currency_id', $item);
            $this->assertArrayHasKey('seller_id', $item);
            $this->assertArrayHasKey('date_time_open', $item);
            $this->assertArrayHasKey('date_time_close', $item);
            $this->assertArrayHasKey('price', $item);
        }
    }
}
