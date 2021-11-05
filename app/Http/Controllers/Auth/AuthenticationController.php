<?php

namespace App\Http\Controllers\Auth;

use App\Contracts\Repository\UserRepositoryInterface;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthenticationController extends AbstractAuthenticationController
{
    /**
     * @var UserRepositoryInterface
     */
    private $repository;

    public function __construct(
        UserRepositoryInterface $repository
    )
    {
        $this->repository = $repository;
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
            $user = $this->repository->get($email);
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
    public function getUser(Request $request): JsonResponse
    {
        return new JsonResponse([
            'data' => [
                'authenticated' => true,
                'uuid' => $request->user()->uuid
            ]
        ]);
    }
}
