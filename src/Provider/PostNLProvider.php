<?php

namespace Setono\SyliusPickupPointPlugin\Provider;

use Doctrine\Persistence\ManagerRegistry;
use Psr\Http\Client\NetworkExceptionInterface;
use Setono\SyliusPickupPointPlugin\Client\ClientInterface;
use Setono\SyliusPickupPointPlugin\Exception\TimeoutException;
use Setono\SyliusPickupPointPlugin\Factory\PostNL\ServicePointQueryFactory;
use Setono\SyliusPickupPointPlugin\Factory\ServicePointQueryFactoryInterface;
use Setono\SyliusPickupPointPlugin\Model\PickupPointCode;
use Setono\SyliusPickupPointPlugin\Model\PickupPointInterface;
use Setono\SyliusPickupPointPlugin\Transformer\PickupPointTransformerInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Core\Model\OrderInterface;
use Webmozart\Assert\Assert;

/**
 * @see https://developer.postnl.nl/browse-apis/delivery-options/location-webservice/testtool-rest/#/default/get_v2_1_locations_nearest
 */
final class PostNLProvider extends Provider
{
    public const CODE = 'postnl';
    public const NAME = 'PostNL';

    private ClientInterface $client;
    private PickupPointTransformerInterface $pickupPointTransformer;
    private ManagerRegistry $managerRegistry;
    private ?ServicePointQueryFactory $servicePointQueryFactory;

    private array $countryCodes;
    private string $addressClassString;

    public function __construct(
        ClientInterface $client,
        PickupPointTransformerInterface $pickupPointTransformer,
        ManagerRegistry $managerRegistry,
        string $addressClassString,
        ?ServicePointQueryFactoryInterface $servicePointQueryFactory = null,
        array $countryCodes = ['BE', 'NL']
    ) {
        $this->client = $client;
        $this->countryCodes = $countryCodes;
        $this->servicePointQueryFactory = $servicePointQueryFactory;
        $this->pickupPointTransformer = $pickupPointTransformer;
        $this->managerRegistry = $managerRegistry;
        $this->addressClassString = $addressClassString;
    }

    public function findPickupPoints(OrderInterface $order): iterable
    {
        $servicePointQuery = $this->getServicePointQueryFactory()->createServicePointQueryForOrder($order);
        $servicePoints = $this->client->locate($servicePointQuery);
        foreach ($servicePoints as $item) {
            yield $this->transform($item);
        }
    }

    public function findPickupPoint(PickupPointCode $code): ?PickupPointInterface
    {
        try {
            $servicePointQuery = $this->getServicePointQueryFactory()->createServicePointQueryForPickupPoint($code);
            $servicePoint = $this->client->locate($servicePointQuery);
        } catch (NetworkExceptionInterface $e) {
            throw new TimeoutException($e);
        }
        $servicePoints = [];
        foreach ($servicePoint as $index => $point) {
            $servicePoints[$index] = $point;
        }
        if (\count($servicePoints) < 1) {
            return null;
        }
        return $this->transform($servicePoints);
    }

    /**
     * As there is currently no good alternative to query all pickup points for a given country, this will be provided
     * as an alternative.
     */
    public function findAllPickupPoints(): iterable
    {
        if (!class_exists($this->addressClassString)) {
            throw new \InvalidArgumentException(sprintf("Class '%s' does not exist", $this->addressClassString));
        }

        $manager = $this->managerRegistry->getManagerForClass($this->addressClassString);
        Assert::notNull($manager);

        /** @var EntityRepository $repository */
        $repository = $manager->getRepository($this->addressClassString);
        try {
            foreach ($this->countryCodes as $countryCode) {
                $qb = $repository->createQueryBuilder('sa');
                $postalCodes = $qb->distinct()->select('sa.postcode')
                    ->where('sa.countryCode = :countryCode')
                    ->setParameter('countryCode', $countryCode)
                    ->getQuery()->getResult();

                $postalCodes = array_map(static function (array $code) {
                    return $code['postcode'];
                }, $postalCodes);

                foreach ($postalCodes as $postalCode) {
                    $servicePointQuery = $this->getServicePointQueryFactory()
                        ->createServicePointQueryForAllPickupPoints($countryCode, $postalCode);
                    $servicePoints = $this->client->locate($servicePointQuery);
                    foreach ($servicePoints as $item) {
                        yield $this->transform($item);
                    }
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
            $this->servicePointQueryFactory = new ServicePointQueryFactory();
        }
        return $this->servicePointQueryFactory;
    }

    private function transform(array $servicePoint): PickupPointInterface
    {
        return $this->pickupPointTransformer->transform($servicePoint, $this->getCode());
    }
}
