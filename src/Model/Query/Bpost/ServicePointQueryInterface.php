<?php

namespace Setono\SyliusPickupPointPlugin\Model\Query\Bpost;

use Setono\SyliusPickupPointPlugin\Model\Query\ServicePointQueryInterface as BaseServicePointQueryInterface;

interface ServicePointQueryInterface extends BaseServicePointQueryInterface
{
    public const FUNCTIONS = [
        self::FUNCTION_SEARCH,
        self::FUNCTION_GET_ALL_SERVICE_POINTS,
        self::FUNCTION_INFO
    ];

    public const FUNCTION_GET_ALL_SERVICE_POINTS = 'getallservicepoints';
    public const FUNCTION_INFO = 'info';
    public const FUNCTION_SEARCH = 'search';

    public const TYPE_POST_OFFICE = 'PostOffice';
    public const TYPE_POST_POINT = 'PostPoint';
    public const TYPE_PACK_STATION = 'PackStation';
    public const TYPE_SHOP = 'Shop';
    public const TYPE_KARIBOO = 'Kariboo';

    public const TYPES = [
        self::TYPE_POST_OFFICE => 1,
        self::TYPE_POST_POINT => 2,
        self::TYPE_PACK_STATION => 4,
        self::TYPE_SHOP => 8,
        self::TYPE_KARIBOO => 16,
    ];
}
