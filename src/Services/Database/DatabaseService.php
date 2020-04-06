<?php declare(strict_types=1);

namespace Emz\StagingEnvironment\Services\Database;

use Doctrine\DBAL\Connection;

class DatabaseService implements DatabaseServiceInterface
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var string
     */
    private $kernelProjectDir;

    public function __construct(
        Connection $connection,
        string $kernelProjectDir
    )
    {
        $this->connection = $connection;
        $this->kernelProjectDir = $kernelProjectDir;
    }

    public function getAllTableNames()
    {
        //TODO: get real database name from config.php
        $stmt = $this->connection->executeQuery("select TABLE_NAME from INFORMATION_SCHEMA.TABLES where TABLE_SCHEMA = 'shopware6' order by create_time asc");
        $tables = $stmt->fetchAll(\PDO::FETCH_COLUMN);

        //TODO: what if no values returned?

        return $tables;
    }

    public function syncTableSchema($table, $overwrite = true)
    {
        $table = $this->delimite($table);

        if (!$table) {
            return;
        }

        $result = false;

        $pdo = $this->getStagingPdoConnection();

        if ($overwrite) {
            $pdo->query("DROP TABLE IF EXISTS {$table}");
        }

        $stmt = $this->connection->executeQuery("SHOW CREATE TABLE {$table}");
        $create = $stmt->fetch()['Create Table']; //TODO: is there a cleaner way for this

        $create = str_replace("CREATE TABLE", "CREATE TABLE IF NOT EXISTS", $create);

        try {
            $pdo->query("SET FOREIGN_KEY_CHECKS=0;");
            $result = $pdo->query($create);
            $pdo->query("SET FOREIGN_KEY_CHECKS=1;");
        } catch (\Exception $exception) {
            echo '<pre>';
            print_r($exception->getMessage());
            die();
        }

        return $result;
    }

    public function syncTableData($table, $overwrite = true)
    {
        try {
            //TODO: lock table
            $table = $this->delimite($table);

            $pdo = $this->getStagingPdoConnection();

            $stmt = $this->connection->executeQuery("SHOW COLUMNS FROM {$table}");
            $cols = [];
            $numeric = [];
            $excludedCols = [];

            foreach ($stmt->fetchAll() as $row) {

                if (strpos($row['Extra'], 'GENERATED') !== false) {
                    $excludedCols[] = $row['Field'];
                    continue;
                }

                $col = $row['Field'];
                $cols[] = $this->delimite($col);
                $numeric[$col] = (bool) preg_match('#^[^(]*(BYTE|COUNTER|SERIAL|INT|LONG$|CURRENCY|REAL|MONEY|FLOAT|DOUBLE|DECIMAL|NUMERIC|NUMBER)#i', $row['Type']);
            }

            $cols = '(' . implode(', ', $cols) . ')';
            $values = '';

            $stmt = $this->connection->executeQuery("SELECT * FROM {$table}");
            $rows = $stmt->fetchAll();

            if (!$rows) {
                return true;
            }

            $i = 0;
            $len = count($rows);

            foreach ($rows as $row) {

                $values .= '(';

                foreach ($row as $key => $value) {
                    if (in_array($key, $excludedCols)) {
                        unset($row[$key]);
                    }
                }

                $i2 = 0;
                $len2 = count($row);

                foreach ($row as $key => $value) {
                    if ($value === null) {
                        $values .= "NULL\t";
                    } elseif ($numeric[$key]) {
                        $values .= $value . "\t";
                    } else {
                        $values .= $pdo->quote($value) . "\t";
                    }

                    if ($i2 != $len2 - 1) {
                        $values .= ',';
                    }

                    $i2++;
                }

                $values .= ')';

                if ($i != $len - 1) {
                    $values .= ',';
                }

                $i++;
            }

            $pdo->query("SET FOREIGN_KEY_CHECKS=0;");
            $insert = "INSERT INTO {$table} {$cols} VALUES\n{$values}";
            $result = $pdo->query($insert);
            $pdo->query("SET FOREIGN_KEY_CHECKS=1;");

            //
            //
            //TODO: Aktuelles Problem => bei 'order_delivery_position' sind manche spalten wie "total_price" price "generated". d.h., dass sie erst null oder sowas sein müssen und beim setzen automatisch aus anderen feldern generiert werden
            // Hier kommt aber wieder das foreign-key-problem. damit die generated felder alle korrekt gesetzt werden können, müssen die tabellen vorher irgendwie sortiert werden
            //
            //

            if (!$result) {
                echo '<pre>';
                echo $table;
                print_r($insert);
                print_r($pdo->errorInfo());
                file_put_contents('demo.sql', $insert);
                die();
            }
        } catch (\Exception $exception) {
            echo '<pre>';
            print_r($exception->getMessage());
            die();
        }

        return $result;
    }

    private function delimite($s)
    {
        return '`' . str_replace('`', '``', $s) . '`';
    }

    private function getStagingPdoConnection()
    {
        return new \PDO('mysql:host=localhost;dbname=shopware6_staging', 'root', 'root');
    }
}
