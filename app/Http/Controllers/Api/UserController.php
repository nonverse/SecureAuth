<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Repository\UserRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Models\Profile;
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

        $hasProfile = false;
        if (Profile::query()->where('uuid', $user->uuid)->exists() && Profile::query()->where('uuid', $user->uuid)->first()->profile_verified_at !== null) {
            $hasProfile = true;
        }

        return new JsonResponse([
            'data' => [
                'authenticated' => true,
                'uuid' => $user->uuid
            ],
            'meta' => [
                'email_verified_at' => $user->email_verified_at,
                'has_valid_profile' => $hasProfile,
            ]
        ]);
    }
}
