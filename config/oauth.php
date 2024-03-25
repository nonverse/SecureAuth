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
     * Supported (enabled) response types
     */
    'response_types' => [
        'code',
    ],

    /**
     * Authorization code configuration
     */
    'authorization_code' => [
        'expiry' => 1 //(Minutes)
    ],

    /**
     * Application redirect URIs that should skip the
     * authorization prompt
     */
    'skip_prompt' => [
        env('VITE_ACCOUNT_APP') . '/',
    ],

    /**
     * Access token configuration
     */
    'access_tokens' => [
        'expiry' => 30 //(Minutes)
    ],

    /**
     * Refresh token configuration
     */
    'refresh_tokens' => [
        'expiry' => 1051200 //(Minutes)
    ],
];
