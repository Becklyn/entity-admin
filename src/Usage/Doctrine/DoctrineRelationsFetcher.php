<?php declare(strict_types=1);

namespace Becklyn\EntityAdmin\Usage\Doctrine;

use Becklyn\EntityAdmin\Usage\Doctrine\Map\DoctrineRelation;
use Becklyn\EntityAdmin\Usage\Doctrine\Map\RelationsMap;
use Becklyn\Rad\Entity\Interfaces\EntityInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Contracts\Cache\CacheInterface;

final class DoctrineRelationsFetcher
{
    private const MAP_CACHE_KEY = "becklyn.entity-admin.doctrine-relations-map";
    private ManagerRegistry $registry;
    private CacheInterface $cache;
    private bool $isDebug;
    private ?RelationsMap $relationsMap = null;


    /**
     */
    public function __construct (
        ManagerRegistry $registry,
        CacheInterface $cache,
        bool $isDebug
    )
    {
        $this->cache = $cache;
        $this->isDebug = $isDebug;
        $this->registry = $registry;
    }


    /**
     *
     */
    public function fetchEntitiesFromRelations (EntityInterface $entity) : array
    {
        $grouped = $this->getMap()->getGroupedRelationsWithTarget(\get_class($entity));
        $result = [];

        foreach ($grouped as $entityClass => $relations)
        {
            foreach ($this->fetchEntitiesForRelation($entity, $entityClass, $relations) as $match)
            {
                $result[] = $match;
            }
        }

        return $result;
    }


    /**
     * @param DoctrineRelation[] $relations
     *
     * @return EntityInterface[]
     */
    private function fetchEntitiesForRelation (EntityInterface $target, string $entityClass, array $relations) : array
    {
        /** @var EntityRepository $repository */
        $repository = $this->registry->getRepository($entityClass);

        $queryBuilder = $repository->createQueryBuilder("entity")
            ->select("entity")
            ->setParameter("target", $target);

        foreach ($relations as $relation)
        {
            if ($relation->isMultiple())
            {
                $queryBuilder->orWhere(":target MEMBER OF entity.{$relation->getProperty()}");
            }
            else
            {
                $queryBuilder->orWhere("entity.{$relation->getProperty()} = :target");
            }
        }

        return $queryBuilder->getQuery()->getResult();
    }


    /**
     * Fetches the relations map
     */
    private function getMap () : RelationsMap
    {
        if (null !== $this->relationsMap)
        {
            return $this->relationsMap;
        }

        if ($this->isDebug)
        {
            return $this->relationsMap = $this->buildMap();
        }

        return $this->relationsMap = $this->cache->get(
            self::MAP_CACHE_KEY,
            fn () => $this->buildMap()
        );
    }


    /**
     * Builds the relations map
     */
    private function buildMap () : RelationsMap
    {
        $classesMetadata = $this->registry->getManager()->getMetadataFactory()->getAllMetadata();
        /** @var DoctrineRelation[] $relations */
        $relations = [];
        $entitiesToSearch = [];

        foreach ($classesMetadata as $metadata)
        {
            if (!$metadata instanceof ClassMetadataInfo)
            {
                throw new \RuntimeException("Invalid class meta data found, must bee ClassMetadataInfo");
            }

            $entityHierarchy = $metadata->parentClasses;
            $entityHierarchy[] = $metadata->name;
            $entitiesToSearch[$metadata->name] = $entityHierarchy;

            foreach ($metadata->associationMappings as $mapping)
            {
                if (!$mapping["isOwningSide"])
                {
                    continue;
                }

                $relations[] = new DoctrineRelation(
                    $metadata->name,
                    $mapping["fieldName"],
                    $mapping["targetEntity"],
                    !empty($mapping["joinTable"])
                );
            }
        }

        return new RelationsMap($relations, $entitiesToSearch);
    }
}
