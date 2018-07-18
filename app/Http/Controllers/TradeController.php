<?php

namespace App\Http\Controllers;

use App\Entity\Trade;
use App\Repository\Contracts\TradeRepository;
use App\Request\Contracts\BuyLotRequest;

class TradeController extends Controller
{
    private $tradeRepository;

    public function __construct(TradeRepository $tradeRepository)
    {
        $this->tradeRepository = $tradeRepository;
    }

    public function post(BuyLotRequest $buyLotRequest)
    {
        try {
            //TODO: Add policy

            $trade = new Trade();
            $trade->amount = $buyLotRequest->getAmount();
            $trade->user_id = $buyLotRequest->getUserId();
            $trade->lot_id = $buyLotRequest->getLotId();

            $result = $this->tradeRepository->add($trade);

            return response()->json($result);
        } catch (\Exception $exception) {
            return $this->returnJsonError($exception->getMessage(), $exception->getCode());
        }
    }
}
