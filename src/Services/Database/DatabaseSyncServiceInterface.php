<?php declare(strict_types=1);

namespace Emz\StagingEnvironment\Services\Database;

interface DatabaseSyncServiceInterface
{
    public function syncDatabase();
}