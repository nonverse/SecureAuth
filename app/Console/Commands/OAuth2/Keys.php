<?php

namespace App\Console\Commands\OAuth2;

use Illuminate\Console\Command;
use phpseclib3\Crypt\RSA;

class Keys extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'oauth2:keys';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate OAuth2 encryption keys';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $publicKey = './storage/oauth-public.key';
        $privateKey = './storage/oauth-private.key';

        $key = RSA::createKey(4096);

        file_put_contents($publicKey, (string)$key->getPublicKey());
        file_put_contents($privateKey, (string)$key);

        return Command::SUCCESS;
    }
}
