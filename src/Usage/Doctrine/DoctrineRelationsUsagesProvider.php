<?php declare(strict_types=1);

namespace Becklyn\EntityAdmin\Usage\Doctrine;

use Becklyn\EntityAdmin\Usage\EntityUsagesProviderInterface;
use Becklyn\RadBundle\Entity\Interfaces\EntityInterface;

final class DoctrineRelationsUsagesProvider implements EntityUsagesProviderInterface
{
    private DoctrineRelationsFetcher $relationsMap;


    /**
     */
    public function __construct (DoctrineRelationsFetcher $relationsMap)
    {
        $this->relationsMap = $relationsMap;
    }


    /**
     * @inheritDoc
     */
    public function provideUsages (EntityInterface $entity) : array
    {
        return $this->relationsMap->fetchEntitiesFromRelations($entity);
    }
}
