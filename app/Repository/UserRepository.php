<?php

namespace App\Repository;

use \App\Repository\Contracts\UserRepository as IUserRepository;
use App\User;

/**
 * Class UserRepository
 * @package App\Repository
 */
class UserRepository implements IUserRepository
{

    public function getById(int $id): ?User
    {
        return User::find($id);
    }
}
