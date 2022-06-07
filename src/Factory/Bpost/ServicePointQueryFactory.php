<?php

namespace Setono\SyliusPickupPointPlugin\Factory\Bpost;

use Setono\SyliusPickupPointPlugin\Factory\ServicePointQueryFactoryInterface;
use Setono\SyliusPickupPointPlugin\Model\Query\Bpost\ServicePointQuery;
use Setono\SyliusPickupPointPlugin\Model\Query\Bpost\ServicePointQueryInterface;
use Setono\SyliusPickupPointPlugin\Model\PickupPointCode;
use Sylius\Component\Core\Model\OrderInterface;

class ServicePointQueryFactory implements ServicePointQueryFactoryInterface
{
    private string $partnerId;

    public function __construct(string $partnerId)
    {
        $this->partnerId = $partnerId;
    }

    public function createServicePointQueryForOrder(OrderInterface $order): ServicePointQueryInterface
    {
        $servicePointQuery = new ServicePointQuery($this->partnerId);
        $servicePointQuery->setFunction(ServicePointQueryInterface::FUNCTION_SEARCH);
        $servicePointQuery->setLimit(20);

        $shippingAddress = $order->getShippingAddress();
        if (null === $shippingAddress) {
            return $servicePointQuery;
        }

        $street = $shippingAddress->getStreet();
        $postCode = $shippingAddress->getPostcode();
        $countryCode = $shippingAddress->getCountryCode();

        if ($street !== null) {
            $servicePointQuery->setStreet($street);
        }

        if ($countryCode !== null) {
            $servicePointQuery->setCountry($countryCode);
        }

        if ($postCode !== null) {
            $servicePointQuery->setZone($postCode);
        }

        return $servicePointQuery;
    }

    public function createServicePointQueryForPickupPoint(PickupPointCode $pickupPointCode): ServicePointQueryInterface
    {
        $servicePointQuery = new ServicePointQuery($this->partnerId);
        $servicePointQuery->setFunction(ServicePointQueryInterface::FUNCTION_INFO);
        $servicePointQuery->setId($pickupPointCode->getIdPart());
        $servicePointQuery->setCountry($pickupPointCode->getCountryPart());

        return $servicePointQuery;
    }

    public function createServicePointQueryForAllPickupPoints(string $countryCode, ?string $postalCode = null): ServicePointQueryInterface
    {
        $servicePointQuery = new ServicePointQuery($this->partnerId);
        $servicePointQuery->setFunction(ServicePointQueryInterface::FUNCTION_GET_ALL_SERVICE_POINTS);
        $servicePointQuery->setAccount($this->partnerId);
        $servicePointQuery->setCountry($countryCode);

        if ($postalCode !== null) {
            $servicePointQuery->setZip($postalCode);
        }

        return $servicePointQuery;
    }
}
