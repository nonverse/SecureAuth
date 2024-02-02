<?php

namespace App\Models;

use App\Notifications\ResetPassword;
use App\Services\User\HasApiTokens;
use Carbon\Carbon;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * @property string $uuid
 * @property string $username
 * @property string $name_first
 * @property string $name_last
 * @property string $email
 * @property string $phone
 * @property Carbon $dob
 * @property string $password
 * @property bool $admin
 * @property bool $use_totp
 * @property string $totp_secret
 * @property string $totp_recovery_token
 * @property string $violations
 * @property Carbon $email_verified_at
 * @property Carbon $totp_authenticated_at
 * @property Carbon $violation_ends_at
 * @property string $remember_token
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable, CanResetPassword, HasApiTokens;

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'uuid';

    /**
     * Indicates if the model's ID is auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The data type of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'use_totp',
        'totp_secret',
        'totp_recovery_token',
        'totp_authenticated_at',
        'password'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * @param string $token
     * @return void
     */
    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPassword($this, env('APP_URL') . '/recovery/password?token=' . $token . '&email=' . urlencode($this->email)));
    }
}
