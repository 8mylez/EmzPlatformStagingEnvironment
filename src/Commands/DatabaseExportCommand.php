<?php declare(strict_types = 1);

namespace Emz\StagingEnvironment\Commands;

use Emz\StagingEnvironment\Services\Database\DatabaseServiceInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DatabaseExportCommand extends Command
{
    protected static $defaultName = 'emz:sd:database:export';

    /**
     * @var DatabaseServiceInterface
     */
    private $databaseService;

    public function __construct(
        DatabaseServiceInterface $databaseService
    ) {
        parent::__construct();

        $this->databaseService = $databaseService;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Start export');

        $tables = $this->databaseService->getAllTableNames();

        foreach ($tables as $table) {
            $output->writeln("Sync schema for table: {$table}");
            $result = $this->databaseService->syncTableSchema($table);
            $result = $result ? "success" : "error";
            $output->writeln("Result: {$result}");
            $output->writeln("");
        }

        foreach ($tables as $table) {
            $output->writeln("Sync data for table: {$table}");
            $result = $this->databaseService->syncTableData($table);
            $result = $result ? "success" : "error";
            $output->writeln("Result: {$result}");
            $output->writeln("");
        }
    }
}
