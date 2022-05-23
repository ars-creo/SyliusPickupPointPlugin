<?php

namespace Setono\SyliusPickupPointPlugin\Transformer\Bpost;

use Setono\SyliusPickupPointPlugin\Model\PickupPointCode;
use Setono\SyliusPickupPointPlugin\Model\PickupPointInterface;
use Setono\SyliusPickupPointPlugin\Transformer\PickupPointTransformerInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Webmozart\Assert\Assert;

final class PickupPointTransformer implements PickupPointTransformerInterface
{
    private FactoryInterface $pickupPointFactory;

    public function __construct(FactoryInterface $pickupPointFactory)
    {
        $this->pickupPointFactory = $pickupPointFactory;
    }

    public function transform(array $servicePoint, string $providerCode): PickupPointInterface
    {
        $servicePointSanitized = $this->arrayChangeKeyCaseRecursive($servicePoint);
        $id = new PickupPointCode(
            $servicePointSanitized['id'],
            $providerCode,
            $servicePointSanitized['country'] ?? $servicePointSanitized['city']
        );

        $address = trim(
            sprintf(
                '%s %s',
                $servicePointSanitized['street'] ?? '',
                $servicePointSanitized['number'] ?? $servicePointSanitized['nr'] ?? '',
            )
        );
        $latitude =  $servicePointSanitized['latitude'] ? (float) $servicePointSanitized['latitude'] : null;
        $longitude =  $servicePointSanitized['longitude'] ? (float) $servicePointSanitized['longitude'] : null;

        /** @var PickupPointInterface|object $pickupPoint */
        $pickupPoint = $this->pickupPointFactory->createNew();
        Assert::isInstanceOf($pickupPoint, PickupPointInterface::class);
        $pickupPoint->setCode($id);
        $pickupPoint->setName($servicePointSanitized['name'] ?? $servicePointSanitized['office']);
        $pickupPoint->setAddress($address);
        $pickupPoint->setZipCode((string) $servicePointSanitized['zip']);
        $pickupPoint->setCity($servicePointSanitized['city']);
        $pickupPoint->setCountry($servicePointSanitized['country']);
        $pickupPoint->setLatitude($latitude);
        $pickupPoint->setLongitude($longitude);

        return $pickupPoint;
    }

    private function arrayChangeKeyCaseRecursive($arr, $case = CASE_LOWER): array
    {
        return array_map(function($item) use($case) {
            if(is_array($item)) {
                $item = $this->arrayChangeKeyCaseRecursive($item, $case);
            }
            return $item;
        },array_change_key_case($arr, $case));
    }
}
