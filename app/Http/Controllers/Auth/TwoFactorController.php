<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\User\TwoFactorEnableService;
use App\Services\User\TwoFactorSetupService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use PragmaRX\Google2FA\Exceptions\IncompatibleWithGoogleAuthenticatorException;
use PragmaRX\Google2FA\Exceptions\InvalidCharactersException;
use PragmaRX\Google2FA\Exceptions\SecretKeyTooShortException;

class TwoFactorController extends Controller
{
    private $setupService;

    private $enableService;

    public function __construct(
        TwoFactorSetupService  $setupService,
        TwoFactorEnableService $enableService
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
            'data' => $this->setupService->handle($request->user()->uuid)
        ]);
    }

    /**
     * @throws IncompatibleWithGoogleAuthenticatorException
     * @throws SecretKeyTooShortException
     * @throws InvalidCharactersException
     */
    public function enable(Request $request)
    {
        $request->validate([
            'code' => 'required|min:6|max:6'
        ]);

        return new JsonResponse([
            'data' => $this->enableService->handle($request->user()->uuid, $request->input('code'))
        ]);
    }
}
