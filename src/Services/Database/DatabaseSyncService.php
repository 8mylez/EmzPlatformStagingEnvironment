<?php declare(strict_types=1);

namespace Emz\StagingEnvironment\Services\Database;

use Emz\StagingEnvironment\Services\Database\DatabaseSyncServcieInterface;
use Doctrine\DBAL\Connection;

class DatabaseSyncService implements DatabaseSyncServiceInterface
{
    /** 
     * @var Connection
     */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function syncDatabase()
    {
        echo 'SYNC THAT DATABASE';
    }

}