<?php

namespace App\Services\Auth;

use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use JetBrains\PhpStorm\ArrayShape;

class PasswordConfirmationService
{
    /**
     * Issue
     *
     * @param Request $request
     * @param User $user
     * @param string|null $authenticates
     * @return array
     */
    #[ArrayShape(['confirmation_token' => "string", 'token_expiry' => "\Carbon\CarbonImmutable"])]
    public function handle(Request $request, User $user, string $authenticates = null): array
    {
        /*
         * Generate 64 character confirmation token
         */
        $token = Str::random(64);

        $request->session()->put('password_confirmation_token', [
            'uuid' => $user->uuid,
            'token_value' => $token,
            'token_expiry' => CarbonImmutable::now()->addMinutes(5),
            'authenticates' => $authenticates
        ]);

        return [
            'confirmation_token' => $token,
            'token_expiry' => CarbonImmutable::now()->addMinutes(5)
        ];
    }
}
