<?php declare(strict_types=1);

namespace Becklyn\EntityAdmin\Usage\Doctrine\Map;

final class DoctrineRelation
{
    private string $class;
    private string $property;
    private string $target;
    private bool $multiple;


    /**
     */
    public function __construct (
        string $class,
        string $property,
        string $target,
        bool $multiple
    )
    {
        $this->class = $class;
        $this->property = $property;
        $this->target = $target;
        $this->multiple = $multiple;
    }


    /**
     */
    public function getClass () : string
    {
        return $this->class;
    }


    /**
     */
    public function getProperty () : string
    {
        return $this->property;
    }


    /**
     */
    public function getTarget () : string
    {
        return $this->target;
    }


    /**
     */
    public function isMultiple () : bool
    {
        return $this->multiple;
    }


    /**
     * Returns a uniquely identifying key (for deduplicating)
     */
    public function getUniqueKey () : string
    {
        return "{$this->class}::{$this->property}";
    }
}
