<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\Services\BlackfireGoogleAlerts;

#[AsCommand(
    name: 'blackfire:import-google-alerts',
    description: 'Import Google RSS Feed and spot corresponding Blackfire Marjers on your timeline',
)]
class GoogleAlertToBlackfireCommand extends Command
{
    public function __construct(
        private BlackfireGoogleAlerts $blackfireGoogleAlerts,
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
    }
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $this->blackfireGoogleAlerts->processRssFeed();

        $io->success('Feeds imported');

        return Command::SUCCESS;
    }
}
