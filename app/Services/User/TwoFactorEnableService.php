<?php

namespace App\Services\User;

use App\Contracts\Repository\UserRepositoryInterface;
use Carbon\Carbon;
use Exception;
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
     * @param $uuid
     * @param $code
     * @return array|Response
     * @throws IncompatibleWithGoogleAuthenticatorException
     * @throws InvalidCharactersException
     * @throws SecretKeyTooShortException
     */
    public function handle($uuid, $code)
    {
        try {
            $user = $this->repository->get($uuid);
            $secret = $this->encrypter->decrypt($user->totp_secret);
        } catch (Exception $e) {
            throw new RuntimeException($e->getMessage());
        }

        if (!$this->google2FA->verifyKey($secret, $code)) {
            return response('Invalid code', 401);
        }

        $this->repository->update($uuid, [
            'use_totp' => true,
            'totp_authenticated_at' => Carbon::now()
        ]);

        return [
            'uuid' => $user->uuid,
            'enabled' => true
        ];
    }
}
