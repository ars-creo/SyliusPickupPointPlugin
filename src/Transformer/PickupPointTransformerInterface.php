<?php

namespace Setono\SyliusPickupPointPlugin\Transformer;

use Setono\SyliusPickupPointPlugin\Model\PickupPointInterface;

interface PickupPointTransformerInterface
{
    public function transform(array $servicePoint, string $providerCode): PickupPointInterface;
}
