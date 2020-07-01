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
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\Context;
use Emz\StagingEnvironment\Core\Content\StagingEnvironment\StagingEnvironmentProfileEntity;

class DatabaseSyncService implements DatabaseSyncServiceInterface
{
    /** 
     * @var Connection
     */
    private $connection;

    /** @var EntityRepositoryInterface */
    private $profileRepository;

    public function __construct(
        Connection $connection,
        EntityRepositoryInterface $profileRepository
    ){
        $this->connection = $connection;
        $this->profileRepository = $profileRepository;
    }

    /**
     * Clones the database with all table strucutes and values
     * 
     * @param string $selectedProfileId
     * 
     * @return bool
     */
    public function syncDatabase(string $selectedProfileId): bool
    {
        $stagingConnectionParams = [
            'dbname' => 'staging',
            'user' => 'user',
            'password' => 'password',
            'host' => 'localhost',
            'driver' => 'pdo_mysql'
        ];

        /** @var StagingEnvironmentProfileEntity */
        $selectedProfile = $this->profileRepository->search(
            new Criteria([$selectedProfileId]), Context::createDefaultContext()
        )->get($selectedProfileId);

        if ($selectedProfile) {
            $stagingConnectionParams = [
                'dbname' => $selectedProfile->get('databaseName'),
                'user' => $selectedProfile->get('databaseUser'),
                'password' => $selectedProfile->get('databasePassword'),
                'host' => $selectedProfile->get('databaseHost'),
                'port' => $selectedProfile->get('databasePort'),
                'driver' => 'pdo_mysql'
            ];
        } else {
            return false;
        }

        $stagingConnection = DriverManager::getConnection($stagingConnectionParams);
        
        $tables = $this->connection->executeQuery('SHOW FULL TABLES;')->fetchAll();

        foreach($tables as $table) {
            $create = $this->connection->executeQuery('SHOW CREATE TABLE `' . $table['Tables_in_shopware'] . '`')->fetch();

            $stagingConnection->executeQuery('SET FOREIGN_KEY_CHECKS=0;DROP TABLE IF EXISTS `' . $table['Tables_in_shopware'] . '`;SET FOREIGN_KEY_CHECKS=1;');

            // echo "Table was dropped: " . $create['Table'] . "\n";

            $stagingConnection->executeQuery('SET FOREIGN_KEY_CHECKS=0;' . $create['Create Table'] . ';SET FOREIGN_KEY_CHECKS=1;');

            // echo "Table was created: " . $create['Table'] . "\n";

            $data = [];
            $data = $this->connection->executeQuery('SELECT * FROM `' . $table['Tables_in_shopware'] . '`')->fetchAll();

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

                    $sqlInsert = 'SET FOREIGN_KEY_CHECKS=0;INSERT INTO `' . $table['Tables_in_shopware'] . '` (' . implode(", ", $columns) . ') VALUES ( ' . implode(', ', $set) . ' );SET FOREIGN_KEY_CHECKS=1;';

                    $stagingConnection->executeUpdate($sqlInsert, $values);
                }

                // echo "Imported Data for: " . $table['Tables_in_shopware'] . "\n";
                
            }

            // echo "\n";
        }

        return true;
    }

}