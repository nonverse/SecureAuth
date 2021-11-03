<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
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
            $remember = $request->has('keep_authenticated');

            /**
             * @var User
             */
            $user = User::query()->where('email', $email)->firstOrFail();

        } catch (ModelNotFoundException $e) {
            return response('User not found', 400);
        }

        // Verify that the user has provided a valid password before continuing
        if (!Hash::check($request->input('password'), $user->password)) {
            return response('Invalid password', 401);
        }

        // TODO Add logic to handle 2FA authentication

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

    /**
     * Check if a email provided belongs to a valid user instance
     *
     * @param Request $request
     * @return JsonResponse|Response
     */
    function verifyUserEmail(Request $request)
    {
        // Check if a email was provided in the request and
        // verify that the email has a corresponding user instance
        try {
            $email = $request->input('email');

            /**
             * @var User
             */
            $user = User::query()->where('email', $email)->firstOrFail();
        } catch (ModelNotFoundException $e) {
            return response('Unable to find user', 400);
        }

        return new JsonResponse([
            'data' => [
                'email' => $user->email,
                'name_first' => $user->name_first,
                'name_last' => $user->name_last
            ]
        ]);
    }

    /**
     * Check if a user is authenticated on the system
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function verifyAuthentication(Request $request): JsonResponse
    {
        if ($request->user()) {
            $user = $request->user();
        } else {
            return new JsonResponse([
                'data' => [
                    'authenticated' => false,
                ]
            ]);
        }

        return new JsonResponse([
            'data' => [
                'authenticated' => true,
                'uuid' => $user->uuid
            ]
        ]);
    }
}
