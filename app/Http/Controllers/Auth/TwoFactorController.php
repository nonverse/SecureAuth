<?php

namespace App\Http\Controllers\Auth;

use App\Contracts\Repository\SettingsRepositoryInterface;
use App\Contracts\Repository\UserRepositoryInterface;
use App\Services\User\UserManagementService;
use Carbon\CarbonImmutable;
use Exception;
use http\Exception\RuntimeException;
use Illuminate\Contracts\Encryption\Encrypter;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use PragmaRX\Google2FA\Exceptions\IncompatibleWithGoogleAuthenticatorException;
use PragmaRX\Google2FA\Exceptions\InvalidCharactersException;
use PragmaRX\Google2FA\Exceptions\SecretKeyTooShortException;
use PragmaRX\Google2FA\Google2FA;

class TwoFactorController extends AbstractAuthenticationController
{
    /**
     * @var UserRepositoryInterface
     */
    private UserRepositoryInterface $repository;

    /**
     * @var Encrypter
     */
    private Encrypter $encrypter;

    /**
     * @var Hasher
     */
    private Hasher $hasher;

    /**
     * @var Google2FA
     */
    private Google2FA $google2FA;

    public function __construct(
        UserRepositoryInterface     $repository,
        SettingsRepositoryInterface $settingsRepository,
        UserManagementService       $managementService,
        Hasher                      $hasher,
        Google2FA                   $google2FA,
    )
    {
        parent::__construct($settingsRepository, $managementService);
        $this->repository = $repository;
        $this->encrypter = new \Illuminate\Encryption\Encrypter(base64_decode(str_replace('base64:', '', env('APP_SHARED_KEY'))), config('app.cipher'));;
        $this->hasher = $hasher;
        $this->google2FA = $google2FA;
    }

    /**
     * Verify 2FA code
     *
     * @param Request $request
     * @return Response|JsonResponse
     * @throws IncompatibleWithGoogleAuthenticatorException
     * @throws InvalidCharactersException
     * @throws SecretKeyTooShortException
     */
    public function verify(Request $request): Response|JsonResponse
    {
        $request->validate([
            'authentication_token' => 'required'
        ]);

        $store = $request->session()->get('two_factor_authentication');

        /*
         * Verify authentication token
         */
        if (!$this->validateAuthenticationToken($request, $request->input('authentication_token'))) {
            return response('Invalid authentication token, please restart login', 401);
        }

        /*
         * Get user from database
         */
        $user = $this->repository->get($store['uuid']);
        if (!$user) {
            return response('Unable to find user', 400);
        }

        /*
         * If a user has chosen to bypass 2FA using a TOTP recovery token,
         * verify the token and disable 2FA on the user's account
         */
        if ($request->has('totp_recovery_token')) {
            /*
             * Verify TOTP recovery token
             */
            if (!$this->hasher->check($request->input('totp_recovery_token'), $user->totp_recovery_token)) {
                return response('Invalid recovery token', 401);
                //TODO totp_recovery_token is no longer stored in user table
            }

            /*
             * Attempt to disable 2FA on user account
             */
            try {
                $this->repository->update($user->uuid, [
                    'use_totp' => 0,
                    'totp_authenticated_at' => CarbonImmutable::now()
                ]);
            } catch (Exception $e) {
                throw new RuntimeException($e->getMessage());
            }

            return $this->sendLoginSuccessResponse($request, $user);
        }

        /*
         * Verify 2FA code
         */
        $secret = $this->encrypter->decrypt($user->totp_secret);
        if (!$this->google2FA->verifyKey($secret, $request->input('one_time_password'))) {
            return response('Invalid OTP', 401);
        }

        return $this->sendLoginSuccessResponse($request, $user);
    }
}
