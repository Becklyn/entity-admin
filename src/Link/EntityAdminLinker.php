<?php declare(strict_types=1);

namespace Becklyn\EntityAdmin\Link;

use Becklyn\EntityAdmin\Link\Data\EntityAdminLink;
use Becklyn\EntityAdmin\Link\Data\ResolvedEntityAdminLink;
use Becklyn\Rad\Entity\Interfaces\EntityInterface;
use Becklyn\Rad\Translation\BackendTranslator;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class EntityAdminLinker
{
    /** @var iterable<EntityAdminLinkerInterface> */
    private iterable $linkers;
    private BackendTranslator $backendTranslator;
    private RouterInterface $router;
    private TranslatorInterface $translator;


    /**
     * @param iterable<EntityAdminLinkerInterface> $linkers
     */
    public function __construct (
        iterable $linkers,
        BackendTranslator $backendTranslator,
        RouterInterface $router,
        TranslatorInterface $translator
    )
    {
        $this->linkers = $linkers;
        $this->router = $router;
        $this->backendTranslator = $backendTranslator;
        $this->translator = $translator;
    }


    /**
     * @param EntityInterface[] $entities
     *
     * @return array<string, ResolvedEntityAdminLink[]>
     */
    public function linkAll (array $entities) : array
    {
        /** @var EntityAdminLink[] $links */
        $links = [];

        foreach ($entities as $entity)
        {
            $links[] = $this->linkToEntity($entity);
        }

        return $this->resolveAndGroupLinks($links);
    }


    /**
     * Generates the link to the given entity
     */
    private function linkToEntity (EntityInterface $entity) : EntityAdminLink
    {
        foreach ($this->linkers as $linker)
        {
            if ($linker->supports($entity))
            {
                $generated = $linker->link($entity);

                if (null !== $generated)
                {
                    return $generated;
                }

                break;
            }
        }

        return new EntityAdminLink($this->generateDefaultName($entity));
    }


    /**
     * Generates the default name
     */
    private function generateDefaultName (EntityInterface $entity) : string
    {
        $name = \get_class($entity);
        $lastSlash = \strrpos($name, "\\");

        if (false !== $lastSlash)
        {
            $name = \substr($name, $lastSlash + 1);
        }

        return \sprintf('%s (#%d)', $name, $entity->getId());
    }


    /**
     * @param EntityAdminLink[] $links
     *
     * @return array<string, ResolvedEntityAdminLink[]>
     */
    private function resolveAndGroupLinks (array $links) : array
    {
        $grouped = [];
        $ungrouped = [];

        /** @var EntityAdminLink|null $link */
        foreach ($links as $link)
        {
            $resolved = $link->resolve($this->router, $this->translator);

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
