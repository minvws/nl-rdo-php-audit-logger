<?php

declare(strict_types=1);

namespace MinVWS\AuditLogger\Tests;

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase as BaseTestCase;
use Spatie\Snapshots\MatchesSnapshots;

class TestCase extends BaseTestCase
{
    use MockeryPHPUnitIntegration;
    use MatchesSnapshots;

    protected function assertSnapshotShouldBeCreated(string $snapshotFileName): void
    {
        if ($this->shouldCreateSnapshots()) {
            return;
        }

        static::fail(
            "Snapshot \"$snapshotFileName\" does not exist.\n" .
            'You can automatically create it by running "composer update-test-snapshots".',
        );
    }
}
