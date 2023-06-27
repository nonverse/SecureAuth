<?php

namespace App\Http\Controllers\Recovery;

use App\Contracts\Repository\UserRepositoryInterface;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Passwords\PasswordBroker;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PasswordRecoveryController extends Controller
{
    /**
     * @var UserRepositoryInterface
     */
    private UserRepositoryInterface $repository;

    /**
     * @var PasswordBroker
     */
    private PasswordBroker $broker;

    /**
     * @var Hasher
     */
    private Hasher $hasher;

    public function __construct(
        UserRepositoryInterface $repository,
        PasswordBroker          $broker,
        Hasher                  $hasher
    )
    {
        $this->repository = $repository;
        $this->broker = $broker;
        $this->hasher = $hasher;
    }

    /**
     * Send password reset email to user
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function forgot(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        if (!$this->repository->get($request->input('email'))) {
            return new JsonResponse([
                'data' => [
                    'success' => false
                ],
                'error' => [
                    'User not found'
                ]
            ], 404);
        }

        $reset = $this->broker->sendResetLink($request->only('email'));

        if ($reset !== $this->broker::RESET_LINK_SENT) {
            return new JsonResponse([
                'errors' => [
                    'email' => __($reset)
                ]
            ], 400);
        }

        return new JsonResponse([
            'data' => [
                'success' => true
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
            'password' => 'required|confirmed'
        ]);

        /*
         * Check if password contains user's personal info
         */
        $password = $request->input('password');
        $user = $this->repository->get($request->input('email'));
        $restricted = strtolower($user->name_first . $user->name_last . $user->username . $user->email);
        if (str_contains($restricted, $request->input('password'))) {
            return new JsonResponse([
                'success' => false,
                'errors' => [
                    'password' => 'Password cannot contain personal info'
                ]
            ], 422);
        }

        /*
         * Reset user's password
         */
        $status = $this->broker->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $hash = $this->hasher->make($password);
                $this->repository->update($user->uuid, [
                    'password' => $hash
                ]);
            }
        );

        if ($status !== $this->broker::PASSWORD_RESET) {
            return new JsonResponse([
                'success' => false,
                'errors' => [
                    'password' => __($status)
                ]
            ], 400);
        }

        return new JsonResponse([
            'data' => [
                'success' => true
            ]
        ]);
    }
}
