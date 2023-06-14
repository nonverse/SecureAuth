<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuthorizationToken extends Model
{
    use HasFactory;

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
}
