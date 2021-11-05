<?php

namespace App\Services\User;

use App\Contracts\Repository\UserRepositoryInterface;
use App\Models\User;
use Illuminate\Contracts\Encryption\Encrypter;
use Illuminate\Support\Str;
use PragmaRX\Google2FA\Google2FA;
use RuntimeException;
use Exception;

class TwoFactorSetupService
{
    /**
     * @var UserRepositoryInterface
     */
    private $repository;

    /**
     * @var Encrypter
     */
    private $encrypter;

    /**
     * @var Google2FA
     */
    private $google2FA;

    public function __construct(
        UserRepositoryInterface $repository,
        Encrypter               $encrypter,
        Google2FA               $google2FA
    )
    {
        $this->repository = $repository;
        $this->encrypter = $encrypter;
        $this->google2FA = $google2FA;
    }

    /**
     * Generate a TOTP secret for the currently authenticated user
     *
     * @param User $user
     * @return array
     */
    public function handle(User $user): array
    {
        $secret = '';
        try {
            $secret = $this->google2FA->generateSecretKey();
        } catch (Exception $e) {
            throw new RuntimeException($e->getMessage());
        }

        $this->repository->update($user->uuid, [
            'totp_secret' => $this->encrypter->encrypt($secret),
        ]);

        return [
            'qrcode_data' => $this->google2FA->getQRCodeUrl(
                'Nonverse',
                $user->email,
                $secret
            ),
            'secret' => $secret
        ];
    }
}
