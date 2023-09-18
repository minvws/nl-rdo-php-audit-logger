<?php

declare(strict_types=1);

namespace MinVWS\AuditLogger;

class EncryptionHandler
{
    protected bool $enabled;
    protected string $publicKey;
    #[\SensitiveParameter]
    protected string $privateKey;

    public function __construct(bool $enabled, string $publicKey, #[\SensitiveParameter] string $privateKey)
    {
        if (! function_exists('sodium_crypto_box')) {
            throw new \Exception('libsodium cound not found. Please install libsodium or do not use encryption in the audit_logger');
        }

        $this->enabled = $enabled;
        $this->publicKey = base64_decode($publicKey);
        $this->privateKey = base64_decode($privateKey);
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function encrypt(mixed $data): string
    {
        $data = json_encode($data, JSON_THROW_ON_ERROR);

        $pair = sodium_crypto_box_keypair_from_secretkey_and_publickey($this->privateKey, $this->publicKey);
        $nonce = random_bytes(SODIUM_CRYPTO_BOX_NONCEBYTES);
        $encrypted = sodium_crypto_box($data, $nonce, $pair);

        return base64_encode($nonce . $encrypted);
    }
}
