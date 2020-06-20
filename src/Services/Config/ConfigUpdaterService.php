<?php declare(strict_types=1);

namespace Emz\StagingEnvironment\Services\Config;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Symfony\Component\Dotenv\Dotenv;

class ConfigUpdaterService implements ConfigUpdaterServiceInterface
{
    /** @var string */
    private $projectDir;

    /**
     * @var Connection
     */
    private $connection;

    public function __construct(
        string $projectDir,
        Connection $connection
    ) {
        $this->projectDir = $projectDir;
        $this->connection = $connection;
    }

    public function setSalesChannelDomains()
    {
        //TODO: put here configuration of plugin
        $stagingConnectionParams = [
            'dbname' => 'shopware_staging',
            'user' => 'app',
            'password' => 'app',
            'host' => 'localhost',
            'driver' => 'pdo_mysql',
        ];

        //should be coming from the profile
        $config = [
            'folderName' => 'emzstaging'
        ];

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
    
    public function setSalesChannelsInMaintenance()
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

        $salesChannels = $stagingConnection->executeQuery('SELECT id as id, maintenance FROM sales_channel')->fetchAll();

        foreach ($salesChannels as $salesChannel) {
            $stagingConnection->executeUpdate('UPDATE sales_channel SET maintenance = :maintenance WHERE id = :id', 
                ['maintenance' => true, 'id' => $salesChannel['id']]
            );
        }

        return true;
    }

    public function createEnvFile()
    {
        //TODO: put here configuration of plugin
        $stagingConnectionParams = [
            'dbname' => 'shopware_staging',
            'user' => 'app',
            'password' => 'app',
            'host' => 'localhost',
            'driver' => 'pdo_mysql',
            'port' => 3306
        ];

        //should be coming from the profile
        $config = [
            'folderName' => 'emzstaging'
        ];

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
}