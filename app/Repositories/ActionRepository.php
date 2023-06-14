<?php

namespace App\Repositories;

use App\Models\Action;

class ActionRepository extends Repository implements \App\Contracts\Repository\ActionRepositoryInterface
{

    public function model(): string
    {
        return Action::class;
    }
}
