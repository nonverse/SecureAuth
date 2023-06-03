<?php

return [
    /**
     * RS256 Private key file
     */
    'private_key' => file_get_contents('../storage/oauth-private.key'),

    /**
     * RS256 Public key file
     */
    'public_key' => file_get_contents('../storage/oauth-public.key'),

    /**
     * Supported (enabled) grant types
     */
    'grant_types' => [
        'code',
    ],

    'access_tokens' => [
        'expiry' => 30
    ]
];
