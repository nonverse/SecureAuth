<?php

namespace App\Http\Controllers\OAuth2;

use App\Contracts\Repository\OAuth2\ClientRepositoryInterface;
use App\Contracts\Repository\OAuth2\ScopeRepositoryInterface;
use App\Exceptions\Http\OAuth2\AuthorizationException;
use App\Exceptions\Http\ValidationException;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AuthorizationController extends Controller
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
        ScopeRepositoryInterface  $scopeRepository
    )
    {
        $this->clientRepository = $clientRepository;
        $this->scopeRepository = $scopeRepository;
    }

    /**
     * Handle request to show client details on authorization prompt
     *
     * @param Request $request
     * @return JsonResponse|string
     */
    public function show(Request $request): JsonResponse|string
    {
        /**
         * Validate the authorization request
         */
        $client = $this->validateAuthorizationRequest($request);

        /**
         * Automatically approve the request if the client is configured to 'skip_prompt'
         */
        if (in_array(array_pop($client), config('oauth.skip_prompt'))) {
            // return $this->>approve()
        }

        /**
         * Return the client's name and scope as a JSON response
         */
        return new JsonResponse([
            'data' => $client
        ]);
    }

    /**
     * Validate an incoming authorization request
     *
     * @param Request $request
     * @return string[]
     */
    protected function validateAuthorizationRequest(Request $request): array
    {
        /**
         * Validate request input
         */
        $validator = Validator::make($request->all(), [
            'response_type' => 'required',
            'client_id' => 'required',
            'redirect_uri' => 'required',
            'scope' => 'required',
        ]);

        /**
         * Throw ValidationException if input validation fails
         */
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        try {
            /**
             * Get the client details from database
             */
            $client = $this->clientRepository->get($request->input('client_id'));
        } catch (ModelNotFoundException) {
            throw new AuthorizationException([
                'client_id' => 'Client does not exist'
            ]);
        }

        /**
         * Check if client is revoked
         */
        if ($client->revoked) {
            throw new AuthorizationException([
                'client_id' => 'Client has been revoked'
            ]);
        }

        /**
         * Check if request redirect_uri matches that of the client
         */
        if ($client->redirect !== $request->input('redirect_uri')) {
            throw new AuthorizationException([
                'redirect_uri' => 'Failed to validate redirect uri'
            ]);
        }

        try {
            /**
             * Check that each requested scope exists and get their description
             */
            $scope = [];
            foreach (explode(' ', $request->input('scope')) as $scopeId) {
                $scope[] = $this->scopeRepository->get($scopeId);
            }
        } catch (ModelNotFoundException) {
            throw new AuthorizationException([
                'scope' => "Scope '" . $scopeId . "' does not exist"
            ]);
        }

        //TODO Might need better scoping system (Will be done when new node system is built)
        //TODO Verify user scopes ("")

        /**
         * Return basic client details
         */
        return [
            'name' => $client->name,
            'scope' => $scope,
            'redirect_uri' => $client->redirect,
        ];
    }
}
