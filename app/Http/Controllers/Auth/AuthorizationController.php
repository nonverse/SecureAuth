<?php

namespace App\Http\Controllers\Auth;

use App\Contracts\Repository\UserRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Auth\AuthorizationService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AuthorizationController extends Controller
{
    /**
     * @var UserRepositoryInterface
     */
    private UserRepositoryInterface $repository;

    /**
     * @var AuthorizationService
     */
    private AuthorizationService $authorizationService;

    /**
     * @var Hasher
     */
    private Hasher $hasher;

    public function __construct(
        UserRepositoryInterface $repository,
        AuthorizationService    $authorizationService,
        Hasher                  $hasher,
    )
    {
        $this->repository = $repository;
        $this->authorizationService = $authorizationService;
        $this->hasher = $hasher;
    }

    /**
     * Issue an authorization token for requested action
     *
     * @param Request $request
     * @return Application|ResponseFactory|JsonResponse|Response|void
     */
    public function authorizationToken(Request $request)
    {

        /**
         * @var User
         */
        $user = $request->user();

        /**
         * Validate authorization request
         */
        $request->validate([
            'action_id' => 'required|string',
            'password' => 'required'
        ]);

        //TODO Verify action ID

        /**
         * Verify user's password
         */
        if (!$this->hasher->check($request->input('password'), $user->password)) {
            return response('Invalid password', 401);
        }

        $token = $this->authorizationService->handle($request);
        if ($token['success']) {
            return new JsonResponse([
                'data' => [
                    'authorization_token' => $token['authorization_token']
                ]
            ]);
        }
    }

    /**
     * Verify authorization token
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function verifyToken(Request $request) {
        $request->validate([
            'action_id' => 'required|string',
            'authorization_token' => 'required'
        ]);

        $authorization = $this->authorizationService->verify($request);
        if (!$authorization['success']) {
            return new JsonResponse([
                'error' => [
                    'token' => 'Invalid authorization token'
                ]
            ], 401);
        }

        return new JsonResponse([
            'data' => [
                ...$authorization['data']
            ]
        ]);
    }
}
