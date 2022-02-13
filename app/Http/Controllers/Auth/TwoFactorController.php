<?php

namespace App\Http\Controllers\Auth;

use App\Contracts\Repository\UserRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Services\User\TwoFactorEnableService;
use App\Services\User\TwoFactorSetupService;
use Carbon\Carbon;
use Illuminate\Encryption\Encrypter;
use Illuminate\Http\Response;
use RuntimeException;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use PragmaRX\Google2FA\Exceptions\IncompatibleWithGoogleAuthenticatorException;
use PragmaRX\Google2FA\Exceptions\InvalidCharactersException;
use PragmaRX\Google2FA\Exceptions\SecretKeyTooShortException;

class TwoFactorController extends Controller
{
    /**
     * @var TwoFactorSetupService
     */
    private $setupService;

    /**
     * @var TwoFactorEnableService
     */
    private $enableService;

    public function __construct(
        TwoFactorSetupService   $setupService,
        TwoFactorEnableService  $enableService
    )
    {
        $this->setupService = $setupService;
        $this->enableService = $enableService;
    }

    public function setup(Request $request)
    {
        if ($request->user()->use_totp) {
            return response('Two factor authentication is already enabled', 400);
        }

        return new JsonResponse([
            'data' => $this->setupService->handle($request->user())
        ]);
    }

    /**
     * Enable 2FA on a user's account
     *
     * @throws IncompatibleWithGoogleAuthenticatorException
     * @throws SecretKeyTooShortException
     * @throws InvalidCharactersException
     */
    public function enable(Request $request): JsonResponse
    {
        $request->validate([
            'code' => 'required|min:6|max:6'
        ]);

        // Attempt to toggle 2FA using the user provided authenticator code
        return new JsonResponse([
            'data' => $this->enableService->handle($request->user(), $request->input('code'))
        ]);
    }

    /**
     * Disable 2FA on a user's account
     *
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function disable(Request $request)
    {
        $user = $request->user();
        if (!Hash::check($request->input('password'), $user->password)) {
            return response('Incorrect password', 401);
        }

        try {
            $user->update([
                'use_totp' => false,
                'totp_authenticated_at' => Carbon::now()
            ]);
        } catch (Exception $e) {
            throw new RuntimeException($e->getMessage());
        }

        return new JsonResponse([
            'data' => [
                'uuid' => $user->uuid,
                'success' => true
            ]
        ]);
    }
}
