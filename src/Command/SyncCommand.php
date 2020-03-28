<?php declare(strict_types=1);

namespace Emz\StagingEnvironment\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Emz\StagingEnvironment\Services\Sync\SyncServiceInterface;

class SyncCommand extends Command
{
    /** @var SyncServiceInterface */
    private $syncService;

    public function __construct(SyncServiceInterface $syncService)
    {
        parent::__construct();
        $this->syncService = $syncService;
    }

    protected static $defaultName = 'staging:sync';

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $result = $this->syncService->syncCore();

        if ($result) {
            $output->writeln('SYNCED!');
        } else {
            $output->writeln('3RR4R');
        }
    }
}