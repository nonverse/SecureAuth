<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Contracts\Repository\UserRepositoryInterface;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;

class UserInitialisationController extends Controller
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
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function getCookie(Request $request)
    {
        if (!$request->cookie('uuid')) {
            return response('No stored user', 400);
        }

        try {
            $user = $this->repository->get($request->cookie('uuid'));
        } catch (ModelNotFoundException $e) {
            return response('Invalid user stored', 400);
        }

        return new JsonResponse([
            'data' => [
                'email' => $user->email,
                'name_first' => $user->name_first,
                'name_last' => $user->name_last
            ]
        ]);
    }

    /**
     * Remove a user's cookie from local storage
     *
     * @return JsonResponse
     */
    public function deleteCookie(): JsonResponse
    {
        return response()->json([
            'data' => [
                'success' => true
            ]
        ])->withCookie('uuid');
    }
}
