<?php

namespace App\Http\Controllers\Auth;

use App\Contracts\Repository\UserRepositoryInterface;
use Carbon\CarbonImmutable;
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
        UserRepositoryInterface      $repository,
        Encrypter                    $encrypter
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

        // Verify that the user does not have any violations recorded on their account
        if ($user->violations) {
            return response('You account has been ' . $user->violations . ' for violating the Nonverse terms and conditions', 403);
        }

        if ($user->use_totp) {

            // Skip 2FA if user was timed out of previous session
            $uuid = $request->cookie('uuid');
            if ($uuid === $user->uuid) {
                return $this->sendLoginSuccessResponse($request, $user);
            }

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

        return $this->sendLogoutSuccessResponse($request);
    }

    /**
     * Revoke user authentication on all devices except the current one
     *
     * @param Request $request
     * @return bool|Application|ResponseFactory|Response|null
     */
    public function revokeAllAuthentication(Request $request)
    {

        if (!Hash::check($request->input('password'), $request->user()->password)) {
            return response('Invalid password', 401);
        }

        return Auth::logoutOtherDevices($request->input('password'));
    }

    /**
     * Verify if a user exists on the system before continuing with authentication
     *
     * @param Request $request
     * @return JsonResponse|Response
     */
    function verifyEmail(Request $request)
    {
        // Check if a email was provided in the request and
        // verify that the email has a corresponding user instance
        try {
            $email = $request->input('email');

            /**
             * @var User
             */
            $user = $this->repository->get($email);
        } catch (ModelNotFoundException $e) {
            return response('Unable to find user', 400);
        }

        // Return the name of the user to display on client app
        return new JsonResponse([
            'data' => [
                'email' => $user->email,
                'name_first' => $user->name_first,
                'name_last' => $user->name_last
            ]
        ]);
    }
}
