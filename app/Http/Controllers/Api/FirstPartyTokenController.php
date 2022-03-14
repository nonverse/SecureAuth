<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Repository\UserRepositoryInterface;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\Encryption\Encrypter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\MessageBag;

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
     * @return MessageBag|JsonResponse
     */
    public function verify(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'uuid' => 'required',
            'token' => 'required|min:64|max:64'
        ]);

        if ($validator->fails()) {
            abort(422);
        }

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
