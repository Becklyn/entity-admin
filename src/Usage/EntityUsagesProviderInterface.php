<?php declare(strict_types=1);

namespace Becklyn\EntityAdmin\Usage;

use Becklyn\Rad\Entity\Interfaces\EntityInterface;

interface EntityUsagesProviderInterface
{
    /**
     * Searches for all entities that are using (= having a relation to)
     * the given entity.
     *
     * @return EntityInterface[]
     */
    public function provideUsages (EntityInterface $entity) : array;
}
