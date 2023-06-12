<?php

namespace App\Http\Controllers\User;

use App\Contracts\Repository\UserRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Services\User\UserCreationService;
use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UserController extends Controller
{
    /**
     * @var UserRepositoryInterface
     */
    private UserRepositoryInterface $repository;

    /**
     * @var UserCreationService
     */
    private UserCreationService $creationService;

    public function __construct(
        UserRepositoryInterface $repository,
        UserCreationService $creationService
    )
    {
        $this->repository = $repository;
        $this->creationService = $creationService;
    }

    /**
     * Return the UUID of the currently logged in user
     *
     * @param Request $request
     * @return Response|JsonResponse
     */
    public function get(Request $request): Response|JsonResponse
    {
        if (!$request->user()) {
            return response('No authenticated user found', 401);
        }

        return new JsonResponse([
            'data' => [
                'uuid' => $request->user()->uuid,
                'name_first' => $request->user()->name_first,
                'name_last' => $request->user()->name_last
            ]
        ]);
    }

    /**
     *
     * Request new user registration from API
     *
     * @param Request $request
     * @return PromiseInterface|\Illuminate\Http\Client\Response
     */
    public function store(Request $request): PromiseInterface|\Illuminate\Http\Client\Response
    {
        $request->validate([
            'email' => 'required|email|unique:users,email',
            'username' => 'required|unique:users,username',
            'name_first' => 'required',
            'name_last' => 'required',
            'password' => 'required|min:8|confirmed',
            'activation_key' => 'required'
        ]);

        return $this->creationService->handle($request->all());
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
         * If an account is found, return the user's UUID,
         * first name and last name
         */
        return response()->json([
            'data' => [
                'uuid' => $user->uuid,
                'name_first' => $user->name_first,
                'name_last' => $user->name_last,
            ]
        ]);
    }

    /**
     * Return details of a user stored in browser cookie
     *
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function getCookie(Request $request): Response|JsonResponse
    {
        if (!$request->cookie('user')) {
            return response('No user cookie found', 404);
        }

        $user = $this->repository->get(json_decode($request->cookie('user'))->uuid);

        if (!$user) {
            return response('Invalid user cookie', 400);
        }

        return new JsonResponse([
            'data' => [
                'uuid' => $user->uuid,
                'email' => $user->email,
                'name_first' => $user->name_first,
                'name_last' => $user->name_last,
            ]
        ]);
    }

    /**
     * Clear user browser cookie
     *
     * @return Response
     */
    public function clearCookie(): Response
    {
        return response('User cookie cleared')->withoutCookie('user');
    }
}
