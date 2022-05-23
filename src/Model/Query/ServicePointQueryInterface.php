<?php

namespace Setono\SyliusPickupPointPlugin\Model\Query;

interface ServicePointQueryInterface
{
    public function getEndPoint(): string;

    public function toArray(): array;
}
