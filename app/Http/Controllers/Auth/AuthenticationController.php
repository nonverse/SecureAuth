<?php

namespace App\Http\Controllers\Auth;

use App\Contracts\Repository\UserRepositoryInterface;
use Carbon\CarbonImmutable;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Encryption\Encrypter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthenticationController extends AbstractAuthenticationController
{
    /**
     * @var UserRepositoryInterface
     */
    private $repository;

    /**
     * @var Encrypter
     */
    private $encrypter;

    public function __construct(
        UserRepositoryInterface $repository,
        Encrypter               $encrypter
    )
    {
        $this->repository = $repository;
        $this->encrypter = $encrypter;
    }

    /**
     * Authenticate a user on the system
     *
     * @param Request $request
     * @return Application|ResponseFactory|JsonResponse|Response
     */
    public function authenticate(Request $request)
    {
        // Check if a email was provided in the request and
        // verify that the email has a corresponding user instance
        try {
            $email = $request->input('email');
            $remember = false;

            /**
             * @var User
             */
            $user = $this->repository->get($email);

        } catch (ModelNotFoundException $e) {
            return response('User not found', 400);
        }

        // Verify that the user has provided a valid password before continuing
        if (!Hash::check($request->input('password'), $user->password)) {
            return response('Invalid password', 401);
        }

        if ($user->use_totp) {
            $token = Str::random(64);

            $request->session()->put('two_factor_token', [
                'uuid' => $user->uuid,
                'token_value' => $this->encrypter->encrypt($token),
                'token_expiry' => CarbonImmutable::now()->addMinutes(5)
            ]);

            return new JsonResponse([
                'data' => [
                    'complete' => false,
                    'auth_token' => $token
                ]
            ]);
        }

        // Attempt to authenticate a user
        try {
            Auth::login($user, $remember);
        } catch (AuthenticationException $e) {
            return response('Something went wrong', 500);
        }

        return $this->sendLoginSuccessResponse($request, $user);
    }

    /**
     * Revoke a user's authentication on the system
     *
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function revokeAuthentication(Request $request)
    {
        // Attempt to revoke user authentication
        try {
            Auth::logout();
        } catch (AuthenticationException $e) {
            return response('Something went wrong', 500);
        }

        return $this->sendLogoutSuccessResponse($request);
    }
}
