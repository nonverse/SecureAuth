<?php

namespace App\Http\Controllers\User;

use App\Contracts\Repository\UserRepositoryInterface;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UserController extends Controller
{
    /**
     * @var UserRepositoryInterface
     */
    private UserRepositoryInterface $repository;

    public function __construct(
        UserRepositoryInterface $repository
    )
    {
        $this->repository = $repository;
    }

    /**
     * Check if the email requested is associated with an user account
     *
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function email(Request $request): Response|JsonResponse
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        /*
         * Attempt to get the user associated with the requested email
         */
        $user = $this->repository->get($request->input('email'));
        if (!$user) {
            /*
             * If an account is not found, return HTTP error 404
             */
            return response('User not found', 404);
        }

        /*
         * Create an unauthenticated browser cookie containing the user's UUID
         * This cookie expires in 5 minutes
         */
        $cookie = cookie('user', json_encode([
            'uuid' => $user->uuid,
            'has_authed' => false
        ]), 5);

        /*
         * If an account is found, return the user's UUID,
         * first name and last name
         */
        return response()->json([
            'data' => [
                'uuid' => $user->uuid,
                'name_first' => $user->name_first,
                'name_last' => $user->name_last,
            ]
        ])->withCookie($cookie);
    }

    /**
     * Return details of a user stored in browser cookie
     *
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function cookie(Request $request): Response|JsonResponse
    {

        if (!$request->cookie('user')) {
            return response('No user cookie found', 404);
        }

        $user = $this->repository->get(json_decode($request->cookie('user'))->uuid);

        return new JsonResponse([
            'data' => [
                'uuid' => $user->uuid,
                'email' => $user->email,
                'name_first' => $user->name_first,
                'name_last' => $user->name_last,
            ]
        ]);
    }
}
