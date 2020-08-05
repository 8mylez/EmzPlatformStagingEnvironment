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

use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\BoolField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\JsonField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\CreatedAtField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\LongTextField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\AllowHtml;
use Shopware\Core\Framework\DataAbstractionLayer\Field\UpdatedAtField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Emz\StagingEnvironment\Core\Content\StagingEnvironment\StagingEnvironmentEntity;
use Emz\StagingEnvironment\Core\Content\StagingEnvironment\StagingEnvironmentCollection;

class StagingEnvironmentDefinition extends EntityDefinition
{
    public const ENTITY_NAME = 'emz_pse_environment';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getCollectionClass(): string
    {
        return StagingEnvironmentCollection::class;
    }

    public function getEntityClass(): string
    {
        return StagingEnvironmentEntity::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->setFlags(new PrimaryKey(), new Required()),
            (new StringField('environment_name', 'environmentName'))->setFlags(new Required()),
            (new StringField('folder_name', 'folderName'))->setFlags(new Required()),
            new StringField('sub_folder', 'subFolder'),
            new StringField('excluded_folders', 'excludedFolders'),
            (new LongTextField('comment', 'comment'))->addFlags(new AllowHtml()),
            (new StringField('database_name', 'databaseName'))->setFlags(new Required()),
            (new StringField('database_user', 'databaseUser'))->setFlags(new Required()),
            (new StringField('database_host', 'databaseHost'))->setFlags(new Required()),
            (new StringField('database_password', 'databasePassword'))->setFlags(new Required()),
            new StringField('database_port', 'databasePort'),
            new BoolField('set_in_maintenance', 'setInMaintenance'),
            new UpdatedAtField(),
            new CreatedAtField()
        ]);
    }
}