<?php declare(strict_types=1);

namespace Emz\StagingEnvironment\Core\Content\StagingEnvironment;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;
use Emz\StagingEnvironment\Core\Content\StagingEnvironment\StagingEnvironmentProfileEntity;

/**
 * @method void add(StagingEnvironmentProfileEntity $entity)
 * @method void set(string $key, StagingEnvironmentProfileEntity $entity)
 * @method StagingEnvironmentProfileEntity[] getIterator()
 * @method StagingEnvironmentProfileEntity[] getElements()
 * @method StagingEnvironmentProfileEntity|null get(string $key)
 * @method StagingEnvironmentProfileEntity|null first()
 * @method StagingEnvironmentProfileEntity|null last()
 */
class StagingEnvironmentProfileCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return StagingEnvironmentProfileEntity::class;
    }
}