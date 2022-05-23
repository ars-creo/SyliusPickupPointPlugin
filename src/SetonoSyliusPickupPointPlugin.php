<?php

declare(strict_types=1);

namespace Setono\SyliusPickupPointPlugin;

use Setono\SyliusPickupPointPlugin\DependencyInjection\Compiler\RegisterProvidersPass;
use Setono\SyliusPickupPointPlugin\DependencyInjection\Compiler\RegisterHttpClientPass;
use Setono\SyliusPickupPointPlugin\DependencyInjection\Compiler\RegisterFactoriesPass;
use Sylius\Bundle\CoreBundle\Application\SyliusPluginTrait;
use Sylius\Bundle\ResourceBundle\AbstractResourceBundle;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class SetonoSyliusPickupPointPlugin extends AbstractResourceBundle
{
    use SyliusPluginTrait;

    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new RegisterProvidersPass());
        $container->addCompilerPass(new RegisterHttpClientPass());
        $container->addCompilerPass(new RegisterFactoriesPass());
    }

    public function getSupportedDrivers(): array
    {
        return [
            SyliusResourceBundle::DRIVER_DOCTRINE_ORM,
        ];
    }
}
