<?php

namespace App\Models\OAuth2;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RefreshToken extends Model
{
    use HasFactory;

    /**
     * @var string
     */
    public $table = 'oauth_refresh_tokens';

    /**
     * Indicates if the model's primary key is auto-incrementing
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * Data type of the model's primary key
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * @var string[]
     */
    public $fillable = [
        'id',
        'access_token_id',
        'revoked',
        'expires_at',
    ];

    public $timestamps = false;
}
