<?php

namespace App\Services\Auth;

use Carbon\CarbonImmutable;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use JetBrains\PhpStorm\ArrayShape;

class AuthorizationService
{
    /**
     * @var Hasher
     */
    private Hasher $hasher;

    public function __construct(
        Hasher $hasher,
    )
    {
        $this->hasher = $hasher;
    }

    /**
     * @param Request $request
     * @return array
     */
    #[ArrayShape(['success' => "bool", 'authorization_token' => "array"])]
    public function handle(Request $request): array
    {

        /**
         * Generate a 64 character authorization token
         */
        $token = Str::random(64);
        $tokenData = [
            'token_expires' => CarbonImmutable::now()->addMinutes(5),
            'action_id' => $request->input('action_id')
        ];

        /**
         * Store token hash in session
         */
        $request->session()->put('authorization_token', [
            'token_hash' => $this->hasher->make($token),
            ...$tokenData
        ]);

        /**
         * Return token as array
         */
        return [
            'success' => true,
            'authorization_token' => [
                'token_value' => $token,
                ...$tokenData
            ]
        ];
    }

    /**
     * @param Request $request
     * @return array
     */
    public function verify(Request $request): array
    {
        /**
         * Get authorization token from session
         */
        if ($request->session()->get('authorization_token')) {
            $token = $request->session()->get('authorization_token');
        } else {
            return [
                'success' => false,
                'error' => 'Authorization token not found'
            ];
        }

        /**
         * Check if authorization token has expired
         */
        if ($token['token_expiry']->isBefore(CarbonImmutable::now())) {
            return [
                'success' => false,
                'error' => 'Authorization token has expired'
            ];
        }

        /**
         * Check if authorized action matches requested action
         */
        if (!$token['action_id'] === $request->input('action_id')) {
            return [
                'success' => false,
                'error' => 'Invalid authorization token'
            ];
        }

        /**
         * Check if correct authorization token was provided
         */
        if (!$this->hasher->check($request->input('authorization_token'), $token['token_value'])) {
            return [
                'success' => false,
                'error' => 'Invalid authorization token'
            ];
        }

        /**
         * If all tests pass, return success with authorized action
         */
        return [
            'success' => true,
            'data' => [
                'action_id' => $request->input('action_id')
            ]
        ];
    }
}
