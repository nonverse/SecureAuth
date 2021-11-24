<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Repository\UserRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UserController extends Controller
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
     * Return the UUID of the currently authenticated user
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function get(Request $request): JsonResponse
    {
        $user = $request->user();

        return new JsonResponse([
            'data' => [
                'authenticated' => true,
                'uuid' => $user->uuid
            ],
            'meta' => [
                'email_verification' => $user->email_verified_at
            ]
        ]);
    }
}
