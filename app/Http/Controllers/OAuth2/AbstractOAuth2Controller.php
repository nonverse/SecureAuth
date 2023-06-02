<?php

namespace App\Http\Controllers\OAuth2;

use App\Contracts\Repository\OAuth2\ClientRepositoryInterface;
use App\Contracts\Repository\OAuth2\ScopeRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Services\OAuth\AuthCode\CreateAuthCodeService;
use Exception;
use Illuminate\Http\Request;

class AbstractOAuth2Controller extends Controller
{
    /**
     * @var ClientRepositoryInterface
     */
    private ClientRepositoryInterface $clientRepository;

    /**
     * @var ScopeRepositoryInterface
     */
    private ScopeRepositoryInterface $scopeRepository;

    public function __construct(
        ClientRepositoryInterface $clientRepository,
        ScopeRepositoryInterface  $scopeRepository,
    )
    {
        $this->clientRepository = $clientRepository;
        $this->scopeRepository = $scopeRepository;
    }

    /**
     * Validate client
     *
     * @param Request $request
     * @return array
     * @throws Exception
     */
    public function validateAuthorizationRequest(Request $request): array
    {
        $request->validate([
            'client_id' => 'required',
            'redirect_uri' => 'required',
            'response_type' => 'required',
        ]);

        $errors = [];
        try {
            $client = $this->clientRepository->get($request->input('client_id'));
        } catch (Exception $e) {
            return [
                'client_id' => 'Client not found'
            ];
        }

        if ($client->revoked) {
            $errors['client_id'] = 'Invalid client';
        }

        if ($client->redirect !== $request->input('redirect_uri')) {
            $errors['redirect_uri'] = 'Unable to validate redirect_uri';
        }

        if ($request->input('scopes')) {
            $scopes = explode(' ', $request->input('scopes'));

            try {
                $this->scopeRepository->getScopesById($scopes);
            } catch (Exception $e) {
                $errors['scopes'] = $e->getMessage();
            }
        }

        return $errors;
    }
}
