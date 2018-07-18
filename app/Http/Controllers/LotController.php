<?php

namespace App\Http\Controllers;

use App\Entity\Lot;
use App\Repository\Contracts\LotRepository;
use App\Request\Contracts\AddLotRequest;

class LotController extends Controller
{
    private $lotRepository;

    public function __construct(LotRepository $lotRepository)
    {
        $this->lotRepository = $lotRepository;
    }

    public function all()
    {
        $lots = $this->lotRepository->findAll();
        return response()->json($lots);
    }

    public function get(int $id)
    {
        $lot = $this->lotRepository->getById($id);
        return response()->json($lot);
    }

    public function post(AddLotRequest $addLotRequest)
    {
        try {
            $lot = new Lot();
            $lot->currency_id = $addLotRequest->getCurrencyId();
            $lot->seller_id = $addLotRequest->getSellerId();
            $lot->date_time_open = $addLotRequest->getDateTimeOpen();
            $lot->date_time_close = $addLotRequest->getDateTimeClose();
            $lot->price = $addLotRequest->getPrice();

            $result = $this->lotRepository->add($lot);

            return response()->json($result);
        } catch (\Exception $exception) {
            return $this->returnJsonError($exception->getMessage(), $exception->getCode());
        }
    }
}
