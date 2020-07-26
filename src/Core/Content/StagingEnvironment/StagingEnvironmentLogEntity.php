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

namespace Emz\StagingEnvironment\Core\Content\StagingEnvironment;

use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;
use Emz\StagingEnvironment\Core\Content\StagingEnvironment\StagingEnvironmentEntity;

class StagingEnvironmentLogEntity extends Entity
{
    use EntityIdTrait;

    /**
     * @var string
     */
    protected $environmentId;

    /**
     * @var StagingEnvironmentEntity
     */
    protected $environment;

    /**
     * @var string
     */
    protected $state;

    public function getEnvironmentId(): string
    {
        return $this->environmentId;
    }

    public function setEnvironmentId(string $environmentId): void
    {
        $this->environmentId = $environmentId;
    }

    public function getEnvironment(): StagingEnvironmentEntity
    {
        return $this->environment;
    }

    public function setEnvironment(StagingEnvironmentEntity $environment): void
    {
        $this->environment = $environment;
    }
}