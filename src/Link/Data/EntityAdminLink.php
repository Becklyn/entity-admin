<?php declare(strict_types=1);

namespace Becklyn\EntityAdmin\Link\Data;

use Becklyn\Rad\Route\DeferredRoute;
use Becklyn\Rad\Translation\DeferredTranslation;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * An unresolved entity usage. Optimized for ease of use.
 *
 * Neither the group nor the route is resolved yet.
 */
final class EntityAdminLink
{
    /**
     * @var array<string|DeferredTranslation>
     */
    private $labels;


    /**
     * @var string|null
     */
    private $group;


    /**
     * A label that describes the type, like a text or an icon key. Depends on the UI on what to do with this.
     *
     * @var string|null
     */
    private $type;


    /**
     * @var DeferredRoute|null
     */
    private $link;


    /**
     * @param string|DeferredTranslation|array<string|DeferredTranslation> $labels
     * @param string|null                                                  $group  The group name. Will be translated in the `backend` domain.
     */
    public function __construct ($labels, ?string $group = null, ?string $type = null, ?DeferredRoute $link = null)
    {
        $this->labels = \is_array($labels) ? $labels : [$labels];
        $this->group = $group;
        $this->type = $type;
        $this->link = $link;
    }


    /**
     * @return array<string|DeferredTranslation>
     */
    public function getLabels () : array
    {
        return $this->labels;
    }


    /**
     */
    public function getGroup () : ?string
    {
        return $this->group;
    }


    /**
     */
    public function getType () : ?string
    {
        return $this->type;
    }


    /**
     */
    public function getLink () : ?DeferredRoute
    {
        return $this->link;
    }


    /**
     * Resolves the entity usage
     */
    public function resolve (UrlGeneratorInterface $urlGenerator, TranslatorInterface $translator) : ResolvedEntityAdminLink
    {
        return new ResolvedEntityAdminLink(
            DeferredTranslation::translateAllValues($this->labels, $translator),
            $this->type,
            null !== $this->link
                ? $this->link->generate($urlGenerator)
                : null
        );
    }
}
