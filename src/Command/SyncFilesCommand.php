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

use Emz\StagingEnvironment\Command\StagingCommandHelperInterface;
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

class SyncFilesCommand extends Command
{
    protected static $defaultName = 'emzpse:sync-files';

    /** @var SymfonyStyle */
    private $io;

    /** @var EntityRepositoryInterface */
    private $stagingEnvironmentRepository;

    /** @var SyncServiceInterface */
    private $syncService;

    /** @var StagingCommandHelperInterface */
    private $stagingCommandHelper;

    /**
     * @var Context
     */
    private $context;

    public function __construct(
        EntityRepositoryInterface $stagingEnvironmentRepository,
        SyncServiceInterface $syncService,
        StagingCommandHelperInterface $stagingCommandHelper
    )
    {
        parent::__construct();

        $this->stagingEnvironmentRepository = $stagingEnvironmentRepository;
        $this->syncService = $syncService;
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
            $this->io->error('Invalid answer');
            return 1;
        }

        $this->io->writeln('Starting with Job "File sync"!');
        if (!$this->syncService->syncCore($stagingEnvironment->getId(), $this->context)) {
            $this->io->error('File sync failed!');
            return 1;
        }

        $this->io->success('Job "File sync" finished successfully');

        return 0;
    }
}
