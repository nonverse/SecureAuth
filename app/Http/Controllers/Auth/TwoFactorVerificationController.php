<?php

namespace App\Http\Controllers\Auth;

use App\Contracts\Repository\UserRepositoryInterface;
use App\Services\Api\FrontEndTokenCreationService;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Encryption\Encrypter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use PragmaRX\Google2FA\Exceptions\IncompatibleWithGoogleAuthenticatorException;
use PragmaRX\Google2FA\Exceptions\InvalidCharactersException;
use PragmaRX\Google2FA\Exceptions\SecretKeyTooShortException;
use PragmaRX\Google2FA\Google2FA;
use RuntimeException;

class TwoFactorVerificationController extends AbstractAuthenticationController
{
    /**
     * @var UserRepositoryInterface
     */
    private $repository;

    /**
     * @var Encrypter
     */
    private $encrypter;

    /**
     * @var Google2FA
     */
    private $google2FA;

    public function __construct(
        UserRepositoryInterface $repository,
        Encrypter               $encrypter,
        Google2FA               $google2FA,
        FrontEndTokenCreationService $tokenCreationService
    )
    {
        parent::__construct($tokenCreationService);

        $this->repository = $repository;
        $this->encrypter = $encrypter;
        $this->google2FA = $google2FA;
    }

    /**
     * @throws IncompatibleWithGoogleAuthenticatorException
     * @throws InvalidCharactersException
     * @throws SecretKeyTooShortException
     */
    public function verify(Request $request)
    {
        $details = $request->session()->get('two_factor_token');
        if (!$this->validateSessionDetails($details)) {
            return response('Authentication token has expired', 400);
        }

        if ($request->input('auth_token') !== $this->encrypter->decrypt($details['token_value'])) {
            return response('Invalid authentication token', 400);
        }

        try {
            $user = $this->repository->get($details['uuid']);
        } catch (ModelNotFoundException $e) {
            return response('Invalid user store', 400);
        }

        // 2FA recovery process if a user has lost access to their authenticator app
        if ($request->has('recovery_token')) {
            // Check if a valid recovery token was provided
            if (!Hash::check($request->input('recovery_token'), $user->totp_recovery_token)) {
                return response('Invalid recovery token', 401);
            }

            // Disable 2FA on user's account
            try {
                $user->update([
                    'use_totp' => false,
                    'totp_authenticated_at' => Carbon::now()
                ]);
            } catch (Exception $e) {
                throw new RuntimeException($e->getMessage());
            }

            return $this->sendLoginSuccessResponse($request, $user);
        }

        // Verify user using the authenticator code provided
        $secret = $this->encrypter->decrypt($user->totp_secret);
        if (!$this->google2FA->verifyKey($secret, $request->input('code'))) {
            return response('Invalid code', 401);
        }

        return $this->sendLoginSuccessResponse($request, $user);
    }

    /**
     * Verify that the session details are valid
     *
     * @param array $details
     * @return bool
     */
    protected function validateSessionDetails(array $details): bool
    {
        $validator = Validator::make($details, [
            'uuid' => 'required|string',
            'token_value' => 'required|string',
            'token_expiry' => 'required'
        ]);

        if ($validator->fails()) {
            return false;
        }

        if (!$details['token_expiry'] instanceof CarbonInterface) {
            return false;
        }

        if ($details['token_expiry']->isBefore(CarbonImmutable::now())) {
            return false;
        }

        return true;
    }
}
