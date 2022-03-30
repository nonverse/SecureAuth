<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Api\FrontEndTokenCreationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AbstractAuthenticationController extends Controller
{
    /**
     * @var FrontEndTokenCreationService
     */
    private $tokenCreationService;

    public function __construct(
        FrontEndTokenCreationService $tokenCreationService
    )
    {
        $this->tokenCreationService = $tokenCreationService;
    }

    /**
     * Retrieve the intended target URI following login
     *
     * @param $request
     * @return array
     */
    public function retrieveIntended($request): array
    {
        return array(
            'host' => $request->input('host') ?: env('MIX_BASE_APP'),
            'resource' => $request->input('resource') ?: ''
        );

    }

    /**
     * Regenerate session and redirect user to target URI
     *
     * @param Request $request
     * @param User $user
     * @return JsonResponse
     */
    public function sendLoginSuccessResponse(Request $request, User $user): JsonResponse
    {
        $request->session()->forget('two_factor_token');
        $request->session()->regenerate();

        $intended = $this->retrieveIntended($request);
        $cookie = cookie('uuid', $user->uuid, 2628000);
//        $token = $this->tokenCreationService->handle($user);
//
//        if (!$token) {
//            return response()->json([
//                'complete' => false
//            ]);
//        }

        Auth::login($user, $request->input('remember'));

        return response()->json([
            'data' => [
                'complete' => true,
                'uuid' => $user->uuid,
                'host' => $intended['host'],
                'resource' => $intended['resource']
            ]
        ])->cookie($cookie);
    }

    /**
     * Direct a user to login page after successful logout
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function sendLogoutSuccessResponse(Request $request): JsonResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            'data' => [
                'success' => true
            ]
        ])->withCookie('uuid');
    }
}
