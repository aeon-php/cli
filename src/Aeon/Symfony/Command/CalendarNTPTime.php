<?php

declare(strict_types=1);

namespace Aeon\Symfony\Command;

use Aeon\Calendar\Gregorian\DateTime;
use Aeon\Calendar\Gregorian\GregorianCalendar;
use Aeon\Calendar\Stopwatch;
use Aeon\Calendar\TimeUnit;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class CalendarNTPTime extends Command
{
    protected static $defaultName = 'calendar:ntp:time';

    protected function configure() : void
    {
        $this
            ->setDescription('Get ntp time and optionally compare it with php local time')
            ->addOption('server', 's', InputOption::VALUE_OPTIONAL, 'NTP Server url', 'pool.ntp.org')
            ->addOption('port', 'p', InputOption::VALUE_OPTIONAL, 'NTP Server port', 123)
            ->addOption('compare', 'c', InputOption::VALUE_NONE, 'Compare NTP time with local time')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Get NTP Time');


        $io->note(\sprintf('Connecting to ntp server udp://%s:%d...', $input->getOption('server'), $input->getOption('port')));
        $stopwatch = new Stopwatch();
        $stopwatch->start();

        $socket = \stream_socket_client(
            \sprintf(
                'udp://%s:%d',
                $input->getOption('server'),
                $input->getOption('port')
            ),
            $errorNumber,
            $errorMessage,
            TimeUnit::seconds(10)->inSeconds()
        );
        \fwrite($socket, \chr(0x23) . \str_repeat(chr(0x00), 47));
        $response = fread($socket, 48);
        $stopwatch->stop();

        \fclose($socket);

        $io->note(\sprintf('Received response from NTP server in %s seconds', $stopwatch->totalElapsedTime()->inSecondsPreciseString()));

        $unpackedResponse = @\unpack('N12', $response);

        if (!$unpackedResponse) {
            $io->error('Invalid NTP server response');

            return 1;
        }

        $referenceTime = DateTime::fromString('1900-01-01 00:00:00');
        $unixTime = DateTime::fromString('1970-01-01 00:00:00');

        $ntpTime = DateTime::fromTimestampUnix($unpackedResponse[9])
            ->sub($referenceTime->until($unixTime)->distance());

        $diff = $ntpTime->until($systemTime = GregorianCalendar::systemDefault()->now())->distance();

        $io->note('NTP Server Unix Timestamp: ' . $ntpTime->timestampUNIX()->inSeconds() . ' ' . $ntpTime->toISO8601());
        $io->note('System Unix Timestamp: ' . $systemTime->timestampUNIX()->inSeconds() . ' ' . $systemTime->toISO8601());

        if ($input->getOption('compare')) {
            if ($diff->inSecondsAbs() > 0) {
                $io->error(\sprintf('Your server time is probably not synchronized with NTP, difference %d seconds', $diff->inSeconds()));

                return 1;
            }

            $io->success('Your server time sees to be synchronized.');
        }

        return 0;
    }
}
