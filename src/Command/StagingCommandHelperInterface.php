<?php declare(strict_types=1);

namespace Emz\StagingEnvironment\Command;

use Emz\StagingEnvironment\Core\Content\StagingEnvironment\StagingEnvironmentCollection;
use Emz\StagingEnvironment\Core\Content\StagingEnvironment\StagingEnvironmentEntity;

interface StagingCommandHelperInterface {
    public function getStagingEnvironmentChoices(StagingEnvironmentCollection $stagingEnvironments): array;

    public function parseStagingEnvironmentAnswer($answer, StagingEnvironmentCollection $stagingEnvironments): ?StagingEnvironmentEntity;
}
