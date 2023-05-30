<?php

namespace App\Services\User;

use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class UserCreationService
{

    /**
     * @param array $data
     * @return PromiseInterface|Response|bool
     */
    public function handle(array $data): PromiseInterface|Response|bool
    {

        /*
         * API endpoint for user registration
         */
        $endpoint = env('API_SERVER') . '/user';

        /*
         * Request user registration from API and return response
         */
        $response = Http::withToken(env('API_ACCESS_KEY'))->post($endpoint, $data);

        if (!$response->successful()) {
            return false;
        }

        return $response;
    }
}
