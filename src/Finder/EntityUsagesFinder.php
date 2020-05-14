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
            foreach ($provider->provideUsages($entity) as $rawUsage)
            {
                $usage = $this->transformEntity($rawUsage, $entity);

                if (null === $usage)
                {
                    continue;
                }

                $key = \get_class($usage) . ":{$usage->getId()}";
                $usages[$key] = $usage;
            }
        }

        return \array_values($usages);
    }


    /**
     * Transforms the entity with the registered transformers
     */
    private function transformEntity (EntityInterface $usage, EntityInterface $source) : ?EntityInterface
    {
        foreach ($this->transformers as $transformer)
        {
            $source = $transformer->transform($usage, $source);

            if (null === $source)
            {
                return null;
            }
        }

        return $source;
    }
}
