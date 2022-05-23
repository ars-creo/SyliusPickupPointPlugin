<?php

declare(strict_types=1);

namespace Setono\SyliusPickupPointPlugin\DependencyInjection\Compiler;

use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

final class RegisterFactoriesPass implements CompilerPassInterface
{
    private const REQUEST_FACTORY_SUFFIX = 'request_factory';
    private const STREAM_FACTORY_SUFFIX = 'stream_factory';
    private const PSR17_FACTORY_SUFFIX = 'psr17_factory';

    private const SERVICE_ID_FORMAT = '%s.%s';

    private const SETONO_BPOST_PROVIDER = 'setono_bpost';
    private const SETONO_POSTNL_PROVIDER = 'setono_postnl';

    private const PROVIDERS = [
        self::SETONO_BPOST_PROVIDER,
        self::SETONO_POSTNL_PROVIDER,
    ];

    public function process(ContainerBuilder $container): void
    {
        foreach (self::PROVIDERS as $PROVIDER) {
            if (class_exists(Psr17Factory::class)) {
                // this service is used later if the Psr17Factory exists. Else it will be automatically removed by Symfony
                $container->register(sprintf(self::SERVICE_ID_FORMAT, $PROVIDER, self::PSR17_FACTORY_SUFFIX), Psr17Factory::class);
            }

            $factoryId = sprintf(self::SERVICE_ID_FORMAT, $PROVIDER, self::PSR17_FACTORY_SUFFIX);
            $requestFactoryAlias = sprintf(self::SERVICE_ID_FORMAT, $PROVIDER, self::REQUEST_FACTORY_SUFFIX);
            $this->registerFactory(
                $container,
                $requestFactoryAlias,
                $requestFactoryAlias,
                $factoryId,
                RequestFactoryInterface::class
            );

            $streamFactoryAlias = sprintf(self::SERVICE_ID_FORMAT, $PROVIDER, self::STREAM_FACTORY_SUFFIX);
            $this->registerFactory($container,
                $streamFactoryAlias,
                $streamFactoryAlias,
                $factoryId,
                StreamFactoryInterface::class
            );
        }
    }

    private function registerFactory(ContainerBuilder $container, string $parameter, string $service, string $factoryId, string $factoryInterface): void
    {
        if ($container->hasParameter($parameter)) {
            if (!$container->has($container->getParameter($parameter))) {
                throw new ServiceNotFoundException($container->getParameter($parameter));
            }

            $container->setAlias($service, $container->getParameter($parameter));
        } elseif ($container->has($factoryInterface)) {
            $container->setAlias($service, $factoryInterface);
        } elseif ($container->has('nyholm.psr7.psr17_factory')) {
            $container->setAlias($service, 'nyholm.psr7.psr17_factory');
        } elseif (class_exists(Psr17Factory::class)) {
            $container->setAlias($service, $factoryId);
        }
    }
}
