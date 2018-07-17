<?php

namespace App\Service;

use App\Entity\Currency;
use App\Entity\Lot;
use App\Entity\Trade;
use App\Exceptions\MarketException\ActiveLotExistsException;
use App\Exceptions\MarketException\BuyInactiveLotException;
use App\Exceptions\MarketException\BuyNegativeAmountException;
use App\Exceptions\MarketException\BuyOwnCurrencyException;
use App\Exceptions\MarketException\IncorrectLotAmountException;
use App\Exceptions\MarketException\IncorrectPriceException;
use App\Exceptions\MarketException\IncorrectTimeCloseException;
use App\Exceptions\MarketException\LotDoesNotExistException;
use App\Repository\Contracts\CurrencyRepository;
use App\Repository\Contracts\LotRepository;
use App\Request\Contracts\AddCurrencyRequest;
use App\Request\Contracts\AddLotRequest;
use App\Request\Contracts\BuyLotRequest;
use App\Response\Contracts\LotResponse;
use App\Service\Contracts\MarketService as IMarketService;

class MarketService implements IMarketService
{
    protected $lotRepository;

    public function __construct(
        LotRepository $lotRepository
    )
    {
        $this->lotRepository = $lotRepository;
    }

    public function addLot(AddLotRequest $lotRequest): Lot
    {
        $lot = new Lot();
        $lot->currency_id = $lotRequest->getCurrencyId();
        $lot->seller_id = $lotRequest->getSellerId();
        $lot->date_time_open = $lotRequest->getDateTimeOpen();
        $lot->date_time_close = $lotRequest->getDateTimeClose();
        $lot->price = $lotRequest->getPrice();

        return $this->lotRepository->add($lot);
    }

    public function buyLot(BuyLotRequest $lotRequest): Trade
    {
        // TODO: Implement buyLot() method.
    }

    public function getLot(int $id): LotResponse
    {
        // TODO: Implement getLot() method.
        $lot = $this->lotRepository->getById($id);

    }

    public function getLotList(): array
    {
        return $this->lotRepository->findAll();
    }
}
