<?php declare(strict_types=1);

namespace Becklyn\EntityAdmin\Link;

use Becklyn\EntityAdmin\Link\Data\EntityAdminLink;

interface EntityAdminLinkerInterface
{
    /**
     * Returns whether the given object is supported.
     */
    public function supports (object $entity) : bool;

    /**
     * Links to the given object.
     */
    public function link (object $entity) : ?EntityAdminLink;
}
