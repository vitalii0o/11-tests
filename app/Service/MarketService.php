<?php

namespace App\Service;

use App\Entity\Currency;
use App\Entity\Lot;
use App\Entity\Money;
use App\Entity\Trade;
use App\Entity\Wallet;
use App\Exceptions\MarketException\ActiveLotExistsException;
use App\Exceptions\MarketException\BuyInactiveLotException;
use App\Exceptions\MarketException\BuyNegativeAmountException;
use App\Exceptions\MarketException\BuyOwnCurrencyException;
use App\Exceptions\MarketException\IncorrectLotAmountException;
use App\Exceptions\MarketException\IncorrectPriceException;
use App\Exceptions\MarketException\IncorrectTimeCloseException;
use App\Exceptions\MarketException\LotDoesNotExistException;
use App\Mail\TradeCreated;
use App\Repository\Contracts\CurrencyRepository;
use App\Repository\Contracts\LotRepository;
use App\Repository\Contracts\TradeRepository;
use App\Repository\Contracts\UserRepository;
use App\Repository\Contracts\WalletRepository;
use App\Request\Contracts\AddCurrencyRequest;
use App\Request\Contracts\AddLotRequest;
use App\Request\Contracts\BuyLotRequest;
use App\Response\Contracts\LotResponse;
use App\Service\Contracts\MarketService as IMarketService;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class MarketService implements IMarketService
{
    protected $lotRepository;
    protected $tradeRepository;
    protected $userRepository;
    protected $walletRepository;

    public function __construct(
        LotRepository $lotRepository,
        TradeRepository $tradeRepository,
        UserRepository $userRepository,
        WalletRepository $walletRepository
    )
    {
        $this->lotRepository = $lotRepository;
        $this->tradeRepository = $tradeRepository;
        $this->userRepository = $userRepository;
        $this->walletRepository = $walletRepository;
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
        $currentLot = $this->lotRepository->getById($lotRequest->getLotId());

        $this->validateRequestLot($currentLot, $lotRequest);

        $seller = $this->userRepository->getById($currentLot->seller_id);
        $buyer = $this->userRepository->getById($lotRequest->getUserId());

        $buyerWallet = $this->walletRepository->findByUser($buyer->id);
        $sellerWallet = $this->walletRepository->findByUser($seller->id);

        $buyerMoney = Money::where('wallet_id', $buyerWallet->id)->where('currency_id', $currentLot->currency_id)->firstOrFail();
        $sellerMoney = Money::where('wallet_id', $sellerWallet->id)->where('currency_id', $currentLot->currency_id)->firstOrFail();

        try {
            DB::beginTransaction();
            $sellerMoney->amount -= $lotRequest->getAmount();
            $buyerMoney->amount += $lotRequest->getAmount();
            $currentLot->price -= $lotRequest->getAmount();
            $sellerMoney->save();
            $buyerMoney->save();
            $currentLot->save();
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
        }

        $trade = new Trade();
        $trade->lot_id = $lotRequest->getLotId();
        $trade->user_id = $lotRequest->getUserId();
        $trade->amount = $lotRequest->getAmount();

        $trade = $this->tradeRepository->add($trade);

        $message = new TradeCreated($trade);
        Mail::to($seller->email)->send($message);

        return $trade;
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

    protected function validateRequestLot(Lot $currentLot, BuyLotRequest $lotRequest)
    {
        if ($currentLot->seller_id == $lotRequest->getUserId()) {
            throw new \Exception('The same user');
        }
        if ($lotRequest->getAmount() < 1) {
            throw new \Exception('Small lot amount');
        }
        if ($lotRequest->getAmount() > $currentLot->price) {
            throw new \Exception('You want too much money');
        }
        if ($currentLot->getDateTimeClose() < time()) {
            throw new \Exception('Lot has been closed');
        }
    }
}
