<?php

namespace App\Http\Controllers\Auth;

use App\Contracts\Repository\SettingsRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\User\UserManagementService;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AbstractAuthenticationController extends Controller
{
    /**
     * @var SettingsRepositoryInterface
     */
    private SettingsRepositoryInterface $settingsRepository;

    /**
     * @var UserManagementService
     */
    private UserManagementService $userManagementService;

    public function __construct(
        SettingsRepositoryInterface $settingsRepository,
        UserManagementService       $userManagementService
    )
    {
        $this->settingsRepository = $settingsRepository;
        $this->userManagementService = $userManagementService;
    }

    /**
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
         * Add user to cookie
         */
        $this->userManagementService->add($request, $user);

        $settings = [];
        foreach ($this->settingsRepository->getUserSettings($user->uuid) as $setting) {
            $settings[$setting['key']] = $setting['value'];
        }

        /*
         * Create settings cookie containing user's basic settings
         */
        $settingsCookie = cookie('settings', json_encode([
            'theme' => array_key_exists('theme', $settings) ? $settings['theme'] : 'system',
            'language' => array_key_exists('language', $settings) ? $settings['language'] : 'en-AU'
        ]), 43800, null, env('SESSION_PARENT_DOMAIN'), false, false);

        /*
         * Log user in
         */
        Auth::login($user, false);

        return response()->json([
            'complete' => true,
            'data' => [
                'uuid' => $user->uuid
            ]
        ])->withCookie($this->userManagementService->getResponseCookie())->withCookie($settingsCookie);
    }


    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function sendLogoutSuccessResponse(Request $request): JsonResponse
    {
        /**
         * Remove user from cookie
         */
        $this->userManagementService->remove($request, $request->user());

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
        $settingsCookie = cookie('settings', null, null, null, env('SESSION_PARENT_DOMAIN'), false, false);

        return response()->json([
            'data' => [
                'success' => true
            ]
        ])->withCookie($this->userManagementService->getResponseCookie())->withCookie($cookie)->withCookie($settingsCookie);
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
