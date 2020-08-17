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

namespace Emz\StagingEnvironment\Services\Check;

use Emz\StagingEnvironment\Services\Database\CheckServiceInterface;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\Uuid\Uuid;
use Emz\StagingEnvironment\Core\Content\StagingEnvironment\Exception\ProductionDatabaseUsedException;

class DatabaseSyncService implements CheckServiceInterface
{
    /** 
     * @var Connection
     */
    private $connection;

    public function __construct(
        Connection $connection
    ){
        $this->connection = $connection;
    }

    /**
     * Checks if the folder of the staging enviroment is empty
     * 
     * @param string $environmentId
     * @param Context $context
     * 
     * @return bool
     */
    public function isFolderEmpty(string $environmentId, Context $context): bool
    {
        return false;
    }

    /**
     * Clears the database
     * 
     * @param string $environmentId
     * @param Context $context
     * 
     * @return bool
     */
    public function isDatabaseEmpty(string $environmentId, Context $context): bool
    {
        $stagingConnectionParams = [
            'dbname' => $environment->getDatabaseName(),
            'user' => $environment->getDatabaseUser(),
            'password' => $environment->getDatabasePassword(),
            'host' => $environment->getDatabaseHost(),
            'port' => $environment->getDatabasePort(),
            'driver' => 'pdo_mysql'
        ];

        if ($this->connection->getDatabase() === $environment->getDatabaseName()) {
            throw new ProductionDatabaseUsedException($environment->getDatabaseName());
        }

        $stagingConnection = DriverManager::getConnection($stagingConnectionParams);

        $tables = $this->connection->executeQuery('SHOW FULL TABLES;')->fetchAll();
        $tablesInKey = "Tables_in_{$this->connection->getDatabase()}";

        foreach($tables as $table) {
            $stagingConnection->executeQuery('SET FOREIGN_KEY_CHECKS=0;DROP TABLE IF EXISTS `' . $table[$tablesInKey] . '`;SET FOREIGN_KEY_CHECKS=1;');
        }

        $this->environmentLogRepository->create(
            [
                [
                    'id' => Uuid::randomHex(),
                    'environmentId' => $environmentId,
                    'state' => 'database_cleared'
                ],
            ],
            $context
        );

        return true;
    }
}