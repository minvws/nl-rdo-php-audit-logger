<?php

declare(strict_types=1);

namespace MinVWS\AuditLogger;

use RuntimeException;
use SensitiveParameter;

final class EncryptionHandler
{
    protected string $publicKey;
    protected string $privateKey;

    public function __construct(
        protected bool $enabled,
        string $publicKey,
        #[SensitiveParameter]
        string $privateKey,
    ) {
        if (! function_exists('sodium_crypto_box')) {
            throw new \Exception(
                'libsodium cound not found. Please install libsodium or do not use encryption in the audit_logger',
            );
        }

        $publicKey = base64_decode($publicKey, true);
        $privateKey = base64_decode($privateKey, true);

        if ($publicKey === false || $privateKey === false) {
            throw new RuntimeException('Invalid public or private key given');
        }

        $this->publicKey = $publicKey;
        $this->privateKey = $privateKey;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function encrypt(string $data): string
    {
        $pair = sodium_crypto_box_keypair_from_secretkey_and_publickey($this->privateKey, $this->publicKey);
        $nonce = random_bytes(SODIUM_CRYPTO_BOX_NONCEBYTES);
        $encrypted = sodium_crypto_box($data, $nonce, $pair);

        return base64_encode($nonce . $encrypted);
    }
}
