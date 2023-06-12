<?php

namespace App\Http\Controllers\OAuth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use JetBrains\PhpStorm\ArrayShape;

class AbstractOAuthController
{
    /**
     * Convert PRS7 Redirect response into JSON object containing redirect URI
     *
     * @param $psrResponse
     * @return array|bool|string
     */
    #[ArrayShape(['redirect_uri' => "mixed"])] public function convertResponseToJson($psrResponse): array|bool|string
    {
        $raw = str_replace("'", "\'", json_encode($psrResponse->getHeaders()));

        return [
            'redirect_uri' => json_decode($raw, true)['Location'][0]
        ];
    }
}
