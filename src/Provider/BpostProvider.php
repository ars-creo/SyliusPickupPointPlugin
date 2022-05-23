<?php

namespace Setono\SyliusPickupPointPlugin\Provider;

use Psr\Http\Client\NetworkExceptionInterface;
use Setono\SyliusPickupPointPlugin\Client\ClientInterface;
use Setono\SyliusPickupPointPlugin\Factory\Bpost\ServicePointQueryFactory;
use Setono\SyliusPickupPointPlugin\Factory\ServicePointQueryFactoryInterface;
use Setono\SyliusPickupPointPlugin\Model\Query\Bpost\ServicePointQueryInterface;
use Setono\SyliusPickupPointPlugin\Transformer\PickupPointTransformerInterface;
use Setono\SyliusPickupPointPlugin\Exception\TimeoutException;
use Setono\SyliusPickupPointPlugin\Model\PickupPointCode;
use Setono\SyliusPickupPointPlugin\Model\PickupPointInterface;
use Sylius\Component\Core\Model\OrderInterface;

/**
 * @see https://pudo.bpost.be/ServicePointQuery for more information
 */
final class BpostProvider extends Provider
{
    public const CODE = 'bpost';
    public const NAME = 'Bpost';

    private ClientInterface $client;
    private ?ServicePointQueryFactoryInterface $servicePointQueryFactory;
    private PickupPointTransformerInterface $pickupPointTransformer;

    private array $countryCodes;
    private string $partnerId;

    public function __construct(
        ClientInterface $client,
        PickupPointTransformerInterface $pickupPointTransformer,
        string $partnerId,
        array $countryCodes = ['BE', 'NL'],
        ?ServicePointQueryFactoryInterface $servicePointQueryFactory = null
    ) {
        $this->client = $client;
        $this->countryCodes = $countryCodes;
        $this->servicePointQueryFactory = $servicePointQueryFactory;
        $this->partnerId = $partnerId;
        $this->pickupPointTransformer = $pickupPointTransformer;
    }

    public function findPickupPoints(OrderInterface $order): iterable
    {
        $shippingAddress = $order->getShippingAddress();
        if (null === $shippingAddress) {
            return [];
        }

        $countryCode = $shippingAddress->getCountryCode();
        if (null === $countryCode) {
            return [];
        }

        $servicePointQuery = $this->getServicePointQueryFactory()->createServicePointQueryForOrder($order);
        $servicePoints = $this->client->locate($servicePointQuery);
        foreach ($servicePoints as $item) {
            $item['country'] = $countryCode;
            yield $this->transform($item);
        }
    }

    public function findPickupPoint(PickupPointCode $code): ?PickupPointInterface
    {
        $servicePoints = [];
        try {
            $servicePointQuery = $this->getServicePointQueryFactory()->createServicePointQueryForPickupPoint($code);
            foreach (ServicePointQueryInterface::TYPES as $type) {
                $servicePointQuery->setType($type);
                $servicePoints = $this->client->locate($servicePointQuery);
                if (count($servicePoints) > 0) {
                    break;
                }
            }
        } catch (NetworkExceptionInterface $e) {
            throw new TimeoutException($e);
        }

        if (count($servicePoints) < 1) {
            return null;
        }

        $servicePoint = $servicePoints[0];
        $servicePoint['country'] = $code->getCountryPart();
        return $this->transform($servicePoint);
    }

    public function findAllPickupPoints(): iterable
    {
        try {
            foreach ($this->countryCodes as $countryCode) {
                $servicePointQuery = $this->getServicePointQueryFactory()->createServicePointQueryForAllPickupPoints($countryCode);

                $servicePointQuery->setCountry($countryCode);
                $servicePoints = $this->client->locate($servicePointQuery);
                foreach ($servicePoints as $item) {
                    $item['country'] = $countryCode;
                    yield $this->transform($item);
                }
            }
        } catch (NetworkExceptionInterface $e) {
            throw new TimeoutException($e);
        }
    }

    public function getCode(): string
    {
        return self::CODE;
    }

    public function getName(): string
    {
        return self::NAME;
    }

    private function getServicePointQueryFactory(): ServicePointQueryFactoryInterface
    {
        if ($this->servicePointQueryFactory === null) {
            $this->servicePointQueryFactory = new ServicePointQueryFactory($this->partnerId);
        }
        return $this->servicePointQueryFactory;
    }

    private function transform(array $servicePoint): PickupPointInterface
    {
        return $this->pickupPointTransformer->transform($servicePoint, $this->getCode());
    }
}
