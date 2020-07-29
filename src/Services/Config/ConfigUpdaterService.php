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

namespace Emz\StagingEnvironment\Services\Config;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Symfony\Component\Dotenv\Dotenv;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Emz\StagingEnvironment\Core\Content\StagingEnvironment\StagingEnvironmentEntity;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;

class ConfigUpdaterService implements ConfigUpdaterServiceInterface
{
    /** @var string */
    private $projectDir;

    /**
     * @var Connection
     */
    private $connection;

    /** @var EntityRepositoryInterface */
    private $environmentRepository;

    /** @var EntityRepositoryInterface */
    private $environmentLogRepository;

    public function __construct(
        string $projectDir,
        Connection $connection,
        EntityRepositoryInterface $environmentRepository,
        EntityRepositoryInterface $environmentLogRepository
    ) {
        $this->projectDir = $projectDir;
        $this->connection = $connection;
        $this->environmentRepository = $environmentRepository;
        $this->environmentLogRepository = $environmentLogRepository;
    }

    /**
     * Updates the domain of the staging environment and adds the subfolder
     * 
     * @param string $environmentId
     * @param Context $context
     * 
     * @return bool
     */
    public function setSalesChannelDomains(string $environmentId, Context $context): bool
    {
        /** @var StagingEnvironmentEntity */
        $environment = $this->environmentRepository
            ->search(new Criteria([$environmentId]), $context)
            ->get($environmentId);

        if (!$environment instanceof StagingEnvironmentEntity) {
            throw new \InvalidArgumentException(sprintf('Staging Environment with id %s not found environmentId missing', $environmentId));
        }
        
        if (!$environment->getFolderName()) {
            throw new \InvalidArgumentException(sprintf('Staging Environment hase no folder saved.'));
        }

        $stagingConnectionParams = $this->getStagingConnectionParams($environment);
            
        $config['folderName'] = str_replace('/', '', $environment->getFolderName());

        $stagingConnection = DriverManager::getConnection($stagingConnectionParams);

        $salesChannelUrls = $stagingConnection->executeQuery('SELECT id as id, url FROM sales_channel_domain')->fetchAll();

        foreach ($salesChannelUrls as $salesChannelUrl) {
            $salesChannelUrl['url'] = rtrim($salesChannelUrl['url'], '/') . '/' . $config['folderName'] . '/';

            $stagingConnection->executeUpdate('UPDATE sales_channel_domain SET url = :url WHERE id = :id', 
                ['url' => $salesChannelUrl['url'], 'id' => $salesChannelUrl['id']]
            );
        }

        $this->environmentLogRepository->create(
            [
                [
                    'id' => Uuid::randomHex(),
                    'environmentId' => $environmentId,
                    'state' => 'settings_saleschannel_domain_success'
                ],
            ],
            $context
        );

        return true;
    }
    
    /**
     * Sets the salesChannel of the staging environment in maintenance
     * 
     * @param string $environmentId
     * @param Context $context
     * 
     * @return bool
     */
    public function setSalesChannelsInMaintenance(string $environmentId, Context $context): bool
    {
         /** @var StagingEnvironmentEntity */
         $environment = $this->environmentRepository
            ->search(new Criteria([$environmentId]), $context)
            ->get($environmentId);

        if (!$environment instanceof StagingEnvironmentEntity) {
            throw new \InvalidArgumentException(sprintf('Staging Environment with id %s not found environmentId missing', $environmentId));
        }

        if (!$environment->getSetInMaintenance()) {
            return true;
        }

        $stagingConnectionParams = $this->getStagingConnectionParams($environment);
        
        $stagingConnection = DriverManager::getConnection($stagingConnectionParams);

        $salesChannels = $stagingConnection->executeQuery('SELECT id as id, maintenance FROM sales_channel')->fetchAll();

        foreach ($salesChannels as $salesChannel) {
            $stagingConnection->executeUpdate('UPDATE sales_channel SET maintenance = :maintenance WHERE id = :id', 
                ['maintenance' => true, 'id' => $salesChannel['id']]
            );
        }

        $this->environmentLogRepository->create(
            [
                [
                    'id' => Uuid::randomHex(),
                    'environmentId' => $environmentId,
                    'state' => 'settings_maintenance_mode_success'
                ],
            ],
            $context
        );

        return true;
    }

