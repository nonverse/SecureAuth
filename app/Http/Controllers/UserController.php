<?php

namespace App\Http\Controllers;

use App\Contracts\Repository\UserRepositoryInterface;
use App\Services\User\UserCreationService;
use Carbon\CarbonImmutable;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

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
        UserCreationService     $creationService
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
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email:rfc,dns|unique:users,email',
            'name_first' => 'required|string',
            'name_last' => 'required|string',
            'username' => 'required|string|unique:users,username',
            'phone' => 'string|min:7|max:15',
            'dob' => 'date',
            'password' => 'required|min:8|confirmed'
        ]);

        /**
         * Try to create a new user
         */
        $user = $this->creationService->handle($request->all());

        /**
         * If successfully created new user, logout any existing user
         * and login the new user
         */
        if ($user) {
            $uuid = $user['data']['uuid'];

            /*
             * Add user to UUID remember cookie
             */
            $cookieData = $request->cookie('user') ? json_decode($request->cookie('user'), true) : [];
            $cookieData[$uuid] = [
                // Store successful login timestamp in cookie
                'authed_at' => CarbonImmutable::now()
            ];

            $cookie = cookie('user', json_encode($cookieData));

            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            Auth::loginUsingId($uuid);

            return response()->json([
                'success' => true,
                'data' => [
                    'uuid' => $uuid
                ]
            ])->withCookie($cookie);
        }

        return new JsonResponse([
            'success' => false
        ], 422);
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
        if (!$cookie = $request->cookie('user')) {
            return response('No user cookie found', 404);
        }

        /**
         * Decode cookie containing timed out users
         */
        $users = json_decode($cookie);

        $response = [];
        $latestUuid = '';
        foreach ($users as $uuid => $authedAt) {
            try {
                /**
                 * Try to get user from database
                 */
                $user = $this->repository->get($uuid);
                $timeStamp = CarbonImmutable::minValue();

                /**
                 * Determine the last logged in user
                 */
                if ($timeStamp->isBefore(CarbonImmutable::parse($authedAt->authed_at))) {
                    $timeStamp = $authedAt;
                    $latestUuid = $uuid;
                }

                $response[$uuid] = [
                    'email' => $user->email,
                    'name_first' => $user->name_first,
                    'name_last' => $user->name_last
                ];
            } catch (Exception $e) {
                /**
                 * If unable to find user in database, assume the account was deleted
                 */
                $response[$uuid] = [
                    'email' => 'deleteduser@nonverse.net',
                    'name_first' => 'Deleted',
                    'name_last' => 'User'
                ];
            }
        }

        return new JsonResponse([
            'data' => [
                'users' => $response,
                'last_login' => $latestUuid
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
