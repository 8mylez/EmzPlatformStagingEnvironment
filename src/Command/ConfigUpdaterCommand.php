<?php declare(strict_types=1);

namespace Emz\StagingEnvironment\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Emz\StagingEnvironment\Services\Config\ConfigUpdaterServiceInterface;

class ConfigUpdaterCommand extends Command
{
    /** @var ConfigUpdaterServiceInterface */
    private $configUpdaterService;

    public function __construct(ConfigUpdaterServiceInterface $configUpdaterService)
    {
        parent::__construct();
        $this->configUpdaterService = $configUpdaterService;
    }

    protected static $defaultName = 'staging:config-update';

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $result = $this->configUpdaterService->setSalesChannelDomains();

        if ($result) {
            $output->writeln('Config updated!');
        } else {
            $output->writeln('3RR3R');
        }
    }
}