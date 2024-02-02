<?php

namespace App\Http\Controllers;

use App\Contracts\Repository\ActionRepositoryInterface;
use App\Contracts\Repository\UserRepositoryInterface;
use App\Services\Authorization\CreateAuthorizationTokenService;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class AuthorizationController extends Controller
{
    /**
     * @var UserRepositoryInterface
     */
    private UserRepositoryInterface $userRepository;

    /**
     * @var ActionRepositoryInterface
     */
    private ActionRepositoryInterface $actionRepository;

    /**
     * @var CreateAuthorizationTokenService
     */
    private CreateAuthorizationTokenService $createAuthorizationTokenService;

    /**
     * @var Hasher
     */
    private Hasher $hasher;

    public function __construct(
        UserRepositoryInterface         $userRepository,
        ActionRepositoryInterface       $actionRepository,
        CreateAuthorizationTokenService $createAuthorizationTokenService,
        Hasher                          $hasher
    )
    {
        $this->userRepository = $userRepository;
        $this->actionRepository = $actionRepository;
        $this->createAuthorizationTokenService = $createAuthorizationTokenService;
        $this->hasher = $hasher;
    }

    /**
     * Get action description
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function get(Request $request): JsonResponse
    {
        /**
         * Validate request
         */
        $validator = Validator::make($request->all(), [
            'action_id' => 'required',
        ]);

        if ($validator->fails()) {
            return new JsonResponse($validator->errors(), 422);
        }

        try {
            $action = $this->actionRepository->get($request->input('action_id'));
        } catch (Exception $e) {
            return new JsonResponse([
                'error' => 'Invalid action identifier'
            ], 400);
        }

        return new JsonResponse([
            'data' => [
                'action_id' => $action->id,
                'action_description' => $action->description
            ]
        ]);
    }

    /**
     * Handle new authorization token request
     *
     * @param Request $request
     * @return Application|ResponseFactory|\Illuminate\Foundation\Application|JsonResponse|Response|void
     */
    public function create(Request $request)
    {
        /**
         * Validate request
         */
        $validator = Validator::make($request->all(), [
            'code' => 'min:6|max:6|required_without:password',
            'password' => 'required_without:code',
            'action_id' => 'required',
        ]);

        if ($validator->fails()) {
            return new JsonResponse($validator->errors(), 422);
        }

        /**
         * Get user from database
         */
        $user = $this->userRepository->get($request->user()->uuid);

        /**
         * If user is authorizing using password...
         */
        if ($request->input('password')) {

            /**
             * Validate password
             */
            if (!$this->hasher->check($request->input('password'), $user->password)) {
                return response('Invalid password', 401);
            }

            /**
             * Create a new authorization token
             */
            $token = $this->createAuthorizationTokenService->handle($request, [
                'action_id' => $request->input('action_id')
            ]);

            return new JsonResponse([
                'data' => [
                    'authorized' => true,
                    'authorization_token' => $token,
                ]
            ]);
        }
    }
}
