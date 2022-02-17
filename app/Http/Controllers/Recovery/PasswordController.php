<?php

namespace App\Http\Controllers\Recovery;

use App\Contracts\Repository\UserRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Notifications\PasswordReset;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Events\Dispatcher;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Auth\Passwords\PasswordBroker;

class PasswordController extends Controller
{
    /**
     * @var UserRepositoryInterface
     */
    private $repository;

    /**
     * @var PasswordBroker
     */
    private $broker;

    /**
     * @var Hasher
     */
    private $hasher;

    private $dispatcher;

    public function __construct(
        UserRepositoryInterface $repository,
        PasswordBroker          $broker,
        Hasher                  $hasher,
        Dispatcher              $dispatcher
    )
    {
        $this->repository = $repository;
        $this->broker = $broker;
        $this->hasher = $hasher;
        $this->dispatcher = $dispatcher;
    }

    /**
     * Send a link to reset a user's password
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function forgot(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email'
        ]);
        $status = $this->broker->sendResetLink($request->only('email'));

        if ($status !== $this->broker::RESET_LINK_SENT) {
            return new JsonResponse([
                'data' => [
                    'success' => false,
                    'error' => __($status),
                ]
            ]);
        }

        return new JsonResponse([
            'data' => [
                'success' => true,
            ]
        ]);
    }

    /**
     * Reset a user's password
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function reset(Request $request): JsonResponse
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed'
        ]);

        // Check if a user's password contains any part of their name(s)
        $password = $request->input('password');
        $user = $this->repository->get($request->input('email'));
        if (str_contains($password, $user->name_first) || str_contains($password, $user->name_last)) {
            return new JsonResponse([
                'errors' => [
                    'password' => 'Password cannot contain your name'
                ]
            ], 422);
        }

        $status = $this->broker->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user = $this->repository->update($user->uuid, [
                    'password' => $this->hasher->make($password)
                ]);

                $this->dispatcher->dispatch(new PasswordReset($user));
            }
        );

        if ($status !== $this->broker::PASSWORD_RESET) {
            return new JsonResponse([
                'data' => [
                    'success' => false,
                ],
                'errors' => [
                    'password' => __($status)
                ]
            ]);
        }

        return new JsonResponse([
            'data' => [
                'success' => true
            ]
        ]);
    }
}
