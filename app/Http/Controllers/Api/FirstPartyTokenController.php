<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Repository\UserRepositoryInterface;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\Encryption\Encrypter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FirstPartyTokenController extends Controller
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
        Encrypter               $encrypter
    )
    {
        $this->repository = $repository;
        $this->encrypter = $encrypter;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function verify(Request $request): JsonResponse
    {
        $request->validate([
            'uuid' => 'required',
            'token' => 'required|min:64|max:64'
        ]);

        $user = $this->repository->get($request->input('uuid'));
        $token = $this->encrypter->decryptString($user->api_encryption);

        if ($request->input('token') !== $token) {
            return new JsonResponse([
                'errors' => [
                    'token' => 'Invalid API token'
                ]
            ], 401);
        }

        return new JsonResponse([
            'data' => [
                'uuid' => $user->uuid,
                'authenticated' => true
            ]
        ]);
    }
}
