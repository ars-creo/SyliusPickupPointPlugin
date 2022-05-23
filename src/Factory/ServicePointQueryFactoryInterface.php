<?php

namespace Setono\SyliusPickupPointPlugin\Factory;

use Setono\SyliusPickupPointPlugin\Model\PickupPointCode;
use Setono\SyliusPickupPointPlugin\Model\Query\ServicePointQueryInterface;
use Sylius\Component\Core\Model\OrderInterface;

interface ServicePointQueryFactoryInterface
{
    public function createServicePointQueryForOrder(OrderInterface $order): ServicePointQueryInterface;

    public function createServicePointQueryForPickupPoint(PickupPointCode $pickupPointCode): ServicePointQueryInterface;

    public function createServicePointQueryForAllPickupPoints(string $countryCode, ?string $postalCode = null): ServicePointQueryInterface;
}
