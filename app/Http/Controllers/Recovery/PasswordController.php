<?php

namespace App\Http\Controllers\Recovery;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Auth\Passwords\PasswordBroker;

class PasswordController extends Controller
{
    /**
     * @var PasswordBroker
     */
    private $broker;

    public function __construct(
        PasswordBroker $broker
    ) {
        $this->broker = $broker;
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
}
