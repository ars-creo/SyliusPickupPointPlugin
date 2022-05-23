<?php

namespace Setono\SyliusPickupPointPlugin\Model\Query\PostNL;

use Setono\SyliusPickupPointPlugin\Model\Query\ServicePointQueryInterface;

final class ServicePointQuery implements ServicePointQueryInterface
{
    private const ENDPOINT = '/shipment/v2_1/locations/nearest';

    private string $id;

    private string $countryCode;

    private string $postalCode;

    private string $city;

    private string $street;

    private string $houseNumber;

    private \DateTime $deliveryDate;

    private \DateTime $openingTime;

    private array $deliveryOptions;

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function getStreet(): string
    {
        return $this->street;
    }

    public function setStreet(string $street): void
    {
        $this->street = $street;
    }

    public function getCountryCode(): string
    {
        return $this->countryCode;
    }

    public function setCountryCode(string $countryCode): void
    {
        $this->countryCode = $countryCode;
    }

    public function getPostalCode(): string
    {
        return $this->postalCode;
    }

    public function setPostalCode(string $postalCode): void
    {
        $this->postalCode = $postalCode;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function setCity(string $city): void
    {
        $this->city = $city;
    }

    public function getHouseNumber(): string
    {
        return $this->houseNumber;
    }

    public function setHouseNumber(string $houseNumber): void
    {
        $this->houseNumber = $houseNumber;
    }

    public function getDeliveryDate(): \DateTime
    {
        return $this->deliveryDate;
    }

    public function setDeliveryDate(\DateTime $deliveryDate): void
    {
        $this->deliveryDate = $deliveryDate;
    }

    public function getOpeningTime(): \DateTime
    {
        return $this->openingTime;
    }

    public function setOpeningTime(\DateTime $openingTime): void
    {
        $this->openingTime = $openingTime;
    }

    public function getDeliveryOptions(): array
    {
        return $this->deliveryOptions;
    }

    public function setDeliveryOptions(array $deliveryOptions): void
    {
        $this->deliveryOptions = $deliveryOptions;
    }

    public function getEndPoint(): string {
        return self::ENDPOINT;
    }

    public function toArray(): array
    {
        $arrayValue = [];
        foreach(get_object_vars($this) as $key => $value) {
            if (is_bool($value)) {
                $value = (int) $value;
            }
            $arrayValue[ucfirst($key)] = $value;
        }
        return $arrayValue;
    }
}
