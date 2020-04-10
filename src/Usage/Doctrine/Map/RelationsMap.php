<?php declare(strict_types=1);

namespace Becklyn\EntityAdmin\Usage\Doctrine\Map;

use Becklyn\EntityAdmin\Usage\Doctrine\Map\DoctrineRelation;

final class RelationsMap
{
    /** @var DoctrineRelation[]  */
    private array $relations;
    /** @var array<string, DoctrineRelation>  */
    private array $targetMap = [];
    /** @var array<string, string[]> */
    private array $entitiesToSearch;

    /**
     * @param DoctrineRelation[] $relations
     */
    public function __construct (array $relations, array $entitiesToSearch)
    {
        $this->relations = $relations;
        $this->entitiesToSearch = $entitiesToSearch;

        foreach ($relations as $relation)
        {
            $this->targetMap[$relation->getTarget()][] = $relation;
        }
    }


    /**
     * Returns the relations pointing to the given target
     *
     * @return array<string, DoctrineRelation[]>
     */
    public function getGroupedRelationsWithTarget (string $className) : array
    {
        $relations = $this->getRelationsWithTarget($className);
        $grouped = [];

        foreach ($relations as $relation)
        {
            $grouped[$relation->getClass()][] = $relation;
        }

        return $grouped;
    }


    /**
     * Returns all relations with the given target
     *
     * @return DoctrineRelation[]
     */
    public function getRelationsWithTarget (string $className) : array
    {
        $relations = [];
        $aliases = $this->entitiesToSearch[$className] ?? [$className];
        dump($aliases);

        foreach ($aliases as $aliasClass)
        {
            foreach (($this->targetMap[$aliasClass] ?? []) as $relation)
            {
                $relations[$relation->getUniqueKey()] = $relation;
            }
        }

        return \array_values($relations);
    }


    /**
     * Fetches all relations targeting the given class name
     */
    private function fetchRelations (string $className) : array
    {
        $relations = [];

        foreach (($this->targetMap[$className] ?? []) as $relation)
        {
            $relations[$relation->getUniqueKey()] = $relation;
        }

        return $relations;
    }
}