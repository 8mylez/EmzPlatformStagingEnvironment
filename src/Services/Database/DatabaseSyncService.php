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

namespace Emz\StagingEnvironment\Services\Database;

use Emz\StagingEnvironment\Services\Database\DatabaseSyncServcieInterface;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\Context;
use Emz\StagingEnvironment\Core\Content\StagingEnvironment\StagingEnvironmentEntity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\Uuid\Uuid;
use Emz\StagingEnvironment\Core\Content\StagingEnvironment\Exception\ProductionDatabaseUsedException;

class DatabaseSyncService implements DatabaseSyncServiceInterface
{
    /** 
     * @var Connection
     */
    private $connection;

    /** @var EntityRepositoryInterface */
    private $environmentRepository;

    /** @var EntityRepositoryInterface */
    private $environmentLogRepository;

    public function __construct(
        Connection $connection,
        EntityRepositoryInterface $environmentRepository,
        EntityRepositoryInterface $environmentLogRepository
    ){
        $this->connection = $connection;
        $this->environmentRepository = $environmentRepository;
        $this->environmentLogRepository = $environmentLogRepository;
    }

    /**
     * Clones the database with all table strucutes and values
     * 
     * @param string $environmentId
     * @param Context $context
     * 
     * @return bool
     */
    public function syncDatabase(string $environmentId, Context $context): bool
    {
        /** @var StagingEnvironmentEntity */
        $environment = $this->environmentRepository
            ->search(new Criteria([$environmentId]), $context)
            ->get($environmentId);

        if (!$environment instanceof StagingEnvironmentEntity) {
            throw new \InvalidArgumentException(sprintf('Staging Environment with id %s not found environmentId missing', $environmentId));
        }
        
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
            $create = $this->connection->executeQuery('SHOW CREATE TABLE `' . $table[$tablesInKey] . '`')->fetch();

            $stagingConnection->executeQuery('SET FOREIGN_KEY_CHECKS=0;DROP TABLE IF EXISTS `' . $table[$tablesInKey] . '`;SET FOREIGN_KEY_CHECKS=1;');
            $stagingConnection->executeQuery('SET FOREIGN_KEY_CHECKS=0;' . $create['Create Table'] . ';SET FOREIGN_KEY_CHECKS=1;');

            $data = [];
            $data = $this->connection->executeQuery('SELECT * FROM `' . $table[$tablesInKey] . '`')->fetchAll();

            if (!empty($data)) {
                foreach($data as $d) {
                    $columns = [];
                    $values = [];
                    $set = [];

                    foreach($d as $columnName => $value) {
                        $columns[] = '`' . $columnName . '`';
                        $values[] = $value;
                        $set[] = '?';
                    }

                    $sqlInsert = 'SET FOREIGN_KEY_CHECKS=0;INSERT INTO `' . $table[$tablesInKey] . '` (' . implode(", ", $columns) . ') VALUES ( ' . implode(', ', $set) . ' );SET FOREIGN_KEY_CHECKS=1;';
                    $stagingConnection->executeUpdate($sqlInsert, $values);
                }                
            }
        }

        $this->environmentLogRepository->create(
            [
                [
                    'id' => Uuid::randomHex(),
                    'environmentId' => $environmentId,
                    'state' => 'database_success'
                ],
            ],
            $context
        );

        return true;
    }

    /**
     * Clears the database
     * 
     * @param string $environmentId
     * @param Context $context
     * 
     * @return bool
     */
    public function clearDatabase(string $environmentId, Context $context): bool
    {
        /** @var StagingEnvironmentEntity */
        $environment = $this->environmentRepository
            ->search(new Criteria([$environmentId]), $context)
            ->get($environmentId);

        if (!$environment instanceof StagingEnvironmentEntity) {
            throw new \InvalidArgumentException(sprintf('Staging Environment with id %s not found environmentId missing', $environmentId));
        }
        
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