<?php

namespace App\Services\User;

use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class UserCreationService
{

    /**
     * @param array $data
     * @return PromiseInterface|Response
     */
    public function handle(array $data): PromiseInterface|Response
    {

        /*
         * API endpoint for user registration
         */
        $endpoint = env('API_SERVER') . '/user';

        /*
         * Request user registration from API and return response
         */
        return Http::withToken(env('API_ACCESS_KEY'))->post($endpoint, $data);
    }
}
