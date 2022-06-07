<?php

namespace Setono\SyliusPickupPointPlugin\Model\Query\Bpost;

use Setono\SyliusPickupPointPlugin\Model\Query\CountryAwareInterface;

final class ServicePointQuery implements ServicePointQueryInterface, CountryAwareInterface
{
    private const ENDPOINT = '/Locator';

    private string $id;

    private string $function;

    private string $language;

    private ?string $street;

    private string $number;

    private string $zone;

    private string $partner;

    private string $account;

    private string $country;

    private int $type;

    private bool $checkData;

    private bool $checkList;

    private bool $checkOpen;

    private bool $info;

    private int $limit;

    private string $zip;

    public function __construct(string $partner)
    {
        $this->checkData = 1;
        $this->checkOpen = 1;
        $this->checkList = 1;
        $this->info = 1;
        $this->limit = 20;
        $this->street = null;

        $this->partner = $partner;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function getFunction(): string
    {
        return $this->function;
    }

    public function setFunction(string $function): void
    {
        if (!in_array($function, self::FUNCTIONS, true)) {
            throw new \LogicException(
                sprintf(
                    'Function: %s is not known, supported functions are: %s',
                    $function,
                    implode(', ', self::FUNCTIONS)
                )
            );
        }
        $this->function = $function;
    }

    public function getLanguage(): string
    {
        return $this->language;
    }

    public function setLanguage(string $language): void
    {
        $this->language = $language;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function setStreet(string $street): void
    {
        $this->street = $street;
    }

    public function getNumber(): string
    {
        return $this->number;
    }

    public function setNumber(string $number): void
    {
        $this->number = $number;
    }

    public function getZone(): string
    {
        return $this->zone;
    }

    public function setZone(string $zone): void
    {
        $this->zone = $zone;
    }

    public function getType(): int
    {
        return $this->type;
    }

    public function setType(int $type): void
    {
        if (!in_array($type, self::TYPES, true)) {
            throw new \LogicException(
                sprintf(
                    'Type: %s is not known, supported types are: %s',
                    $type,
                    implode(', ', self::TYPES)
                )
            );
        }
        $this->type = $type;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function setLimit(int $limit): void
    {
        $this->limit = $limit;
    }

    public function getPartner(): string
    {
        return $this->partner;
    }

    public function getAccount(): string
    {
        return $this->account;
    }

    public function setAccount(string $account): void
    {
        $this->account = $account;
    }

    public function getCountry(): string
    {
        return $this->country;
    }

    public function setCountry(string $country): void
    {
        $this->country = $country;
    }

    public function getEndPoint(): string {
        return self::ENDPOINT;
    }

    public function getCheckData(): int
    {
        return (int) $this->checkData;
    }

    public function setCheckData(bool $checkData): void
    {
        $this->checkData = $checkData;
    }

    public function getCheckList(): int
    {
        return (int) $this->checkList;
    }

    public function setCheckList(bool $checkList): void
    {
        $this->checkList = $checkList;
    }

    public function getCheckOpen(): int
    {
        return (int) $this->checkOpen;
    }

    public function setCheckOpen(bool $checkOpen): void
    {
        $this->checkOpen = $checkOpen;
    }

    public function getInfo(): int
    {
        return (int) $this->info;
    }

    public function setInfo(bool $info): void
    {
        $this->info = $info;
    }

    public function getZip(): string
    {
        return $this->zip;
    }

    public function setZip(string $zip): void
    {
        $this->zip = $zip;
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
