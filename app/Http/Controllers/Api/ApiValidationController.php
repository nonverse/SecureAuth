<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
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
     * @return JsonResponse|Response
     */
    public function activationKey(Request $request): JsonResponse|Response
    {
        $request->validate([
            'email' => 'required|email',
            'activation_key' => 'required'
        ]);

        $response = Http::withToken(env('API_ACCESS_KEY'))->post($this->endpoint . 'activation-key', $request->all());

        if ($response->failed()) {
            return new JsonResponse([
                'errors' => [
                    'activation_key' => $response['errors']['activation_key']
                ]
            ], 422);
        }

        return new JsonResponse([
            'data' => [
                'success' => true
            ]
        ]);
    }
}