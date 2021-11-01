<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthenticationController extends AbstractAuthenticationController
{
    /**
     * Authenticate a user using email and password
     *
     * @param Request $request
     * @return Application|RedirectResponse|Redirector|int
     */

    /**
     * Authenticate a user on the system
     *
     * @param Request $request
     * @return Application|RedirectResponse|Redirector|int
     */
    public function authenticate(Request $request)
    {
        // Check if a email was provided in the request and
        // verify that the email has a corresponding user instance
        try {
            $email = $request->input('email');
            $remember = $request->has('keep_authenticated');

            /**
             * @var User
             */
            $user = User::query()->where('email', $email)->firstOrFail();

        } catch (ModelNotFoundException $e) {
            return Response::HTTP_BAD_REQUEST;
        }

        // Verify that the user has provided a valid password before continuing
        if (!Hash::check($request->input('password'), $user->password)) {
            return Response::HTTP_UNAUTHORIZED;
        }

        // TODO Add logic to handle 2FA authentication

        // Attempt to authenticate a user
        try {
            Auth::login($user, $remember);
        } catch (AuthenticationException $e) {
            return Response::HTTP_INTERNAL_SERVER_ERROR;
        }

        return $this->sendLoginSuccessResponse($request);
    }

    /**
     * Revoke a user's authentication on the system
     *
     * @param Request $request
     * @return Application|RedirectResponse|Redirector|int
     */
    public function revokeAuthentication(Request $request)
    {
        // Attempt to revoke user authentication
        try {
            Auth::logout();
        } catch (AuthenticationException $e) {
            return Response::HTTP_INTERNAL_SERVER_ERROR;
        }

        return $this->sendLogoutSuccessResponse($request);
    }

    /**
     * Check if a user is authenticated on the system
     *
     * @param Request $request
     * @return Application|JsonResponse|Redirector|RedirectResponse
     */
    public function verifyAuthentication(Request $request)
    {
        if ($request->user()) {
            $user = $request->user();
        } else {
            return $this->sendUnauthenticatedResponse($request);
        }

        return new JsonResponse([
            'data' => [
                'authenticated' => 'true',
                'uuid' => $user->uuid
            ]
        ]);
    }
}
