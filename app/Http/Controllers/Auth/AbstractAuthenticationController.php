<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Validator;

class AbstractAuthenticationController extends Controller
{
    /**
     * Login a user into the network
     *
     * @param Request $request
     * @param User $user
     * @return JsonResponse
     */
    public function sendLoginSuccessResponse(Request $request, User $user): JsonResponse
    {

        /*
         * Clear and regenerate session
         */
        $request->session()->forget('two_factor_authentication');
        $request->session()->regenerate();

        /*
         * Create user UUID remember cookie
         */
        $cookie = cookie('user', json_encode([
            'uuid' => $user->uuid,
            'authed_at' => CarbonImmutable::now()
        ]), 43800);

        /*
         * Log user in
         */
        Auth::login($user, false);

        return response()->json([
            'data' => [
                'complete' => true,
                'uuid' => $user->uuid
            ]
        ])->withCookie($cookie);
    }

    /**
     * Logout a user out of the network
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function sendLogoutSuccessResponse(Request $request): JsonResponse
    {

        /*
         * Log user out
         */
        Auth::logout();

        /*
         * Regenerate session
         */
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $cookie = cookie('user_session', null, null, null, env('SESSION_PARENT_DOMAIN'));

        return response()->json([
            'data' => [
                'success' => true
            ]
        ])->withoutCookie('user')->withCookie($cookie);
    }


    /**
     * Verify a user's authentication token
     *
     * @param Request $request
     * @param $token
     * @return bool
     */
    protected function validateAuthenticationToken(Request $request, $token): bool
    {
        $details = $request->session()->get('two_factor_authentication');
        if (!$details) {
            return false;
        }

        /*
         * Check if session store contains all required values
         */
        $validator = Validator::make($details, [
            'uuid' => 'required|string',
            'token_value' => 'required|string',
            'token_expiry' => 'required'
        ]);
        if ($validator->fails()) {
            return false;
        }

        /*
         * Check if authentication token has expired
         */
        if (!$details['token_expiry'] instanceof CarbonInterface) {
            return false;
        }
        if ($details['token_expiry']->isBefore(CarbonImmutable::now())) {
            return false;
        }

        if ($token !== $details['token_value']) {
            return false;
        }

        return true;
    }
}
