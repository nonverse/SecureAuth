<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ApiValidationController extends Controller
{
    /**
     * @var string
     */
    private string $endpoint;

    public function __construct() {
        $this->endpoint = env('API_SERVER') . '/validator/';
    }

    /**
     * Validate an activation key via API
     *
     * @param Request $request
     * @return PromiseInterface|Response
     */
    public function activationKey(Request $request): PromiseInterface|Response
    {
        $request->validate([
            'email' => 'required|email',
            'activation_key' => 'required'
        ]);

        return Http::withToken(env('API_ACCESS_KEY'))->post($this->endpoint . 'activation-key', $request->all());
    }
}
