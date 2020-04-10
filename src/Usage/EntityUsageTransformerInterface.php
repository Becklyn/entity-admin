<?php declare(strict_types=1);

namespace Becklyn\EntityAdmin\Usage;

use Becklyn\RadBundle\Entity\Interfaces\EntityInterface;

/**
 * Transforms the found entity to a possibly different entity
 */
interface EntityUsageTransformerInterface
{
    /**
     * Transforms the entity.
     *
     * Can return `null` to signal to drop the entity.
     */
    public function transform (EntityInterface $entity) : ?EntityInterface;
}
