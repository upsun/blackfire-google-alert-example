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

use App\Services\BlackfireGoogleAlert;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'blackfire:import-google-alerts',
    description: 'Import Google RSS Feed and spot corresponding Blackfire Markers on your timeline',
)]
class GoogleAlertToBlackfireMarkerCommand extends Command
{
    public function __construct(
        private BlackfireGoogleAlert $blackfireGoogleAlerts,
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

        $count = $this->blackfireGoogleAlerts->processRssFeed();

        $io->success("$count Feeds imported");

        return Command::SUCCESS;
    }
}
