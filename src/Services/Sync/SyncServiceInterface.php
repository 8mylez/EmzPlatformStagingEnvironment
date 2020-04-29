<?php declare(strict_types=1);

namespace Emz\StagingEnvironment\Services\Sync;

interface SyncServiceInterface
{
    public function syncCore(string $folderName): bool;
}