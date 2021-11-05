<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;

class AbstractAuthenticationController extends Controller
{
    /**
     * Retrieve the intended target URI following login
     *
     * @param $request
     * @return array
     */
    public function retrieveIntended($request): array
    {
        return array(
            'host' => $request->input('host') ?: env('BASE_APP'),
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
        Auth::login($user, $request->input('remember'));
        $request->session()->regenerate();
        $intended = $this->retrieveIntended($request);

        return new JsonResponse([
            'data' => [
                'complete' => true,
                'uuid' => $user->uuid,
                'host' => $intended['host'],
                'resource' => $intended['resource']
            ]
        ]);
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

        return new JsonResponse([
            'data' => [
                'success' => true
            ]
        ]);
    }
}
