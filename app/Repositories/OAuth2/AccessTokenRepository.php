<?php

namespace App\Repositories\OAuth2;

use App\Models\OAuth2\AccessToken;
use Illuminate\Database\Eloquent\Model;

class AccessTokenRepository extends \App\Repositories\Repository implements \App\Contracts\Repository\OAuth2\AccessTokenRepositoryInterface
{

    public function model(): string
    {
        return AccessToken::class;
    }

    public function getUsingClientAndUser($clientId, $userId): Model
    {
        return $this->getBuilder()->where([['client_id', $clientId], ['user_id', $userId]])->firstOrFail();
    }
}
