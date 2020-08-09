<?php

declare(strict_types=1);

namespace Aeon\Symfony\Command\Tests\Functional;

use Aeon\Calendar\Gregorian\GregorianCalendar;
use Aeon\Symfony\Command\WorkingDay;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

final class WorkingDayTest extends TestCase
{
    public function test_working_day_january_first() : void
    {
        $commandTester = new CommandTester(
            new WorkingDay()
        );

        $commandTester->execute([
            'country-code' => 'PL',
            '--date' => GregorianCalendar::UTC()
                ->currentYear()
                ->january()
                ->firstDay()
                ->format('Y-m-d'),
        ]);

        $this->assertSame(1, $commandTester->getStatusCode(), $commandTester->getDisplay());
    }

    public function test_working_day_january_second() : void
    {
        $commandTester = new CommandTester(
            new WorkingDay()
        );

        $commandTester->execute([
            'country-code' => 'PL',
            '--date' => GregorianCalendar::UTC()
                ->currentYear()
                ->january()
                ->firstDay()
                ->next()
                ->format('Y-m-d'),
        ]);

        $this->assertSame(0, $commandTester->getStatusCode(), $commandTester->getDisplay());
    }
}
