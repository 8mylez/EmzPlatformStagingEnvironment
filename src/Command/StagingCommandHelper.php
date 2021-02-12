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
 *                 /____/
 *
 * Quote:
 * "Any fool can write code that a computer can understand.
 * Good programmers write code that humans can understand."
 * – Martin Fowler
 */

declare(strict_types=1);

namespace Emz\StagingEnvironment\Command;

use Emz\StagingEnvironment\Core\Content\StagingEnvironment\StagingEnvironmentEntity;
use Emz\StagingEnvironment\Core\Content\StagingEnvironment\StagingEnvironmentCollection;
use Emz\StagingEnvironment\Command\StagingCommandHelperInterface;

class StagingCommandHelper implements StagingCommandHelperInterface
{
    public function getStagingEnvironmentChoices(StagingEnvironmentCollection $stagingEnvironments): array
    {
        $choices = [];

        foreach($stagingEnvironments as $stagingEnvironment) {
            $choiceString = $stagingEnvironment->getEnvironmentName();

            if (!empty($stagingEnvironment->getComment())) {
                $choiceString .= ' | ' . $stagingEnvironment->getComment();
            }

            $choiceString .= ' | ' . $stagingEnvironment->getId();

            $choices[] = $choiceString;
        }

        return $choices;
    }

    public function parseStagingEnvironmentAnswer($answer, StagingEnvironmentCollection $stagingEnvironments): ?StagingEnvironmentEntity
    {
        $parts = explode('|', $answer);
        $stagingEnvironmentId = trim(array_pop($parts));
        $stagingEnvironment = $stagingEnvironments->get($stagingEnvironmentId);

        if (!$stagingEnvironmentId || !$stagingEnvironment) {
            $this->io->error('Invalid answer');

            return null;
        };

        return $stagingEnvironment;
    }
}
