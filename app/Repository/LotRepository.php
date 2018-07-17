<?php

namespace App\Repository;

use App\Entity\Lot;
use \App\Repository\Contracts\LotRepository as ILotRepository;

/**
 * Class LotRepository
 * @package App\Repository
 */
class LotRepository implements ILotRepository
{

    public function add(Lot $lot): Lot
    {
        $lot->save();
        return $lot;
    }

    public function getById(int $id): ?Lot
    {
        return Lot::find($id);
    }

    public function findAll()
    {
        return Lot::all();
    }

    public function findActiveLot(int $userId): ?Lot
    {
        $lots = Lot::all();
        foreach ($lots as $lot) {
            $timestamp = now()->getTimestamp();
            if (($timestamp > $lot->getDateTimeOpen()) &&
                ($timestamp < $lot->getDateTimeClose())) {
                return $lot;
            }
        }
        return null;
    }
}
