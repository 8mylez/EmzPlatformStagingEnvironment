<?php declare(strict_types=1);

namespace Emz\StagingEnvironment\Core\Content\StagingEnvironment;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;
use Emz\StagingEnvironment\Core\Content\StagingEnvironment\StagingEnvironmentEntity;

/**
 * @method void add(StagingEnvironmentEntity $entity)
 * @method void set(string $key, StagingEnvironmentEntity $entity)
 * @method StagingEnvironmentEntity[] getIterator()
 * @method StagingEnvironmentEntity[] getElements()
 * @method StagingEnvironmentEntity|null get(string $key)
 * @method StagingEnvironmentEntity|null first()
 * @method StagingEnvironmentEntity|null last()
 */
class StagingEnvironmentCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return StagingEnvironmentEntity::class;
    }
}