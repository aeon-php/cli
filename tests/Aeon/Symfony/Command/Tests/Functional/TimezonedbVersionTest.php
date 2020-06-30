<?php

declare(strict_types=1);

namespace Aeon\Symfony\Command\Tests\Functional;

use Aeon\Symfony\Command\CalendarTimezoneDBVersion;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\HttpClient\HttpClient;

final class TimezonedbVersionTest extends TestCase
{
    public function test_ntp_time() : void
    {
        $commandTester = new CommandTester(
            new CalendarTimezoneDBVersion(HttpClient::create())
        );

        $commandTester->execute([], ['--dry-run']);

        $this->assertSame(0, $commandTester->getStatusCode(), $commandTester->getDisplay());
    }
}
