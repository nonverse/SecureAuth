<?php

namespace App\Http\Controllers\Auth;

use App\Contracts\Repository\SettingsRepositoryInterface;
use App\Contracts\Repository\UserRepositoryInterface;
use Carbon\CarbonImmutable;
use Exception;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Encryption\Encrypter;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
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
        UserRepositoryInterface     $repository,
        SettingsRepositoryInterface $settingsRepository,
        Encrypter                   $encrypter,
        Hasher                      $hasher
    )
    {
        parent::__construct($settingsRepository);
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
            if ($request->cookie('user') && array_key_exists($user->uuid, json_decode($request->cookie('user'), true))) {
                $users = json_decode($request->cookie('user'), true);
                $timeout = CarbonImmutable::parse($users[$user->uuid]['authed_at'])->addDays(7);
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
                'token_value' => $token,
                'token_expiry' => CarbonImmutable::now()->addMinutes(10)
            ]);

            return new JsonResponse([
                'complete' => false,
                'data' => [
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
     * @return Response|bool|Application|ResponseFactory|Authenticatable|null
     */
    public function logoutAll(Request $request): Response|bool|Application|ResponseFactory|Authenticatable|null
    {
        $request->validate([
            'password' => 'required'
        ]);

        if (!$this->hasher->check($request->input('password'), $request->user()->password)) {
            return response('Invalid password', 401);
        }

        return Auth::logoutOtherDevices($request->input('password'));
    }

    /**
     * Switch the currently logged in user
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function switchUser(Request $request): JsonResponse
    {
        /*
         * Validate request
         */
        $validator = Validator::make($request->all(), [
            'uuid' => 'required',
        ]);

        if ($validator->fails()) {
            return new JsonResponse([
                'complete' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        /*
         * Get the user cookie before regenerating the session so that we
         * can persist it to the new session
         *
         * The user cookie is also useful for later on when we have to edit
         * the current users remember timestamps
         */
        $userCookie = $request->cookie('user') ? json_decode($request->cookie('user'), true) : [];

        /*
         * Get the current user
         */
        $user = $request->user();

        /*
         * Attempt to get new user from database
         */
        try {
            $newUser = $this->repository->get($request->input('uuid'));
            if (!$newUser) {
                throw new Exception('No User');
            }
        } catch (Exception $e) {
            return new JsonResponse([
                'complete' => false,
                'errors' => [
                    'uuid' => 'Invalid UUID'
                ]
            ], 400);
        }

        /**
         * Add remember_until timestamp to previous user
         */
        $userCookie[$user->uuid] = [
            ...$userCookie[$user->uuid],
            'remember_until' => CarbonImmutable::now()->addMinutes(config('auth.user_session.remember'))
        ];

        $userCookieNew = cookie('user', json_encode($userCookie));

        if (array_key_exists($newUser->uuid, $userCookie) && array_key_exists('remember_until', $userCookie[$newUser->uuid])) {
            if ($rememberUntil = $userCookie[$newUser->uuid]['remember_until']) {
                if (CarbonImmutable::now()->isBefore($rememberUntil)) {
                    Auth::logout();
                    return $this->sendLoginSuccessResponse($request, $newUser)->withCookie($userCookieNew);
                }
            }
        }

        /*
         * Pasan sat and did fuck all but helped me figure this shit out!!
         *
         * Clear the user_session cookie in parent domain
         */
        $sessionCookieNew = cookie('user_session', null, null, null, env('SESSION_PARENT_DOMAIN'));

        /*
         * Logout previous user and invalidate their session
         */
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $query = http_build_query([
            'email' => $newUser->email
        ]);

        return response()->json([
            'complete' => true,
            'data' => [
                'redirect_uri' => env('APP_URL') . '/login?' . $query
            ]
        ])->withCookie($userCookieNew)->withCookie($sessionCookieNew);
    }
}
