<?php

namespace App\Http\Controllers\Auth;

use App\Contracts\Repository\UserRepositoryInterface;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\Encryption\Encrypter;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AuthenticationController extends AbstractAuthenticationController
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

    public function __construct(
        UserRepositoryInterface $repository,
        Encrypter               $encrypter,
        Hasher                  $hasher
    )
    {
        $this->repository = $repository;
        $this->encrypter = $encrypter;
        $this->hasher = $hasher;
    }

    /**
     * Handle login request
     *
     * @param Request $request
     * @return Response|JsonResponse
     */
    public function login(Request $request): Response|JsonResponse
    {

        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        /*
         * Get user account by email
         */
        $user = $this->repository->get($request->input('email'));
        if (!$user) {
            return response('User not found', 400);
        }

        /*
         * Check if user's password is correct
         */
        if (!$this->hasher->check($request->input('password'), $user->password)) {
            return response('Invalid password', 401);
        }

        /*
         * Check if user has any violations on account
         */
        if ($user->violations) {
            return response('Your account has been' . $user->violations . 'for violation the Nonverse Terms and Conditions', 403);
        }

        /*
         * 2FA logic
         */
        if ($user->use_totp) {
            /*
             * If user has UUID cookie and last login on device was less than
             * 14 days ago, skip 2FA on same device
             */
            if ($request->cookie('user')) {
                $timeout = CarbonImmutable::parse(json_decode($request->cookie('user'))->authed_at)->addDays(14);
                if (CarbonImmutable::now()->isBefore($timeout)) {
                    return $this->sendLoginSuccessResponse($request, $user);
                }
            }

            /*
             * Issue 2FA authentication token and store encrypted value in session
             */
            $token = Str::random(64);
            $request->session()->put('two_factor_authentication', [
                'uuid' => $user->uuid,
                'token_value' => $this->encrypter->encrypt($token),
                'token_expiry' => CarbonImmutable::now()->addMinutes(10)
            ]);

            return new JsonResponse([
                'data' => [
                    'complete' => false,
                    'authentication_token' => $token
                ]
            ]);
        }

        return $this->sendLoginSuccessResponse($request, $user);
    }

    /**
     * Log out currently authenticated user
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        return $this->sendLogoutSuccessResponse($request);
    }

    /**
     * Log user out of all devices expect current one
     *
     * @param Request $request
     * @return bool|Application|ResponseFactory|Response|null
     */
    public function logoutAll(Request $request): Response|bool|Application|ResponseFactory|null
    {
        $request->validate([
            'password' => 'required'
        ]);

        if (!$this->hasher->check($request->input('password'), $request->user()->password)) {
            return response('Invalid password', 401);
        }

        return Auth::logoutOtherDevices($request->input('password'));
    }
}
