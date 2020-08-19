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

use Emz\StagingEnvironment\Services\Check\CheckServiceInterface;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\Uuid\Uuid;
use Emz\StagingEnvironment\Core\Content\StagingEnvironment\Exception\ProductionDatabaseUsedException;
use Symfony\Component\Finder\Finder;
use Emz\StagingEnvironment\Core\Content\StagingEnvironment\StagingEnvironmentEntity;
use Symfony\Component\Filesystem\Filesystem;

class CheckService implements CheckServiceInterface
{
    /**
     * @var string
     */
    private $projectDir;

    /** 
     * @var Connection
     */
    private $connection;

    /**
     * @var Filesystem
     */
    private $fileSystem;

    public function __construct(
        string $projectDir,
        Connection $connection,
        Filesystem $fileSystem
    ){
        $this->projectDir = $projectDir;
        $this->connection = $connection;
        $this->fileSystem = $fileSystem;
    }

    /**
     * Checks if the folder of the staging enviroment is empty
     * 
     * @param string $environmentId
     * @param Context $context
     * 
     * @return bool
     */
    public function isFolderEmpty(StagingEnvironmentEntity $environment, Context $context): bool
    {
        if (!$environment->getFolderName()) {
            throw new \InvalidArgumentException(sprintf('Staging Environment has no folder saved.'));
        }

        $stagingFolder = $this->projectDir . '/' . $environment->getFolderName();
        $count = 0;

        if ($this->fileSystem->exists($stagingFolder)) {
            $finder = new Finder();
            $count = iterator_count($finder->in($stagingFolder)->getIterator());
        }
        
        return $count === 0;
    }

    /**
     * Clears the database
     * 
     * @param string $environmentId
     * @param Context $context
     * 
     * @return bool
     */
    public function isDatabaseEmpty(StagingEnvironmentEntity $environment, Context $context): bool
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

        try {
            $stagingConnection = DriverManager::getConnection($stagingConnectionParams);

            $tables = $stagingConnection->executeQuery('SHOW FULL TABLES;')->fetchAll();
            
            return count($tables) === 0;
        } catch(\Exception $e) {
            return true;
        }
    }
}