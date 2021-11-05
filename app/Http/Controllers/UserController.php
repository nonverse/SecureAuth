<?php

namespace App\Http\Controllers;

use App\Contracts\Repository\UserRepositoryInterface;
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
     * Check if a email provided belongs to a valid user instance
     *
     * @param Request $request
     * @return JsonResponse|Response
     */
    function verify(Request $request)
    {
        // Check if a email was provided in the request and
        // verify that the email has a corresponding user instance
        try {
            $email = $request->input('email');

            /**
             * @var User
             */
            $user = $this->repository->get($email);
        } catch (ModelNotFoundException $e) {
            return response('Unable to find user', 400);
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
     * Check if a user is authenticated on the system
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function gets(Request $request): JsonResponse
    {
        return new JsonResponse([
            'data' => [
                'authenticated' => true,
                'uuid' => $request->user()->uuid
            ]
        ]);
    }
}
