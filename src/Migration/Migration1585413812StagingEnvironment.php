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
                `excluded_folders` LONGTEXT NULL,
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
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ');

        $connection->executeQuery('
            CREATE TABLE IF NOT EXISTS `emz_pse_log` (
                `id` BINARY(16) NOT NULL,
                `environment_id` BINARY(16) NOT NULL,
                `state` VARCHAR(255) NOT NULL,
                `created_at` DATETIME(3) NOT NULL,
                PRIMARY KEY (`id`),
                CONSTRAINT `fk.emz_pse_log.environment_id`
                    FOREIGN KEY (`environment_id`) REFERENCES `emz_pse_environment` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ');
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
