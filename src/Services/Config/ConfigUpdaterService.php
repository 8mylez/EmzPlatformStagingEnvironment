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
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\Context;
use Emz\StagingEnvironment\Core\Content\StagingEnvironment\StagingEnvironmentProfileEntity;

class ConfigUpdaterService implements ConfigUpdaterServiceInterface
{
    /** @var string */
    private $projectDir;

    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var EntityRepositoryInterface
     */
    private $profileRepository;

    public function __construct(
        string $projectDir,
        Connection $connection,
        EntityRepositoryInterface $profileRepository
    ) {
        $this->projectDir = $projectDir;
        $this->connection = $connection;
        $this->profileRepository = $profileRepository;
    }

    /**
     * Updates the domain of the staging environment and adds the subfolder
     * 
     * @param string $selectedProfile
     * 
     * @return bool
     */
    public function setSalesChannelDomains(string $selectedProfileId): bool
    {
        $stagingConnectionParams = [
            'dbname' => 'staging',
            'user' => 'user',
            'password' => 'password',
            'host' => 'localhost',
            'driver' => 'pdo_mysql',
        ];

        $config = [
            'folderName' => 'emzstaging'
        ];

        $selectedProfile = $this->getSelectedProfile($selectedProfileId);
        
        if ($selectedProfile) {
            $stagingConnectionParams = $this->getStagingConnectionParams($selectedProfile);
            
            $config['folderName'] = str_replace('/', '', $selectedProfile->get('folderName'));
        }

        $stagingConnection = DriverManager::getConnection($stagingConnectionParams);

        $salesChannelUrls = $stagingConnection->executeQuery('SELECT id as id, url FROM sales_channel_domain')->fetchAll();

        foreach ($salesChannelUrls as $salesChannelUrl) {
            $salesChannelUrl['url'] = rtrim($salesChannelUrl['url'], '/') . '/' . $config['folderName'];

            $stagingConnection->executeUpdate('UPDATE sales_channel_domain SET url = :url WHERE id = :id', 
                ['url' => $salesChannelUrl['url'], 'id' => $salesChannelUrl['id']]
            );
        }

        return true;
    }
    
    /**
     * Sets the salesChannel of the staging environment in maintenance
     * 
     * @param string $selectedProfileId
     * 
     * @return bool
     */
    public function setSalesChannelsInMaintenance(string $selectedProfileId): bool
    {
        $stagingConnectionParams = [
            'dbname' => 'staging',
            'user' => 'user',
            'password' => 'password',
            'host' => 'localhost',
            'driver' => 'pdo_mysql',
        ];

        $selectedProfile = $this->getSelectedProfile($selectedProfileId);
        
        if ($selectedProfile) {
            $stagingConnectionParams = $this->getStagingConnectionParams($selectedProfile);
        }
        
        $stagingConnection = DriverManager::getConnection($stagingConnectionParams);

        $salesChannels = $stagingConnection->executeQuery('SELECT id as id, maintenance FROM sales_channel')->fetchAll();

        foreach ($salesChannels as $salesChannel) {
            $stagingConnection->executeUpdate('UPDATE sales_channel SET maintenance = :maintenance WHERE id = :id', 
                ['maintenance' => true, 'id' => $salesChannel['id']]
            );
        }

        return true;
    }

    /**
     * Creates the .env file with all necessary data for the staging environment
     * 
     * @param string $selectedProfileId
     * 
     * @return bool 
     */
    public function createEnvFile(string $selectedProfileId): bool
    {
        $stagingConnectionParams = [
            'dbname' => 'staging',
            'user' => 'user',
            'password' => 'password',
            'host' => 'localhost',
            'driver' => 'pdo_mysql',
        ];

        $config = [
            'folderName' => 'emzstaging'
        ];

        $selectedProfile = $this->getSelectedProfile($selectedProfileId);
        
        if ($selectedProfile) {
            $stagingConnectionParams = $this->getStagingConnectionParams($selectedProfile);
            
            $config['folderName'] = str_replace('/', '', $selectedProfile->get('folderName'));
        }

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

        return true;
    }

    /**
     * Search by id for the selected profile
     * 
     * @param string $id
     * 
     * @return StagingEnvironmentProfileEntity|null
     */
    private function getSelectedProfile(string $id): ?StagingEnvironmentProfileEntity
    {
        $selectedProfile = $this->profileRepository->search(
            new Criteria([$id]), Context::createDefaultContext()
        )->get($id);

        if ($selectedProfile) {
            return $selectedProfile;
        }

        return null;
    }

    /**
     * Creates the array with the connection parameters for the staging environment
     * 
     * @param StagingEnvironmentProfileEntity $selectedProfile
     * 
     * @return array
     */
    private function getStagingConnectionParams(StagingEnvironmentProfileEntity $selectedProfile): array
    {
        return [
            'dbname' => $selectedProfile->get('databaseName'),
            'user' => $selectedProfile->get('databaseUser'),
            'password' => $selectedProfile->get('databasePassword'),
            'host' => $selectedProfile->get('databaseHost'),
            'port' => $selectedProfile->get('databasePort'),
            'driver' => 'pdo_mysql'
        ];
    }
}