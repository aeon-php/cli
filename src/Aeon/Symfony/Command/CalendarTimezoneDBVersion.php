<?php

declare(strict_types=1);

namespace Aeon\Symfony\Command;

use Aeon\Calendar\Gregorian\GregorianCalendar;
use Aeon\Calendar\Stopwatch;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class CalendarTimezoneDBVersion extends Command
{
    protected static $defaultName = 'calendar:timezonedb:version';

    private HttpClientInterface $httpClient;

    public function __construct(HttpClientInterface $httpClient)
    {
        parent::__construct();
        $this->httpClient = $httpClient;
    }

    protected function configure() : void
    {
        $this
            ->setDescription('Check if the current version of timezonedb that php is using is up to date')
            ->addOption('iana-timezonedb-http', null, InputOption::VALUE_OPTIONAL, 'Url to IANA timezonedb http page', 'https://www.iana.org/time-zones')
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Assert timezonedb version but return 0 return code regardless of the result')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Checking timezonedb version');

        if ($input->getOption('dry-run')) {
            $io->note('Dry run');
        }

        $io->note('Fetching IANA timezonedb...');
        $stopwatch = new Stopwatch();
        $stopwatch->start();
        $response = $this->httpClient->request('GET', $input->getOption('iana-timezonedb-http'));
        $crawler = new Crawler($response->getContent(true));
        $stopwatch->stop();
        $io->note(\sprintf('IANA timezonedb fetched in %s seconds', $stopwatch->totalElapsedTime()->inSecondsPreciseString()));

        $timezoneDBIANAVersion = $crawler->filter('#timezone_version > #version')->first()->text();
        $timezoneDBIANARelease = $crawler->filter('#timezone_version > #date')->first()->text();
        $phpTimezoneDBVersion = 'system.0'; //\timezone_version_get();

        $io->note('IANA Latest version: ' . $timezoneDBIANAVersion);
        $io->note('IANA Release date: ' . $timezoneDBIANARelease);
        $io->note('PHP timezonedb version ' . $phpTimezoneDBVersion);

        $year = GregorianCalendar::systemDefault()->currentYear()->number();

        $versionParts = \explode('.', $phpTimezoneDBVersion);

        if ((int) $versionParts[0] !== $year) {
            $io->error('timezonedb is out of date, consider using `sudo pecl install timezonedb` to update it or upgrade your php version.');

            if ($input->getOption('dry-run')) {
                return 0;
            }

            return 1;
        }

        $alphabet = \range('a', 'z');
        $phpVersionLetter = $alphabet[$versionParts[1] - 1];

        if (\sprintf('%d%s', $year, $phpVersionLetter) !== $timezoneDBIANAVersion) {
            $io->error('timezonedb is out of date, consider using `sudo pecl install timezonedb` to update it or upgrade your php version.');

            if ($input->getOption('dry-run')) {
                return 0;
            }

            return 1;
        }

        $io->success('timezonedb is up to date');

        return 0;
    }
}
