<?php

namespace App\Exceptions\Http\OAuth2;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;

class AuthorizationException extends HttpException
{
    /**
     * @var array
     */
    private array $errors;

    public function __construct(array $errors)
    {
        parent::__construct(401);
        $this->errors = $errors;
    }

    /**
     * @return JsonResponse
     */
    public function getResponse(): JsonResponse
    {
        return new JsonResponse([
            'success' => false,
            'errors' => $this->errors
        ], $this->getStatusCode());
    }

    /**
     * @return JsonResponse
     */
    public function render(): JsonResponse
    {
        return $this->getResponse();
    }
}
