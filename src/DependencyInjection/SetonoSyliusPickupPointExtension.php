<?php

declare(strict_types=1);

namespace Setono\SyliusPickupPointPlugin\DependencyInjection;

use LogicException;
use Sylius\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractResourceExtension;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Webmozart\Assert\Assert;

final class SetonoSyliusPickupPointExtension extends AbstractResourceExtension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $config = $this->processConfiguration($this->getConfiguration([], $container), $configs);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $container->setParameter('setono_sylius_pickup_point.local', $config['local']);

        $this->registerResources('setono_sylius_pickup_point', $config['driver'], $config['resources'], $container);

        $loader->load('services.xml');

        $bundles = $container->hasParameter('kernel.bundles') ? $container->getParameter('kernel.bundles') : [];
        Assert::isArray($bundles);

        $cacheEnabled = $config['cache']['enabled'];
        if ($cacheEnabled) {
            if (!interface_exists(AdapterInterface::class)) {
                throw new LogicException('Using cache is only supported when symfony/cache is installed.');
            }

            if (null === $config['cache']['pool']) {
                throw new LogicException('You should specify pool in order to use cache for pickup point providers.');
            }

            $container->setAlias('setono_sylius_pickup_point.cache', $config['cache']['pool']);
        }

        $container->setParameter('setono_sylius_pickup_point.cache.enabled', $cacheEnabled);

        if ($config['providers']['faker']) {
            if ('prod' === $container->getParameter('kernel.environment')) {
                throw new LogicException("You can't use faker provider in production environment.");
            }

            $loader->load('services/providers/faker.xml');
        }

        if ($config['providers']['dao']) {
            if (!isset($bundles['SetonoDAOBundle'])) {
                throw new LogicException('You should use SetonoDAOBundle or disable dao provider.');
            }

            $loader->load('services/providers/dao.xml');
        }

        if ($config['providers']['gls']) {
            if (!isset($bundles['SetonoGlsWebserviceBundle'])) {
                throw new LogicException('You should use SetonoGlsWebserviceBundle or disable gls provider.');
            }

            $loader->load('services/providers/gls.xml');
        }

        if ($config['providers']['post_nord']) {
            if (!isset($bundles['SetonoPostNordBundle'])) {
                throw new LogicException('You should use SetonoPostNordBundle or disable post_nord provider.');
            }

            $loader->load('services/providers/post_nord.xml');
        }

        if ($config['providers']['bpost'] && $config['providers']['bpost']['enabled'] === true) {
            $bpostConfig = $config['providers']['bpost'];
            $container->setParameter('setono_bpost.base_url', $bpostConfig['base_url']);

            if (isset($bpostConfig['http_client'])) {
                $container->setParameter('setono_bpost.http_client', $bpostConfig['http_client']);
            }

            if (isset($bpostConfig['request_factory'])) {
                $container->setParameter('setono_bpost.request_factory', $bpostConfig['request_factory']);
            }

            if (isset($bpostConfig['stream_factory'])) {
                $container->setParameter('setono_bpost.stream_factory', $bpostConfig['stream_factory']);
            }

            if (isset($bpostConfig['partner_id'])) {
                $container->setParameter('setono_bpost.partner_id', $bpostConfig['partner_id']);
            }

            $loader->load('services/providers/bpost.xml');
            $loader->load('services/clients/bpost.xml');
            $loader->load('services/transformers/bpost.xml');
        }

        if ($config['providers']['postnl'] && $config['providers']['postnl']['enabled'] === true) {
            $postnlConfig = $config['providers']['postnl'];

            $container->setParameter('setono_postnl.base_url', $postnlConfig['base_url']);

            if (isset($postnlConfig['http_client'])) {
                $container->setParameter('setono_postnl.http_client', $postnlConfig['http_client']);
            }

            if (isset($postnlConfig['request_factory'])) {
                $container->setParameter('setono_postnl.request_factory', $postnlConfig['request_factory']);
            }

            if (isset($postnlConfig['stream_factory'])) {
                $container->setParameter('setono_postnl.stream_factory', $postnlConfig['stream_factory']);
            }

            if (isset($postnlConfig['api_key'])) {
                $container->setParameter('setono_postnl.api_key', $postnlConfig['api_key']);
            }

            if (isset($postnlConfig['address_class'])) {
                $container->setParameter('setono_postnl.address_class', $postnlConfig['address_class']);
            }

            $loader->load('services/providers/postnl.xml');
            $loader->load('services/clients/postnl.xml');
            $loader->load('services/transformers/postnl.xml');
        }
    }
}
