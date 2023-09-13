<?php

declare(strict_types=1);

namespace MinVWS\AuditLogger\Tests\Loggers;

use Illuminate\Log\Logger;
use MinVWS\AuditLogger\Events\Logging\UserLoginLogEvent;
use MinVWS\AuditLogger\Loggers\PsrLogger;
use MinVWS\AuditLogger\Tests\User;
use Mockery;

class PsrLoggerTest extends Mockery\Adapter\Phpunit\MockeryTestCase
{
    public function testPsrloggerWithoutEncryption(): void
    {
        $mockLogger = Mockery::mock(Logger::class);
        $mockLogger->shouldReceive('info')->once()->withArgs(function ($args) {

            $this->assertStringStartsWith('AuditLog: ', $args);

            $parts = explode(" ", $args, 2);
            $this->assertCount(2, $parts);

            $msg = base64_decode($parts[1], true);
            $this->assertNotFalse($msg);

            $data = json_decode($msg, true, 512, JSON_THROW_ON_ERROR);
            $this->assertIsArray($data);

            $this->assertEquals(UserLoginLogEvent::EVENT_CODE, $data['event_code']);
            $this->assertArrayHasKey('foo', $data['request']);
            $this->assertArrayHasKey('bar', $data['request']);

            $this->assertEquals('12345', $data['user_id']);
            $this->assertEquals('john@example.org', $data['email']);

            // Should return a json base64 encoded string
            return true;
        });

        $user = new User();
        $user->email = "john@example.org";
        $user->id = '12345';

        $event = (new UserLoginLogEvent())
            ->withActor($user)
            ->withData(['foo' => 'bar'])
            ->withPiiData(['bar' => 'baz']);

        $service = new PsrLogger($mockLogger, false, '', '');
        $service->log($event);
    }

    public function testPsrloggerWithEncryption(): void
    {
        if (! function_exists('sodium_crypto_box_keypair')) {
            $this->markTestSkipped('No sodium detected');
        }

        $keyPair1 = sodium_crypto_box_keypair();
        $publicKey1 = sodium_crypto_box_publickey($keyPair1);
        $privateKey1 = sodium_crypto_box_secretkey($keyPair1);

        $keyPair2 = sodium_crypto_box_keypair();
        $publicKey2 = sodium_crypto_box_publickey($keyPair2);
        $privateKey2 = sodium_crypto_box_secretkey($keyPair2);

        $mockLogger = Mockery::mock(Logger::class);
        $mockLogger->shouldReceive('info')->once()->withArgs(function ($args) use ($privateKey2, $publicKey1) {
            $this->assertStringStartsWith('AuditLog: ', $args);

            $parts = explode(" ", $args, 2);
            $this->assertCount(2, $parts);

            $box = base64_decode($parts[1]);
            $nonce = substr($box, 0, SODIUM_CRYPTO_BOX_NONCEBYTES);
            $cipher = substr($box, SODIUM_CRYPTO_BOX_NONCEBYTES);

            $pair = sodium_crypto_box_keypair_from_secretkey_and_publickey($privateKey2, $publicKey1);
            $msg = sodium_crypto_box_open($cipher, $nonce, $pair);

            $data = json_decode($msg, true, 512, JSON_THROW_ON_ERROR);
            $this->assertIsArray($data);

            $this->assertEquals(UserLoginLogEvent::EVENT_CODE, $data['event_code']);
            $this->assertArrayHasKey('foo', $data['request']);
            $this->assertArrayHasKey('bar', $data['request']);

            $this->assertEquals('12345', $data['user_id']);
            $this->assertEquals('john@example.org', $data['email']);

            // Should return a json base64 encoded string
            return true;
        });

        $user = new User();
        $user->email = "john@example.org";
        $user->id = '12345';

        $event = (new UserLoginLogEvent())
            ->withActor($user)
            ->withData(['foo' => 'bar'])
            ->withPiiData(['bar' => 'baz']);

        $service = new PsrLogger($mockLogger, true, $publicKey2, $privateKey1);
        $service->log($event);
    }
}
