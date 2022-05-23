<?php

namespace Setono\SyliusPickupPointPlugin\Factory\PostNL;

use Setono\SyliusPickupPointPlugin\Factory\ServicePointQueryFactoryInterface;
use Setono\SyliusPickupPointPlugin\Model\PickupPointCode;
use Setono\SyliusPickupPointPlugin\Model\Query\PostNL\ServicePointLookup;
use Setono\SyliusPickupPointPlugin\Model\Query\PostNL\ServicePointLookupInterface;
use Setono\SyliusPickupPointPlugin\Model\Query\PostNL\ServicePointQuery;
use Setono\SyliusPickupPointPlugin\Model\Query\ServicePointQueryInterface;
use Sylius\Component\Core\Model\OrderInterface;

class ServicePointQueryFactory implements ServicePointQueryFactoryInterface
{
    public function createServicePointQueryForOrder(OrderInterface $order): ServicePointQueryInterface
    {
        $servicePointQuery = new ServicePointQuery();

        $shippingAddress = $order->getShippingAddress();
        if (null === $shippingAddress) {
            return $servicePointQuery;
        }

        $street = $shippingAddress->getStreet();
        $postCode = $shippingAddress->getPostcode();
        $countryCode = $shippingAddress->getCountryCode();
        $city = $shippingAddress->getCity();

        if ($street !== null) {
            $servicePointQuery->setStreet($street);
        }

        if ($countryCode !== null) {
            $servicePointQuery->setCountryCode($countryCode);
        }

        if ($postCode !== null) {
            $servicePointQuery->setPostalCode($postCode);
        }

        if ($city !== null) {
            $servicePointQuery->setCity($city);
        }

        return $servicePointQuery;
    }

    public function createServicePointQueryForPickupPoint(PickupPointCode $pickupPointCode): ServicePointQueryInterface
    {
        return new ServicePointLookup(
            $pickupPointCode->getIdPart(),
            ServicePointLookupInterface::RETAIL_NETWORK_IDS[$pickupPointCode->getCountryPart()]
        );
    }

    public function createServicePointQueryForAllPickupPoints(string $countryCode, ?string $postalCode = null): ServicePointQueryInterface
    {
        $servicePointQuery = new ServicePointQuery();
        $servicePointQuery->setCountryCode($countryCode);

        if ($postalCode !== null) {
            $servicePointQuery->setPostalCode($postalCode);
        }

        return $servicePointQuery;
    }
}
