<?php declare(strict_types=1);

namespace Becklyn\EntityAdmin\Link;

use Becklyn\EntityAdmin\Link\Data\EntityAdminLink;
use Becklyn\RadBundle\Entity\Interfaces\EntityInterface;

interface EntityAdminLinkerInterface
{
    /**
     * Returns whether the given entity is supported.
     */
    public function supports (EntityInterface $entity) : bool;

    /**
     * Links to the given entity.
     */
    public function link (EntityInterface $entity) : ?EntityAdminLink;
}
