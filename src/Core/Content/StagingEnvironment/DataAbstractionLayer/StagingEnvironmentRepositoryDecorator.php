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

namespace Emz\StagingEnvironment\Core\Content\StagingEnvironment;

use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityWrittenContainerEvent;
use Shopware\Core\Framework\DataAbstractionLayer\Search\AggregationResult\AggregationResultCollection;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;
use Shopware\Core\Framework\DataAbstractionLayer\Search\IdSearchResult;
use Emz\StagingEnvironment\Core\Content\StagingEnvironment\Exception\StagingDataNotClearedException;
use Emz\StagingEnvironment\Services\Sync\SyncServiceInterface;
use Emz\StagingEnvironment\Services\Database\DatabaseSyncServiceInterface;

class StagingEnvironmentRepositoryDecorator implements EntityRepositoryInterface
{
    /**
     * @var EntityRepositoryInterface
     */
    private $innerRepository;

    /**
     * @var SyncServiceInterface
     */
    private $syncService;

    /**
     * @var DatabaseSyncServiceInterface
     */
    private $databaseSyncService;

    public function __construct(
        EntityRepositoryInterface $innerRepository,
        SyncServiceInterface $syncService,
        DatabaseSyncServiceInterface $databaseSyncService
    )
    {
        $this->innerRepository = $innerRepository;
        $this->syncService = $syncService;
        $this->databaseSyncService = $databaseSyncService;
    }

    /**
     * @throws StagingDataNotClearedException
     */
    public function delete(array $ids, Context $context): EntityWrittenContainerEvent
    {
        $affectedStagingEnvironments = $this->search(new Criteria(array_column($ids, 'id')), $context);

        if ($affectedStagingEnvironments->count() === 0) {
            return $this->innerRepository->delete($ids, $context);
        }

        foreach($affectedStagingEnvironments->getEntities() as $entity) {
            if ($entity->getId()) {
                if (!$this->syncService->isEmpty($entity->getId())) {
                    throw new StagingDataNotClearedException($entity->getEnvironmentName());
                }
    
                if (!$this->databaseSyncService->isEmpty($entity->getId())) {
                    throw new StagingDataNotClearedException($entity->getEnvironmentName());
                }
            }
        }

        return $this->innerRepository->delete($ids, $context);
    }

    public function aggregate(Criteria $criteria, Context $context): AggregationResultCollection
    {
        return $this->innerRepository->aggregate($criteria, $context);
    }

    public function searchIds(Criteria $criteria, Context $context): IdSearchResult
    {
        return $this->innerRepository->searchIds($criteria, $context);
    }

    public function clone(string $id, Context $context, ?string $newId = null): EntityWrittenContainerEvent
    {
        return $this->innerRepository->clone($id, $context, $newId);
    }

    public function search(Criteria $criteria, Context $context): EntitySearchResult
    {
        return $this->innerRepository->search($criteria, $context);
    }

    public function update(array $data, Context $context): EntityWrittenContainerEvent
    {
        return $this->innerRepository->update($data, $context);
    }

    public function upsert(array $data, Context $context): EntityWrittenContainerEvent
    {
        return $this->innerRepository->upsert($data, $context);
    }

    public function create(array $data, Context $context): EntityWrittenContainerEvent
    {
        return $this->innerRepository->create($data, $context);
    }

    public function createVersion(string $id, Context $context, ?string $name = null, ?string $versionId = null): string
    {
        return $this->innerRepository->createVersion($id, $context, $name, $versionId);
    }

    public function merge(string $versionId, Context $context): void
    {
        $this->innerRepository->merge($versionId, $context);
    }

    public function getDefinition(): EntityDefinition
    {
        return $this->innerRepository->getDefinition();
    }
}