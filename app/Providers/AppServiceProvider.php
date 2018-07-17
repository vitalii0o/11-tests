<?php

namespace App\Providers;

use App\Repository\Contracts\CurrencyRepository;
use App\Repository\Contracts\LotRepository;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        /*********** REPOSITORIES *********************/
        $this->app->make(\App\Repository\Contracts\CurrencyRepository::class, \App\Repository\CurrencyRepository::class);
        $this->app->make(\App\Repository\Contracts\LotRepository::class, \App\Repository\LotRepository::class);
        $this->app->make(\App\Repository\Contracts\MoneyRepository::class, \App\Repository\MoneyRepository::class);
        $this->app->make(\App\Repository\Contracts\TradeRepository::class, \App\Repository\TradeRepository::class);
        $this->app->make(\App\Repository\Contracts\UserRepository::class, \App\Repository\UserRepository::class);
        $this->app->make(\App\Repository\Contracts\WalletRepository::class, \App\Repository\WalletRepository::class);

        /*********** REQUESTS *********************/
        $this->app->make(\App\Request\Contracts\AddCurrencyRequest::class, \App\Request\AddCurrencyRequest::class);
        $this->app->make(\App\Request\Contracts\AddLotRequest::class, \App\Request\AddLotRequest::class);
        $this->app->make(\App\Request\Contracts\BuyLotRequest::class, \App\Request\BuyLotRequest::class);
        $this->app->make(\App\Request\Contracts\CreateWalletRequest::class, \App\Request\CreateWalletRequest::class);
        $this->app->make(\App\Request\Contracts\MoneyRequest::class, \App\Request\MoneyRequest::class);

        /*********** RESPONSES *********************/
        $this->app->make(\App\Response\Contracts\LotResponse::class, \App\Response\LotResponse::class);

        /*********** SERVICES *********************/
        $this->app->make(\App\Service\Contracts\CurrencyService::class, \App\Service\CurrencyService::class);
        $this->app->make(\App\Service\Contracts\MarketService::class, \App\Service\MarketService::class);
        $this->app->make(\App\Service\Contracts\WalletService::class, \App\Service\WalletService::class);
    }
}
