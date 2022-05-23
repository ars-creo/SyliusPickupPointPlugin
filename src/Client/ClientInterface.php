<?php

declare(strict_types=1);

namespace Setono\SyliusPickupPointPlugin\Client;

use Setono\SyliusPickupPointPlugin\Model\Query\ServicePointQueryInterface;

interface ClientInterface
{
    public function locate(ServicePointQueryInterface $servicePointQuery): iterable;
}
