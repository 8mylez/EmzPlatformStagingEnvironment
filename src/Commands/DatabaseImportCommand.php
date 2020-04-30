<?php declare(strict_types = 1);

namespace Emz\StagingEnvironment\Commands;

use Emz\StagingEnvironment\Services\Database\DatabaseServiceInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DatabaseImportCommand extends Command
{
    protected static $defaultName = 'emz:sd:database:import';

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
        $output->writeln('Start import');
    }
}
