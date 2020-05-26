<?php declare(strict_types=1);

namespace Emz\StagingEnvironment\Services\Database;

use Emz\StagingEnvironment\Services\Database\DatabaseSyncServcieInterface;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Schema\Synchronizer\SingleDatabaseSynchronizer;

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

        
        /**
         * How it should work
         * 
         * 1. Establish connection to staging database
         * 2. Copy Scheme
         * 3. Copy Data, but in badges?
         * 4. Update settings for staging environment --> saleschannel url, maintenance mode, etc.
         */

        //self::$connection = DriverManager::getConnection($parameters, new Configuration());
        //put here configuration of plugin
        $stagingConnectionParams = [
            'dbname' => 'shopware_staging',
            'user' => 'app',
            'password' => 'app',
            'host' => 'localhost',
            'driver' => 'pdo_mysql',
        ];

        $stagingConnection = DriverManager::getConnection($stagingConnectionParams);
        // $stmt = $stagingConnection->executeQuery("CREATE TABLE cms_page (
        //     preview_media_id BINARY(16) DEFAULT NULL, 
        //     id BINARY(16) NOT NULL, 
        //     type VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, 
        //     entity VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, 
        //     locked TINYINT(1) DEFAULT '0' NOT NULL, 
        //     config JSON DEFAULT NULL, 
        //     created_at DATETIME NOT NULL, 
        //     updated_at DATETIME DEFAULT NULL, 
        //     INDEX IDX_D39C1B5D2DD3AFD5 (preview_media_id)
        // ) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = ''");

        $stmt = $stagingConnection->executeQuery("select TABLE_NAME from INFORMATION_SCHEMA.TABLES where TABLE_SCHEMA = 'shopware_staging' order by create_time asc");
        $tables = $stmt->fetchAll(\PDO::FETCH_COLUMN);

        echo "<pre>";
        print_r($tables);
        die();

        

        // "$stmt = $this->connection->executeQuery("select TABLE_NAME from INFORMATION_SCHEMA.TABLES where TABLE_SCHEMA = 'shopware6' order by create_time asc");
        // $tables = $stmt->fetchAll(\PDO::FETCH_COLUMN);


         
        $schemaManager = $this->connection->getSchemaManager();
        $currentSchema = $schemaManager->createSchema();
        $newSchema = new Schema();

        $sqlQueries = $newSchema->getMigrateToSql($currentSchema, $this->connection->getDatabasePlatform());

        echo "<pre>";

        foreach($sqlQueries as $sqlQuery) {

        }

        // foreach($schemaManager->listTables() as $table) {
        //     echo $table->getName() . "\n"; 

        //     if ($table->getName() == 'cart') {
        //         foreach($schemaManager->listTableIndexes('cart') as $index) {
        //             echo "Index: " . $index->getName() . "\n";
        //             print_r($index->getColumns());
        //         }
        //     }
        // }

    }

}