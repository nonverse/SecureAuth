<?php

namespace App\Console\Commands\OAuth2;

use App\Services\OAuth\Client\CreateClientService;
use Illuminate\Console\Command;

class Client extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'oauth2:client {name} {redirect}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create new OAuth2 client';

    /**
     * @var CreateClientService
     */
    private CreateClientService $creationService;

    public function __construct(
        CreateClientService $creationService
    )
    {
        $this->creationService = $creationService;
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $client = $this->creationService->handle([
            'name' => $this->argument('name'),
            'redirect' => $this->argument('redirect')
        ]);

        $this->line('Client ID: ' . $client['client_id']);
        $this->line('Client Secret: ' . $client['client_secret']);
        return Command::SUCCESS;
    }
}
