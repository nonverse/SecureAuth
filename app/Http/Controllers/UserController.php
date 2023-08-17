<?php

namespace App\Http\Controllers;

use App\Contracts\Repository\SettingsRepositoryInterface;
use App\Contracts\Repository\UserRepositoryInterface;
use App\Http\Controllers\Auth\AbstractAuthenticationController;
use App\Services\User\UserCreationService;
use App\Services\User\UserManagementService;
use Carbon\CarbonImmutable;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UserController extends AbstractAuthenticationController
{
    /**
     * @var UserRepositoryInterface
     */
    private UserRepositoryInterface $repository;

    /**
     * @var UserCreationService
     */
    private UserCreationService $creationService;

    /**
     * @var UserManagementService
     */
    private UserManagementService $userManagementService;

    public function __construct(
        UserRepositoryInterface     $repository,
        UserCreationService         $creationService,
        UserManagementService       $userManagementService,
        SettingsRepositoryInterface $settingsRepository
    )
    {
        parent::__construct($settingsRepository, $userManagementService);
        $this->repository = $repository;
        $this->creationService = $creationService;
        $this->userManagementService = $userManagementService;
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
        $userResponse = $this->creationService->handle($request->all());

        /**
         * If successfully created new user, logout any existing user
         * and login the new user
         */
        if ($userResponse) {
            $user = $this->repository->get($userResponse['data']['uuid']);

            $this->userManagementService->remember($request);
            $this->userManagementService->add($request, $user);

            return $this->sendLoginSuccessResponse($request, $user);
        }

        return new JsonResponse([
            'complete' => false
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
        $cookie = $this->userManagementService->getCookie($request);

        $response = [];
        $latestUuid = '';

        foreach ($cookie as $uuid => $userRaw) {
            try {
                /**
                 * Try to get user from database
                 */
                $user = $this->repository->get($uuid);
                $timeStamp = CarbonImmutable::minValue();

                /**
                 * Determine the last logged in user
                 */
                if ($timeStamp->isBefore(CarbonImmutable::parse($userRaw['session']['aat']))) {
                    $timeStamp = $userRaw['session']['aat'];
                    $latestUuid = $uuid;
                }

                $response[$uuid] = [
                    'data' => [
                        'email' => $user->email,
                        'name_first' => $user->name_first,
                        'name_last' => $user->name_last,
                    ],
                    'session' => [
                        'is_valid' => (int)(array_key_exists('exp', $cookie[$user->uuid]['session']) && CarbonImmutable::now()->isBefore($cookie[$user->uuid]['session']['exp'])) ?: (int)($request->user() && $request->user()->uuid == $user->uuid)
                    ]
                ];
            } catch (Exception $e) {
                /**
                 * If unable to find user in database, assume the account was deleted and remove
                 * from response
                 */
                unset($response[$uuid]);
            }
        }

        return new JsonResponse([
            'data' => [
                'users' => $response,
                'last_user' => $latestUuid,
                'current_user' => $request->user() ? $request->user()->uuid : null,
            ],
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
