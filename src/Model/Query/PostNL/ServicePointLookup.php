<?php

namespace Setono\SyliusPickupPointPlugin\Model\Query\PostNL;

final class ServicePointLookup implements ServicePointLookupInterface
{
    private const ENDPOINT = '/shipment/v2_1/locations/lookup';

    private string $retailNetworkId;

    private string $locationCode;

    public function __construct(string $locationCode, string $retailNetworkId)
    {
        $this->locationCode = $locationCode;
        $this->retailNetworkId = $retailNetworkId;
    }

    public function getEndPoint(): string
    {
        return self::ENDPOINT;
    }

    public function toArray(): array
    {
        return [
            'RetailNetorkID' => $this->retailNetworkId,
            'LocationCode' => $this->locationCode,
        ];
    }
}
