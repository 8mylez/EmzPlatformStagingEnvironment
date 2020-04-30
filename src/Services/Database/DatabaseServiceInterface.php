<?php declare(strict_types=1);

namespace Emz\StagingEnvironment\Services\Database;

interface DatabaseServiceInterface
{
    public function getAllTableNames();

    public function syncTableSchema($table, $overwrite = true);

    public function syncTableData($table, $overwrite = true);
}