    /**
     * Creates the .env file with all necessary data for the staging environment
     * 
     * @param string $environmentId
     * @param Context $context
     * 
     * @return bool 
     */
    public function createEnvFile(string $environmentId, Context $context): bool
    {
        /** @var StagingEnvironmentEntity */
        $environment = $this->environmentRepository
            ->search(new Criteria([$environmentId]), $context)
            ->get($environmentId);

        if (!$environment instanceof StagingEnvironmentEntity) {
            throw new \InvalidArgumentException(sprintf('Staging Environment with id %s not found environmentId missing', $environmentId));
        }

        if (!$environment->getFolderName()) {
            throw new \InvalidArgumentException(sprintf('Staging Environment hase no folder saved.'));
        }

        $stagingConnectionParams = $this->getStagingConnectionParams($environment);
            
        $config['folderName'] = str_replace('/', '', $environment->getFolderName());

        $currentConfiguration = $_ENV;

        unset($currentConfiguration['SYMFONY_DOTENV_VARS']);
        unset($currentConfiguration['SHELL_VERBOSITY']);

        $databaseUrl = sprintf(
            'mysql://%s:%s@%s:%s/%s',
            rawurlencode($stagingConnectionParams['user']),
            rawurlencode($stagingConnectionParams['password']),
            rawurlencode($stagingConnectionParams['host']),
            rawurlencode((string) $stagingConnectionParams['port']),
            rawurlencode($stagingConnectionParams['dbname'])
        );

        $currentConfiguration['DATABASE_URL'] = $databaseUrl;
        $currentConfiguration['APP_URL'] = rtrim($currentConfiguration['APP_URL'], '/') . '/' . $config['folderName'];

        $targetConfiguration = [];

        foreach($currentConfiguration as $key => $value) {
            $targetConfiguration[] = "{$key}={$value}";
        }

        file_put_contents($this->projectDir . '/' . $config['folderName'] . '/.env', implode("\n", $targetConfiguration));

        $this->environmentLogRepository->create(
            [
                [
                    'id' => Uuid::randomHex(),
                    'environmentId' => $environmentId,
                    'state' => 'settings_env_success'
                ],
            ],
            $context
        );

        return true;
    }

    /**
     * Creates the array with the connection parameters for the staging environment
     * 
     * @param StagingEnvironmentEntity $environment
     * 
     * @return array
     */
    private function getStagingConnectionParams(StagingEnvironmentEntity $environment): array
    {
        if (!$environment->getDatabaseName()) {
            throw new \InvalidArgumentException(sprintf('Missing configuration: database name'));
        }

        if (!$environment->getDatabaseUser()) {
            throw new \InvalidArgumentException(sprintf('Missing configuration: database user'));
        }

        if (!$environment->getDatabasePassword()) {
            throw new \InvalidArgumentException(sprintf('Missing configuration: database password'));
        }

        if (!$environment->getDatabaseHost()) {
            throw new \InvalidArgumentException(sprintf('Missing configuration: database host'));
        }

        if (!$environment->getDatabasePort()) {
            throw new \InvalidArgumentException(sprintf('Missing configuration: database port'));
        }

        return [
            'dbname' => $environment->getDatabaseName(),
            'user' => $environment->getDatabaseUser(),
            'password' => $environment->getDatabasePassword(),
            'host' => $environment->getDatabaseHost(),
            'port' => $environment->getDatabasePort(),
            'driver' => 'pdo_mysql'
        ];
    }
}