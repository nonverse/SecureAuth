<?php

namespace App\Repositories;

use App\Contracts\Repository\RecoveryRepositoryInterface;
use App\Models\Recovery;

class RecoveryRepository extends Repository implements RecoveryRepositoryInterface
{

    public function model(): string
    {
        return Recovery::class;
    }
}
