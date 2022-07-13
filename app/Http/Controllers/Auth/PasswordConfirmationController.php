<?php

namespace App\Http\Controllers\Auth;

use App\Contracts\Repository\UserRepositoryInterface;
use App\Services\Auth\PasswordConfirmationService;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\Encryption\Encrypter;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use PragmaRX\Google2FA\Exceptions\IncompatibleWithGoogleAuthenticatorException;
use PragmaRX\Google2FA\Exceptions\InvalidCharactersException;
use PragmaRX\Google2FA\Exceptions\SecretKeyTooShortException;
use PragmaRX\Google2FA\Google2FA;

class PasswordConfirmationController extends AbstractAuthenticationController
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

    /**
     * @var PasswordConfirmationService
     */
    private PasswordConfirmationService $confirmationService;

    public function __construct(
        UserRepositoryInterface     $repository,
        Encrypter                   $encrypter,
        Hasher                      $hasher,
        Google2FA                   $google2FA,
        PasswordConfirmationService $confirmationService
    )
    {
        $this->repository = $repository;
        $this->encrypter = $encrypter;
        $this->hasher = $hasher;
        $this->google2FA = $google2FA;
        $this->confirmationService = $confirmationService;
    }

    /**
     * Verify a user's password
     *
     * @param Request $request
     * @return Response|JsonResponse
     */
    public function password(Request $request): Response|JsonResponse
    {
        $request->validate([
            'password' => 'required'
        ]);

        $user = $request->user();
        if (!$this->hasher->check($request->input('password'), $user->password)) {
            return response('Invalid password', 401);
        }

        if ($user->use_totp) {
            $token = Str::random(64);
            $request->session()->put('two_factor_authentication', [
                'uuid' => $user->uuid,
                'token_value' => $token,
                'token_expiry' => CarbonImmutable::now()->addMinutes(10)
            ]);

            return new JsonResponse([
                'data' => [
                    'complete' => false,
                    'authentication_token' => $token
                ]
            ]);
        }

        /*
         * Store confirmation token with expiry
         */
        $confirmation = $this->confirmationService->handle($request, $user, $request->input('authenticates'));

        return new JsonResponse([
            'data' => [
                'complete' => true,
                ...$confirmation
            ]
        ]);
    }

    /**
     * Verify a user's 2FA code
     *
     * @param Request $request
     * @return Response|JsonResponse
     * @throws SecretKeyTooShortException
     * @throws IncompatibleWithGoogleAuthenticatorException
     * @throws InvalidCharactersException
     */
    public function twoFactor(Request $request): Response|JsonResponse
    {
        $request->validate([
            'one_time_password' => 'required',
            'authentication_token' => 'required'
        ]);

        /*
         * Verify authentication token
         */
        if (!$this->validateAuthenticationToken($request, $request->input('authentication_token'))) {
            return response('Invalid authentication token', 401);
        }

        $user = $request->user();

        /*
         * Verify 2FA code
         */
        $secret = $this->encrypter->decrypt($user->totp_secret);
        if (!$this->google2FA->verifyKey($secret, $request->input('one_time_password'))) {
            return response('Invalid OTP', 401);
        }


        return new JsonResponse([
            'data' => [
                'complete' => true
            ]
        ]);
    }
}
