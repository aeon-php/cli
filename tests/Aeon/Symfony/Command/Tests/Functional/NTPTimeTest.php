<?php

declare(strict_types=1);

namespace Aeon\Symfony\Command\Tests\Functional;

use Aeon\Symfony\Command\CalendarNTPTime;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

final class NTPTimeTest extends TestCase
{
    public function test_ntp_time() : void
    {
        $commandTester = new CommandTester(
            new CalendarNTPTime()
        );

        $commandTester->execute([]);

        $this->assertSame(0, $commandTester->getStatusCode());
    }
}
