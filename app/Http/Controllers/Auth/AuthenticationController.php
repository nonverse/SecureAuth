<?php

namespace App\Http\Controllers\Auth;

use App\Contracts\Repository\SettingsRepositoryInterface;
use App\Contracts\Repository\UserRepositoryInterface;
use App\Services\User\UserManagementService;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\Auth\Authenticatable;
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

    /**
     * @var UserManagementService
     */
    private UserManagementService $userManagementService;

    public function __construct(
        UserRepositoryInterface     $repository,
        SettingsRepositoryInterface $settingsRepository,
        UserManagementService       $managementService,
        Encrypter                   $encrypter,
        Hasher                      $hasher
    )
    {
        parent::__construct($settingsRepository, $managementService);
        $this->userManagementService = $managementService;
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
        /**
         * If a UUID is present in the request, attempt to log the user in using their
         * UUID and user cookie
         */
        if ($request->has('uuid')) {
            return $this->loginUsingUuid($request);
        }

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
             * x days ago, skip 2FA on same device
             */
            $users = $this->userManagementService->getCookie($request);
            if (array_key_exists($user->uuid, $users)) {
                $timeout = CarbonImmutable::parse($users[$user->uuid]['session']['aat'])->addDays(7);
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
     * Check if the user is remembered in the cookie and either
     * send usr login details or login user
     *
     * @param Request $request
     * @return JsonResponse
     */
    protected function loginUsingUuid(Request $request): JsonResponse
    {
        $user = $this->repository->get($request->input('uuid'));

        if (!$user) {
            return new JsonResponse([
                'complete' => false,
                'errors' => [
                    'uuid' => 'User not found'
                ]
            ], 404);
        }

        $this->userManagementService->remember($request);

        if (!$this->userManagementService->isRemembered($request, $user)) {
            return response()->json([
                'complete' => false,
                'data' => [
                    'email' => $user->email,
                    'name_first' => $user->name_first,
                    'name_last' => $user->name_last
                ]])->withCookie($this->userManagementService->getResponseCookie());
        }

        return $this->sendLoginSuccessResponse($request, $user)->withCookie($this->userManagementService->getResponseCookie());
    }
}
