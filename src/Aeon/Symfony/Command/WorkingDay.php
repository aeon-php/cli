<?php

declare(strict_types=1);

namespace Aeon\Symfony\Command;

use Aeon\Calendar\Gregorian\Day;
use Aeon\Calendar\Gregorian\GregorianCalendar;
use Aeon\Calendar\Gregorian\Holidays;
use Aeon\Calendar\Gregorian\Holidays\GoogleCalendarRegionalHolidays;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class WorkingDay extends Command
{
    protected static $defaultName = 'calendar:working-day';

    protected function configure() : void
    {
        $this
            ->setDescription('Check if current day (or any other) is a working day in given country')
            ->addArgument('country-code', InputArgument::REQUIRED, 'ISO3166 2 letters country code')
            ->addOption('date', 'd', InputOption::VALUE_OPTIONAL, 'Set date, by default current date will be taken from calendar', null);
    }

    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Check if date is a holiday');

        $countryCode = \strtoupper($input->getArgument('country-code'));
        $dateString = $input->getOption('date');

        if (!\in_array($countryCode, Holidays\GoogleCalendar\CountryCodes::all(), true)) {
            $io->error('Invalid country code ' . $countryCode);

            return 1;
        }

        $date = \is_string($dateString)
            ? Day::fromString($dateString)
            : GregorianCalendar::UTC()->currentDay();

        $holidays = new GoogleCalendarRegionalHolidays($countryCode);

        if ($holidays->isHoliday($date)) {
            $io->error($date->format('Y-m-d') . ' is holiday "' . $holidays->holidaysAt($date)[0]->name() . '" in ' . $countryCode);

            return 1;
        }
        $io->success($date->format('Y-m-d') . ' is not a holiday in ' . $countryCode);

        return 0;
    }
}
