<?php declare(strict_types=1);

namespace Emz\StagingEnvironment\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Emz\StagingEnvironment\Services\Database\DatabaseSyncServiceInterface;

class DatabaseSyncCommand extends Command
{
    /**
     * @var DatabaseSyncServiceInterface
     */
    private $databaseSyncService;

    public function __construct(DatabaseSyncServiceInterface $databaseSyncService)
    {
        parent::__construct();
        $this->databaseSyncService = $databaseSyncService;
    }

    protected static $defaultName = 'staging:dbsync';

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $result = $this->databaseSyncService->syncDatabase();
    }
}