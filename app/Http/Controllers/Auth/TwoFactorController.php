<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\User\TwoFactorSetupService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TwoFactorController extends Controller
{
    private $setupService;

    public function __construct(
        TwoFactorSetupService $setupService
    )
    {
        $this->setupService = $setupService;
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
}
