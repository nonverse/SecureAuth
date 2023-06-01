<?php

namespace App\Repositories\OAuth2;

use App\Models\OAuth2\AuthCode;
use Illuminate\Database\Eloquent\Model;

class AuthCodeRepository extends \App\Repositories\Repository implements \App\Contracts\Repository\OAuth2\AuthCodeRepositoryInterface
{

    public function model(): string
    {
        return AuthCode::class;
    }

    public function getUsingClientAndUser($clientId, $userId): Model
    {
        return $this->getBuilder()->where([['client_id', $clientId], ['user_id', $userId]])->first();
    }
}
