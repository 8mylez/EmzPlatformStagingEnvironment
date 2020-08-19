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

namespace Emz\StagingEnvironment\Core\Content\StagingEnvironment\Exception;

use Shopware\Core\Framework\ShopwareHttpException;
use Symfony\Component\HttpFoundation\Response;

class StagingDataNotClearedException extends ShopwareHttpException
{
    public function __construct(string $environmentName)
    {
        parent::__construct(
            'Can not delete staging environment "{{ environmentName }}", because database and folder are not cleared.',
            ['environmentName' => $environmentName]
        );
    }

    public function getErrorCode(): string
    {
        return 'EMZSTAGINGENVIRONMENT__STAGING_DATA_NOT_CLEARED';
    }
}
