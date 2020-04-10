<?php declare(strict_types=1);

namespace Becklyn\EntityAdmin\Link;

use Becklyn\EntityAdmin\Link\EntityAdminLinkerInterface;
use Becklyn\RadBundle\Entity\Interfaces\EntityInterface;
use Becklyn\RadBundle\Translation\BackendTranslator;
use Becklyn\EntityAdmin\Link\Data\EntityAdminLink;
use Becklyn\EntityAdmin\Link\Data\ResolvedEntityAdminLink;
use Symfony\Component\Routing\RouterInterface;

final class EntityAdminLinker
{
    /** @var iterable<EntityAdminLinkerInterface> */
    private iterable $linkers;
    private BackendTranslator $backendTranslator;
    private RouterInterface $router;


    /**
     * @param iterable<EntityAdminLinkerInterface> $linkers
     */
    public function __construct (
        iterable $linkers,
        BackendTranslator $backendTranslator,
        RouterInterface $router
    )
    {
        $this->linkers = $linkers;
        $this->router = $router;
        $this->backendTranslator = $backendTranslator;
    }


    /**
     * @param EntityInterface[] $entities
     * @return array<string, ResolvedEntityAdminLink[]>
     */
    public function linkAll (array $entities) : array
    {
        /** @var EntityAdminLink[] $links */
        $links = [];

        foreach ($entities as $entity)
        {
            $link = $this->linkToEntity($entity);

            if (null !== $link)
            {
                $links[] = $link;
            }
        }

        return $this->resolveAndGroupLinks($links);
    }


    /**
     * Generates the link to the given entity
     */
    private function linkToEntity (EntityInterface $entity) : ?EntityAdminLink
    {
        foreach ($this->linkers as $linker)
        {
            if ($linker->supports($entity))
            {
                return $linker->link($entity);
            }
        }

        return null;
    }


    /**
     * @param array<EntityAdminLink|null> $links
     *
     * @return array<string, ResolvedEntityAdminLink[]>
     */
    private function resolveAndGroupLinks (array $links) : array
    {
        $grouped = [];
        $ungrouped = [];

        foreach ($links as $link)
        {
            if (null === $link)
            {
                continue;
            }

            $resolved = $link->resolve($this->router);

            if (null !== $link->getGroup())
            {
                $group = $this->backendTranslator->t($link->getGroup());
                $grouped[$group][] = $resolved;
            }
            else
            {
                $ungrouped[] = $resolved;
            }
        }


        \uksort($grouped, "strnatcasecmp");

        if (!empty($ungrouped))
        {
            $ungroupedLabel = $this->backendTranslator->t("admin-link.group.ungrouped");
            $grouped[$ungroupedLabel] = $ungrouped;
        }

        return $grouped;
    }
}
