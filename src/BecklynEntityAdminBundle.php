<?php declare(strict_types=1);

namespace Becklyn\EntityAdmin;

use Becklyn\EntityAdmin\Link\EntityAdminLinkerInterface;
use Becklyn\EntityAdmin\Usage\EntityUsagesProviderInterface;
use Becklyn\EntityAdmin\Usage\EntityUsageTransformerInterface;
use Becklyn\RadBundle\Bundle\BundleExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class BecklynEntityAdminBundle extends Bundle
{
    /**
     * @inheritDoc
     */
    public function getContainerExtension ()
    {
        return new BundleExtension($this);
    }


    public function build (ContainerBuilder $container) : void
    {
        $container
            ->registerForAutoconfiguration(EntityAdminLinkerInterface::class)
            ->addTag("becklyn.entity-admin.linker");

        $container
            ->registerForAutoconfiguration(EntityUsagesProviderInterface::class)
            ->addTag("becklyn.entity-admin.usages-provider");

        $container
            ->registerForAutoconfiguration(EntityUsageTransformerInterface::class)
            ->addTag("becklyn.entity-admin.usages-transformer");
    }


    /**
     * @inheritDoc
     */
    public function getPath ()
    {
        return \dirname(__DIR__);
    }
}
