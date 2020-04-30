<?php declare(strict_types=1);

namespace Emz\StagingEnvironment\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1585413812StagingEnvironment extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1585413812;
    }

    public function update(Connection $connection): void
    {
        $connection->executeQuery('
            CREATE TABLE IF NOT EXISTS `emz_pse_environment` (
                `id` BINARY(16) NOT NULL,
                `environment_name` VARCHAR(255) NOT NULL,
                `profile_name` VARCHAR(255) NOT NULL,
                `folder_name` VARCHAR(255) NOT NULL,
                `excluded_folders` JSON NULL,
                `comment` LONGTEXT NULL,
                `database_name` VARCHAR(255) NOT NULL,
                `database_user` VARCHAR(255) NOT NULL,
                `database_host` VARCHAR(255) NOT NULL,
                `database_password` VARCHAR(255) NOT NULL,
                `database_port` VARCHAR(255) NULL,
                `catch_emails` TINYINT(1) NOT NULL DEFAULT 1,
                `anonymize_data` TINYINT(1) NOT NULL DEFAULT 0,
                `deactivate_scheduled_tasks` TINYINT(1) NOT NULL DEFAULT 0,
                `set_in_maintenance` TINYINT(1) NOT NULL DEFAULT 0,
                `created_at` DATETIME(3) NOT NULL,
                `updated_at` DATETIME(3) NULL,
                PRIMARY KEY (`id`),
                CONSTRAINT `json.emz_pse_environment.excluded_folders` CHECK (JSON_VALID(`excluded_folders`))
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ');
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
