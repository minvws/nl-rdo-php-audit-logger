<?php

declare(strict_types=1);

namespace MinVWS\AuditLogger\Tests\Loggers;

use Carbon\CarbonImmutable;
use MinVWS\AuditLogger\EncryptionHandler;
use MinVWS\AuditLogger\Events\Logging\UserLoginLogEvent;
use MinVWS\AuditLogger\Loggers\PsrLogger;
use MinVWS\AuditLogger\Tests\StubUser;
use MinVWS\AuditLogger\Tests\TestCase;
use Mockery;
use Psr\Log\LoggerInterface;

final class PsrLoggerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        CarbonImmutable::setTestNow(CarbonImmutable::parse('2021-01-01 12:00:00'));
    }

    public function testPsrloggerWithoutEncryption(): void
    {
        $encryptionHandler = Mockery::mock(EncryptionHandler::class);
        $encryptionHandler->shouldReceive('isEnabled')->andReturn(false);

        $mockLogger = Mockery::mock(LoggerInterface::class);
        $mockLogger->shouldReceive('info')->once()->withArgs(function ($message) {
            self::assertStringStartsWith('AuditLog: ', $message);

            $parts = explode(' ', $message, 2);
            self::assertCount(2, $parts);

            $json = json_decode($parts[1], true, 512, JSON_THROW_ON_ERROR);
            self::assertIsArray($json);

            self::assertMatchesJsonSnapshot($json);

            return true;
        });

        $event = (new UserLoginLogEvent())
            ->withActor(new StubUser())
            ->withData(['foo' => 'bar'])
            ->withPiiData(['bar' => 'baz']);

        $service = new PsrLogger($encryptionHandler, $mockLogger);
        $service->log($event);
    }

    public function testPsrloggerWithEncryption(): void
    {
        if (! function_exists('sodium_crypto_box_keypair')) {
            self::markTestSkipped('No sodium detected');
        }

        $keyPair1 = sodium_crypto_box_keypair();
        $publicKey1 = sodium_crypto_box_publickey($keyPair1);
        $privateKey1 = sodium_crypto_box_secretkey($keyPair1);

        $keyPair2 = sodium_crypto_box_keypair();
        $publicKey2 = sodium_crypto_box_publickey($keyPair2);
        $privateKey2 = sodium_crypto_box_secretkey($keyPair2);

        $encryptionHandler = new EncryptionHandler(true, base64_encode($publicKey2), base64_encode($privateKey1));

        $mockLogger = Mockery::mock(LoggerInterface::class);
        $mockLogger->shouldReceive('info')->once()->withArgs(function ($message) use ($privateKey2, $publicKey1) {
            self::assertStringStartsWith('AuditLog: ', $message);

            $parts = explode(' ', $message, 2);
            self::assertCount(2, $parts);

            $box = base64_decode($parts[1], true);
            self::assertNotFalse($box);
            $nonce = substr($box, 0, SODIUM_CRYPTO_BOX_NONCEBYTES);
            $cipher = substr($box, SODIUM_CRYPTO_BOX_NONCEBYTES);

            $pair = sodium_crypto_box_keypair_from_secretkey_and_publickey($privateKey2, $publicKey1);
            $msg = sodium_crypto_box_open($cipher, $nonce, $pair);
            self::assertNotFalse($msg);

            $data = json_decode($msg, true, 512, JSON_THROW_ON_ERROR);
            self::assertIsArray($data);

            self::assertMatchesJsonSnapshot($data);

            return true;
        });

        $event = (new UserLoginLogEvent())
            ->withActor(new StubUser())
            ->withData(['foo' => 'bar'])
            ->withPiiData(['bar' => 'baz']);

        $service = new PsrLogger($encryptionHandler, $mockLogger, logPiiData: true);
        $service->log($event);
    }
}
