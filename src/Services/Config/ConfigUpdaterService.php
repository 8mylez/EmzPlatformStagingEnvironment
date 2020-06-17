<?php declare(strict_types=1);

namespace Emz\StagingEnvironment\Services\Config;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;

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

    }

    public function setRobotsMetaTag()
    {

    }

    public function updateEnvData()
    {

    }
}