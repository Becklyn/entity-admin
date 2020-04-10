<?php declare(strict_types=1);

namespace Becklyn\Usages;

use Becklyn\RadBundle\Bundle\BundleExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class BecklynUsagesBundle extends Bundle
{
    /**
     * @inheritDoc
     */
    public function getContainerExtension ()
    {
        return new BundleExtension($this);
    }


    /**
     * @inheritDoc
     */
    public function getPath ()
    {
        return \dirname(__DIR__);
    }
}
