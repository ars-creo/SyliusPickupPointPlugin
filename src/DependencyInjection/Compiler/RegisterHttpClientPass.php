<?php

declare(strict_types=1);

namespace Setono\SyliusPickupPointPlugin\DependencyInjection\Compiler;

use Buzz\Client\BuzzClientInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

final class RegisterHttpClientPass implements CompilerPassInterface
{
    private const HTTP_CLIENT_PARAMETER_SERVICE_IDS = [
        'setono_bpost.http_client' => 'setono_bpost.http_client',
        'setono_postnl.http_client' => 'setono_postnl.http_client',
    ];

    public function process(ContainerBuilder $container): void
    {
        foreach (self::HTTP_CLIENT_PARAMETER_SERVICE_IDS as $PARAMETER => $SERVICE_ID) {
            if ($container->hasParameter($PARAMETER)) {
                if (!$container->has($container->getParameter($PARAMETER))) {
                    throw new ServiceNotFoundException($container->getParameter($PARAMETER));
                }

                $container->setAlias($SERVICE_ID, $container->getParameter($SERVICE_ID));
            } elseif ($container->has(BuzzClientInterface::class)) {
                $container->setAlias($SERVICE_ID, BuzzClientInterface::class);
            }
        }
    }
}
