<?php declare(strict_types=1);

namespace Emz\StagingEnvironment\Services\Database;

use Emz\StagingEnvironment\Services\Database\DatabaseSyncServcieInterface;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;

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

        //TODO: put here configuration of plugin
        $stagingConnectionParams = [
            'dbname' => 'shopware_staging',
            'user' => 'app',
            'password' => 'app',
            'host' => 'localhost',
            'driver' => 'pdo_mysql',
        ];

        $stagingConnection = DriverManager::getConnection($stagingConnectionParams);
        
        $tables = $this->connection->executeQuery('SHOW FULL TABLES;')->fetchAll();

        foreach($tables as $table) {
            $create = $this->connection->executeQuery('SHOW CREATE TABLE `' . $table['Tables_in_shopware'] . '`')->fetch();

            $stagingConnection->executeQuery('SET FOREIGN_KEY_CHECKS=0;DROP TABLE IF EXISTS `' . $table['Tables_in_shopware'] . '`;SET FOREIGN_KEY_CHECKS=1;');

            echo "Table was dropped: " . $create['Table'] . "\n";

            $stagingConnection->executeQuery('SET FOREIGN_KEY_CHECKS=0;' . $create['Create Table'] . ';SET FOREIGN_KEY_CHECKS=1;');

            echo "Table was created: " . $create['Table'] . "\n";

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

                echo "Imported Data for: " . $table['Tables_in_shopware'] . "\n";
                
            }

            echo "\n";
        }
    }

}