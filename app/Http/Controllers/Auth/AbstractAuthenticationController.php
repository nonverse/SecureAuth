<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;

class AbstractAuthenticationController extends Controller
{
    /**
     * Retrieve the intended target URI following login
     *
     * @param $request
     * @return array
     */
    public function retrieveIntended($request): array
    {
        return array(
            'host' => $request->input('host') ?: env('BASE_APP'),
            'resource' => $request->input('resource') ?: ''
        );

    }

    /**
     * Regenerate session and redirect user to target URI
     *
     * @param Request $request
     * @return Application|RedirectResponse|Redirector
     */
    public function sendLoginSuccessResponse(Request $request)
    {
        $request->session()->regenerate();
        $intended = $this->retrieveIntended($request);

        return redirect('https://' . $intended['host'] . $intended['resource']);
    }

    /**
     * Direct a user to login page after successful logout
     *
     * @param Request $request
     * @return Application|RedirectResponse|Redirector
     */
    public function sendLogoutSuccessResponse(Request $request)
    {

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect(env('APP_URL') . '/login');
    }

    /**
     * Redirect a user to login page with intended URI as query string if unable to locate
     * an authenticated user on the system
     *
     * @param Request $request
     * @return Application|RedirectResponse|Redirector
     */
    public function sendUnauthenticatedResponse(Request $request)
    {
        $intended = $this->retrieveIntended($request);
        $loginQuery = '?host=' . $intended['host'] . '&resource=' . $intended['resource'] ?: '';

        return redirect(env('APP_URL') . '/login' . $loginQuery);
    }
}
