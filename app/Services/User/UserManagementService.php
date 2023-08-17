<?php

namespace App\Services\User;

use App\Contracts\Repository\SettingsRepositoryInterface;
use App\Models\User;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Cookie\CookieJar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Cookie;

class UserManagementService
{
    /**
     * @var SettingsRepositoryInterface
     */
    private SettingsRepositoryInterface $settingsRepository;

    /**
     * @var array
     */
    private array $updatedCookie;

    public function __construct(
        SettingsRepositoryInterface $settingsRepository
    )
    {
        $this->settingsRepository = $settingsRepository;
        $this->updatedCookie = [];
    }

    /**
     * Add a user to the local user cookie
     * This method should only be called when a user has logged themselves
     * in using their password or any other sort of login challenge
     *
     * @param Request $request
     * @param User $user
     * @return void
     */
    public function add(Request $request, User $user): void
    {
        /**
         * Get the user cookie from request
         */
        $cookie = $this->getCookie($request);

        /**
         * Get user's settings
         */
        $userSettings = [];
        foreach ($this->settingsRepository->getUserSettings($user->uuid) as $setting) {
            $userSettings[$setting['key']] = $setting['value'];
        }


        /**
         * Create new entry for user
         */
        $cookie[$user->uuid] = [
            'session' => [
                'aat' => CarbonImmutable::now(),
            ],
            'settings' => [
                'thm' => array_key_exists('theme', $userSettings) ? $userSettings['theme'] : 'system',
                'lan' => array_key_exists('language', $userSettings) ? $userSettings['language'] : 'en-AU'
            ]
        ];

        $this->updatedCookie = $cookie;
    }

    /**
     * Remove a user from the local user cookie
     * This will remove all their session data that was stored in that cookie
     *
     * @param Request $request
     * @param User $user
     * @return void
     */
    public function remove(Request $request, User $user): void
    {
        /**
         * Get user cookie from request
         */
        $cookie = $this->getCookie($request);

        /**
         * Remove user from cookie
         */
        if (array_key_exists($user->uuid, $cookie)) {
            unset($cookie[$user->uuid]);
        }

        $this->updatedCookie = $cookie;
    }

    /**
     * Add expiry to user cookie entry
     * This method should be called when a user is being timed out.
     * The user will be considered inactive and can login without
     * being challenged until the value in ['session']['exp']
     *
     * @param Request $request
     * @return void
     */
    public function remember(Request $request): void
    {
        $cookie = $this->getCookie($request);
        $user = $request->user();

        $cookie[$user->uuid] = [
            'session' => [
                ...$cookie[$user->uuid]['session'],
                'exp' => CarbonImmutable::now()->addMinutes(config('auth.user_session.remember'))
            ],
            'settings' => [
                ...$cookie[$user->uuid]['settings']
            ]
        ];

        $this->updatedCookie = $cookie;
    }

    /**
     * Check if a user is remembered in the user cookie
     *
     * @param Request $request
     * @param User $user
     * @return bool
     */
    public function isRemembered(Request $request, User $user): bool
    {
        $cookie = $this->getCookie($request);

        if (!array_key_exists($user->uuid, $cookie)) {
            return false;
        }

        if (!array_key_exists('exp', $cookie[$user->uuid]['session'])) {
            return false;
        }

        $exp = $cookie[$user->uuid]['session']['exp'];

        //TODO IDK Why this validation is not working. It is not required but pisses me off that it doesnt work
//        if (!$exp instanceof CarbonInterface) {
//            return false;
//        }

        if (CarbonImmutable::now()->isAfter($exp)) {
            return false;
        }

        return true;
    }

    /**
     * Get the cookie to attach to outgoing request
     *
     * @return Application|CookieJar|\Illuminate\Foundation\Application|Cookie
     */
    public function getResponseCookie(): \Illuminate\Foundation\Application|CookieJar|Cookie|Application
    {
        return cookie('user', json_encode($this->updatedCookie));
    }

    /**
     * Get the current user cookie
     *
     * @param Request $request
     * @return array
     */
    public function getCookie(Request $request): array
    {
        if (empty($this->updatedCookie)) {
            $this->updatedCookie = $request->cookie('user') ? json_decode($request->cookie('user'), true) : [];
        }

        return $this->updatedCookie;
    }
}
