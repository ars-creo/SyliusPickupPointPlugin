<?php

namespace Setono\SyliusPickupPointPlugin\Model\Query\PostNL;

use Setono\SyliusPickupPointPlugin\Model\Query\ServicePointQueryInterface;

interface ServicePointLookupInterface extends ServicePointQueryInterface
{
    public const RETAIL_NETWORK_IDS = [
        'BE' => self::RETAIL_NETWORK_ID_BE,
        'NL' => self::RETAIL_NETWORK_ID_NL,
    ];

    public const RETAIL_NETWORK_ID_BE = 'PNPBE-01';
    public const RETAIL_NETWORK_ID_NL = 'PNPNL-01';
}
