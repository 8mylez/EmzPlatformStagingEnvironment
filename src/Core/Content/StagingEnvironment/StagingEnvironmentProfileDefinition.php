<?php declare(strict_types=1);

namespace Emz\StagingEnvironment\Core\Content\StagingEnvironment;

use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Emz\StagingEnvironment\Core\Content\StagingEnvironment\StagingEnvironmentProfileEntity;
use Emz\StagingEnvironment\Core\Content\StagingEnvironment\StagingEnvironmentProfileCollection;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\JsonField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\LongTextWithHtmlField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\PasswordField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\BoolField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\UpdatedAtField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\CreatedAtField;

class StagingEnvironmentProfileDefinition extends EntityDefinition
{
    public const ENTITY_NAME = 'emz_pse_profile';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getCollectionClass(): string
    {
        return StagingEnvironmentProfileCollection::class;
    }

    public function getEntityClass(): string
    {
        return StagingEnvironmentProfileEntity::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->setFlags(new PrimaryKey(), new Required()),
            (new StringField('profile_name', 'profileName'))->setFlags(new Required()),
            (new StringField('folder_name', 'folderName'))->setFlags(new Required()),
            new JsonField('excluded_folders', 'excludedFolders'),
            new LongTextWithHtmlField('comment', 'comment'),
            (new StringField('database_name', 'databaseName'))->setFlags(new Required()),
            (new StringField('database_user', 'databaseUser'))->setFlags(new Required()),
            (new StringField('database_host', 'databaseHost'))->setFlags(new Required()),
            (new PasswordField('database_password', 'databasePassword'))->setFlags(new Required()),
            new StringField('database_port', 'databasePort'),
            new BoolField('catch_emails', 'catchEmails'),
            new BoolField('anonymize_data', 'anonymizeData'),
            new BoolField('deactivate_scheduled_tasks', 'deactivateScheduledTasks'),
            new BoolField('set_in_maintenance', 'setInMaintenance'),
            new UpdatedAtField(),
            new CreatedAtField()
        ]);
    }
}