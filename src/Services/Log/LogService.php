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
 *              /____/              
 * 
 * Quote: 
 * "Any fool can write code that a computer can understand. 
 * Good programmers write code that humans can understand." 
 * – Martin Fowler
 */

declare(strict_types=1);

namespace Emz\StagingEnvironment\Services\Log;

use Emz\StagingEnvironment\Services\Log\LogServiceInterface;
use Symfony\Component\Filesystem\Filesystem;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\Context;
use Emz\StagingEnvironment\Core\Content\StagingEnvironment\StagingEnvironmentLogEntity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;

class LogService implements LogServiceInterface
{
    /** @var EntityRepositoryInterface */
    private $environmentLogRepository;

    public function __construct(
        EntityRepositoryInterface $environmentLogRepository
    ){
        $this->environmentLogRepository = $environmentLogRepository;
    }

    /**
     * Looks for the last successful sync of the requested environment
     * 
     * @param string $environmentId
     * @param Context $context
     * 
     * @return string
     */
    public function getLastSync(string $environmentId, Context $context): string
    {
        $environmentLogCriteria = new Criteria();
        $environmentLogCriteria->addFilter(new EqualsFilter('environmentId', $environmentId));
        $environmentLogCriteria->addFilter(new EqualsFilter('environmentId', $environmentId));

        /** @var StagingEnvironmentLogEntity */
        $environmentLogs = $this->environmentLogRepository
            ->search($environmentLogCriteria, $context)
            ->getEntities();

        if (!$environmentLogs) {
            return false;
        }

        $lastSync = '2020-07-26 21:25:59.4';

        return $lastSync;
    }
}