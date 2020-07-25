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
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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

    public function __construct(
        SyncServiceInterface $syncService,
        DatabaseSyncServiceInterface $databaseSyncService,
        ConfigUpdaterServiceInterface $configUpdaterService
    )
    {
        $this->syncService = $syncService;
        $this->databaseSyncService = $databaseSyncService;
        $this->configUpdaterService = $configUpdaterService;
    }

    /**
     * @Route("/api/v{version}/_action/emz_pse/environment/sync_files", name="api.action.emz_pse.environment.sync_files", methods={"POST"})
     */
    public function syncFiles(Request $request): JsonResponse
    {
        $folderName = $request->get('folderName');

        if ($this->syncService->syncCore($folderName)) {
            return new JsonResponse([
                "status" => true,
                "message" => "Synced all files!"
            ]);
        }
    }

    /**
     * @Route("/api/v{version}/_action/emz_pse/environment/clone_database", name="api.action.emz_pse.environment.clone_database", methods={"POST"})
     */
    public function cloneDatabase(Request $request): JsonResponse
    {
        $databaseName = $request->get('databaseName');
        $databaseUser = $request->get('databaseUser');
        $databasePassword = $request->get('databasePassword');
        $databaseHost = $request->get('databaseHost');
        $databasePort = $request->get('databasePort');
        
        if ($this->databaseSyncService->syncDatabase($databaseName, $databaseUser, $databasePassword, $databaseHost, $databasePort)) {
            return new JsonResponse([
                "status" => true,
                "message" => "Database cloned!"
            ]);
        }
    }

    /**
     * @Route("/api/v{version}/_action/emz_pse/environment/update_settings", name="api.action.emz_pse.environment.update_settings", methods={"POST"})
     */
    public function updateSettings(Request $request): JsonResponse
    {
        $config = [];
        $config['folderName'] = $request->get('folderName');
        $config['databaseName'] = $request->get('databaseName');
        $config['databaseUser'] = $request->get('databaseUser');
        $config['databasePassword'] = $request->get('databasePassword');
        $config['databaseHost'] = $request->get('databaseHost');
        $config['databasePort'] = $request->get('databasePort');

        $done = true;
        $done = $this->configUpdaterService->setSalesChannelDomains($config);
        $done = $this->configUpdaterService->setSalesChannelsInMaintenance($config);
        $done = $this->configUpdaterService->createEnvFile($config);

        if ($done) {
            return new JsonResponse([
                "status" => true,
                "message" => "Updated settings!"
            ]);
        }
    }
}