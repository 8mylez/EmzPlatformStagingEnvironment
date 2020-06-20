<?php declare(strict_types=1);

namespace Emz\StagingEnvironment\Services\Config;

interface ConfigUpdaterServiceInterface
{
    public function setSalesChannelDomains();

    public function setSalesChannelsInMaintenance();

    public function createEnvFile();
}