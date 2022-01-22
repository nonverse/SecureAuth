<?php

namespace App\Services\User;

use App\Contracts\Repository\UserRepositoryInterface;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use PragmaRX\Google2FA\Exceptions\IncompatibleWithGoogleAuthenticatorException;
use PragmaRX\Google2FA\Exceptions\InvalidCharactersException;
use PragmaRX\Google2FA\Exceptions\SecretKeyTooShortException;
use PragmaRX\Google2FA\Google2FA;
use RuntimeException;
use Illuminate\Encryption\Encrypter;
use Illuminate\Http\Response;

class TwoFactorEnableService
{
    /**
     * @var UserRepositoryInterface
     */
    private $repository;

    /**
     * @var Encrypter
     */
    private $encrypter;

    private $google2FA;

    public function __construct(
        UserRepositoryInterface $repository,
        Encrypter               $encrypter,
        Google2FA               $google2FA
    )
    {
        $this->repository = $repository;
        $this->encrypter = $encrypter;
        $this->google2FA = $google2FA;
    }

    /**
     * Enable 2FA on a user's account
     *
     * @param User $user
     * @param $code
     * @return array|Response
     * @throws IncompatibleWithGoogleAuthenticatorException
     * @throws InvalidCharactersException
     * @throws SecretKeyTooShortException
     */
    public function handle(User $user, $code)
    {
        try {
            $secret = $this->encrypter->decrypt($user->totp_secret);
        } catch (Exception $e) {
            throw new RuntimeException($e->getMessage());
        }

        if (!$this->google2FA->verifyKey($secret, $code)) {
            return [
                'enabled' => false
            ];
        }

        $token = Str::random(24);

        $this->repository->update($user->uuid, [
            'use_totp' => true,
            'totp_recovery_token' => Hash::make($token),
            'totp_authenticated_at' => Carbon::now()
        ]);

        return [
            'uuid' => $user->uuid,
            'recovery_token' => $token,
            'enabled' => true
        ];
    }
}
