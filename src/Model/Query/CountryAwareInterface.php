<?php

namespace Setono\SyliusPickupPointPlugin\Model\Query;

interface CountryAwareInterface
{
    public function getCountry(): string;

    public function setCountry(string $country): void;
}
