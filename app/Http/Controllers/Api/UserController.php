<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Repository\UserRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Contracts\Encryption\Encrypter;
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

    /**
     * @var Encrypter
     */
    private $encrypter;

    public function __construct(
        UserRepositoryInterface $repository,
        Encrypter $encrypter
    )
    {
        $this->repository = $repository;
        $this->encrypter = $encrypter;
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
                'uuid' => $user->uuid,
                'api_token' => $this->encrypter->decryptString($user->api_encryption)
            ]
        ]);
    }
}
