<?php declare(strict_types=1);

namespace Becklyn\EntityAdmin\Finder;

use Becklyn\EntityAdmin\Usage\EntityUsagesProviderInterface;
use Becklyn\EntityAdmin\Usage\EntityUsageTransformerInterface;
use Becklyn\RadBundle\Entity\Interfaces\EntityInterface;

final class EntityUsagesFinder
{
    /** @var iterable<EntityUsagesProviderInterface>|EntityUsagesProviderInterface[] */
    private iterable $providers;

    /** @var iterable<EntityUsageTransformerInterface>|EntityUsageTransformerInterface[] */
    private iterable $transformers;

    /**
     * @param iterable<EntityUsagesProviderInterface>   $providers
     * @param iterable<EntityUsageTransformerInterface> $transformers
     */
    public function __construct (iterable $providers, iterable $transformers)
    {
        $this->providers = $providers;
        $this->transformers = $transformers;
    }


    /**
     * Fetches all entities that are using the given entity.
     *
     * @return EntityInterface[]
     */
    public function findUsages (EntityInterface $entity) : array
    {
        $usages = [];

        foreach ($this->providers as $provider)
        {
            foreach ($provider->provideUsages($entity) as $relation)
            {
                $relatedEntity = $this->transformEntity($entity, $relation);

                if (null === $relatedEntity)
                {
                    continue;
                }

                $key = \get_class($relatedEntity) . ":{$relatedEntity->getId()}";
                $usages[$key] = $relatedEntity;
            }
        }

        return \array_values($usages);
    }


    /**
     * Transforms the entity with the registered transformers
     */
    private function transformEntity (EntityInterface $source, EntityInterface $entity) : ?EntityInterface
    {
        foreach ($this->transformers as $transformer)
        {
            $entity = $transformer->transform($source, $entity);

            if (null === $entity)
            {
                return null;
            }
        }

        return $entity;
    }
}
