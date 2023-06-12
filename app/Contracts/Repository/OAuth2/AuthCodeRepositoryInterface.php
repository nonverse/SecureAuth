<?php

namespace App\Contracts\Repository\OAuth2;

use App\Contracts\Repository\RepositoryInterface;
use Illuminate\Database\Eloquent\Model;

interface AuthCodeRepositoryInterface extends RepositoryInterface
{
    public function getUsingClientAndUser($clientId, $userId): Model;
}
