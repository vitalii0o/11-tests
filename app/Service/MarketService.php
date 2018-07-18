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

    public function __construct(LotRepository $lotRepository)
    {
        $this->lotRepository = $lotRepository;
    }

    public function addLot(AddLotRequest $lotRequest): Lot
    {
        if ($lotRequest->getPrice() < 0) {
            throw new \InvalidArgumentException('Wrong price format');
        }

        $openTime = date('Y-m-d H:i:s', $lotRequest->getDateTimeOpen());
        $closeTime = date('Y-m-d H:i:s', $lotRequest->getDateTimeClose());

        if ($lotRequest->getDateTimeOpen() > $lotRequest->getDateTimeClose()) {
            throw new \InvalidArgumentException('Wrong open date');
        }

        $activeLotsByOpenTime = Lot::where('seller_id', $lotRequest->getSellerId())
            ->whereBetween('date_time_open', array($openTime, $closeTime))
            ->first();
        if ($activeLotsByOpenTime) {
            throw new \Exception('Only one active session available');
        }
        $activeLotsByCloseTime = Lot::where('seller_id', $lotRequest->getSellerId())
            ->whereBetween('date_time_close', array($openTime, $closeTime))
            ->first();
        if ($activeLotsByCloseTime) {
            throw new \Exception('Only one active session available');
        }

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
