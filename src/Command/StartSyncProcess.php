<?php

/**
 * Copyright (c) 8mylez GmbH. All rights reserved.
 * This file is part of software that is released under a proprietary license.
 * You must not copy, modify, distribute, make publicly available, or execute
 * its contents or parts thereof without express permission by the copyright
 * holder, unless otherwise permitted by law.
 *
 *    ( __ )____ ___  __  __/ /__  ____
 *   / __  / __ `__ \/ / / / / _ \/_  /
 *  / /_/ / / / / / / /_/ / /  __/ / /_
 *  \____/_/ /_/ /_/\__, /_/\___/ /___/
 *                 /____/
 *
 * Quote:
 * "Any fool can write code that a computer can understand.
 * Good programmers write code that humans can understand."
 * – Martin Fowler
 */

declare(strict_types=1);

namespace Emz\StagingEnvironment\Command;

use Emz\StagingEnvironment\Core\Content\StagingEnvironment\StagingEnvironmentEntity;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Emz\StagingEnvironment\Core\Content\StagingEnvironment\StagingEnvironmentCollection;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Emz\StagingEnvironment\Services\Sync\SyncServiceInterface;
use Emz\StagingEnvironment\Services\Database\DatabaseSyncServiceInterface;
use Emz\StagingEnvironment\Services\Config\ConfigUpdaterServiceInterface;
use Emz\StagingEnvironment\Command\StagingCommandHelperInterface;

class StartSyncProcess extends Command
{
    protected static $defaultName = 'emzpse:start-sync-process';

    /** @var SymfonyStyle */
    private $io;

    /** @var EntityRepositoryInterface */
    private $stagingEnvironmentRepository;

    /** @var SyncServiceInterface */
    private $syncService;

    /** @var DatabaseSyncServiceInterface */
    private $databaseSyncService;

    /** @var ConfigUpdaterServiceInterface */
    private $configUpdaterService;

    /** @var StagingCommandHelperInterface */
    private $stagingCommandHelper;

    /**
     * @var Context
     */
    private $context;

    public function __construct(
        EntityRepositoryInterface $stagingEnvironmentRepository,
        SyncServiceInterface $syncService,
        DatabaseSyncServiceInterface $databaseSyncService,
        ConfigUpdaterServiceInterface $configUpdaterService,
        StagingCommandHelperInterface $stagingCommandHelper
    )
    {
        parent::__construct();

        $this->stagingEnvironmentRepository = $stagingEnvironmentRepository;
        $this->syncService = $syncService;
        $this->databaseSyncService = $databaseSyncService;
        $this->configUpdaterService = $configUpdaterService;
        $this->stagingCommandHelper = $stagingCommandHelper;

        $this->context = Context::createDefaultContext();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io = new SymfonyStyle($input, $output);
        $helper = $this->getHelper('question');

        /** @var StagingEnvironmentCollection $stagingEnvironments */
        $stagingEnvironments = $this->stagingEnvironmentRepository->search(new Criteria(), $this->context)->getEntities();

        if (count($stagingEnvironments) <= 0) {
            throw new \RuntimeException('No staging environment available');
        }

        $question = new ChoiceQuestion('Please select a staging environment:', $this->stagingCommandHelper->getStagingEnvironmentChoices($stagingEnvironments));
        $answer = $helper->ask($input, $output, $question);

        $stagingEnvironment = $this->stagingCommandHelper->parseStagingEnvironmentAnswer($answer, $stagingEnvironments);
        if ($stagingEnvironment === null) {
            return 1;
        }

        $this->io->writeln('Starting with Job "File sync"!');
        if (!$this->syncService->syncCore($stagingEnvironment->getId(), $this->context)) {
            $this->io->error('File sync failed!');
            return 1;
        }

        $this->io->success('Job "File sync" finished successfully');
        $this->io->writeln('Starting with Job "Database sync"!');
        if (!$this->databaseSyncService->syncDatabase($stagingEnvironment->getId(), $this->context)) {
            $this->io->error('Database sync failed!');
            return 1;
        }

        $this->io->success('Job "Database sync" finished successfully');
        $this->io->writeln('Starting with Job "Config update saleschannels domains"!');
        if (!$this->configUpdaterService->setSalesChannelDomains($stagingEnvironment->getId(), $this->context)) {
            $this->io->error('Config update saleschannel domain failed!');
            return 1;
        }

        $this->io->success('Job "Config update saleschannels domains" finished successfully');
        $this->io->writeln('Starting with Job "Config update saleschannels in maintenance"!');
        if (!$this->configUpdaterService->setSalesChannelsInMaintenance($stagingEnvironment->getId(), $this->context)) {
            $this->io->error('Config update saleschannel maintenance failed!');
            return 1;
        }

        $this->io->success('Job "Config update saleschannels in maintenance" finished successfully');
        $this->io->writeln('Starting with Job "Config update create env file"!');
        if (!$this->configUpdaterService->createEnvFile($stagingEnvironment->getId(), $this->context)) {
            $this->io->error('Config update create env file failed!');
            return 1;
        }

        $this->io->success('Job "Config update create env file" finished successfully');
        $this->io->success('Staging was created successfully! Have fun!');

        return 0;
    }
}
