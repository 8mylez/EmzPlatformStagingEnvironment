<?php declare(strict_types=1);

namespace Emz\StagingEnvironment;

use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;
use Doctrine\DBAL\Connection;

class EmzPlatformStagingEnvironment extends Plugin
{
    public function uninstall(UninstallContext $context): void
    {
        parent::uninstall($context);

        if ($context->keepUserData()) {
            return;
        }

        $connection = $this->container->get(Connection::class);

        $connection->executeQuery('DROP TABLE IF EXISTS `emz_pse_profile`');
        $connection->executeQuery('DROP TABLE IF EXISTS `emz_pse_environment`');
    }
}