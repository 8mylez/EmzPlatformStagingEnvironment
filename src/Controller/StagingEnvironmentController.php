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

namespace Emz\StagingEnvironment\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Emz\StagingEnvironment\Services\Sync\SyncServiceInterface;
use Emz\StagingEnvironment\Services\Database\DatabaseSyncServiceInterface;
use Emz\StagingEnvironment\Services\Config\ConfigUpdaterServiceInterface;
use Emz\StagingEnvironment\Services\Log\LogServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Shopware\Core\Framework\Context;

/**
 * @RouteScope(scopes={"api"}) 
 */
class StagingEnvironmentController extends AbstractController
{
    /**
     * @var SyncServiceInterface
     */
    private $syncService;

    /**
     * @var DatabaseSyncServiceInterface
     */
    private $databaseSyncService;

    /**
     * @var ConfigUpdaterServiceInterface
     */
    private $configUpdaterService;

    /**
     * @var LogServiceInterface
     */
    private $logService;

    public function __construct(
        SyncServiceInterface $syncService,
        DatabaseSyncServiceInterface $databaseSyncService,
        ConfigUpdaterServiceInterface $configUpdaterService,
        LogServiceInterface $logService
    )
    {
        $this->syncService = $syncService;
        $this->databaseSyncService = $databaseSyncService;
        $this->configUpdaterService = $configUpdaterService;
        $this->logService = $logService;
    }

    /**
     * @Route("/api/v{version}/_action/emz_pse/environment/sync_files", name="api.action.emz_pse.environment.sync_files", methods={"POST"})
     */
    public function syncFiles(Request $request, Context $context): JsonResponse
    {
        if (!$request->request->has('environmentId')) {
            throw new \InvalidArgumentException('Parameter environmentId missing');
        }

        $environmentId = $request->get('environmentId');

        if ($this->syncService->syncCore($environmentId, $context)) {
            return new JsonResponse([
                "status" => true,
                "message" => "Synced all files!"
            ]);
        }
    }

    /**
     * @Route("/api/v{version}/_action/emz_pse/environment/clone_database", name="api.action.emz_pse.environment.clone_database", methods={"POST"})
     */
    public function cloneDatabase(Request $request, Context $context): JsonResponse
    {
        if (!$request->request->has('environmentId')) {
            throw new \InvalidArgumentException('Parameter environmentId missing');
        }

        $environmentId = $request->get('environmentId');
        
        if ($this->databaseSyncService->syncDatabase($environmentId, $context)) {
            return new JsonResponse([
                "status" => true,
                "message" => "Database cloned!"
            ]);
        }
    }

    /**
     * @Route("/api/v{version}/_action/emz_pse/environment/update_settings", name="api.action.emz_pse.environment.update_settings", methods={"POST"})
     */
    public function updateSettings(Request $request, Context $context): JsonResponse
    {
        if (!$request->request->has('environmentId')) {
            throw new \InvalidArgumentException('Parameter environmentId missing');
        }

        $environmentId = $request->get('environmentId');

        $done = true;
        $done = $this->configUpdaterService->setSalesChannelDomains($environmentId, $context);
        $done = $this->configUpdaterService->setSalesChannelsInMaintenance($environmentId, $context);
        $done = $this->configUpdaterService->createEnvFile($environmentId, $context);

        if ($done) {
            return new JsonResponse([
                "status" => true,
                "message" => "Updated settings!"
            ]);
        } else {
            return new JsonResponse([
                "status" => false,
                "message" => "Error updating settings!"
            ]);
        }
    }

    /**
     * @Route("/api/v{version}/_action/emz_pse/environment/get_last_sync", name="api.action.emz_pse.environment.get_last_sync", methods={"POST"})
     */
    public function getLastSync(Request $request, Context $context): JsonResponse
    {
        if (!$request->request->has('environmentId')) {
            throw new \InvalidArgumentException('Parameter environmentId missing');
        }

        $environmentId = $request->get('environmentId');

        $lastSync = $this->logService->getLastSync($environmentId, $context);

        if (!$lastSync) {
            return new JsonResponse([
                "status" => false,
                "message" => "There is no successful sync."
            ]);
        } else {
            return new JsonResponse([
                "status" => true,
                "lastSync" => $lastSync
            ]);
        }
    }
}