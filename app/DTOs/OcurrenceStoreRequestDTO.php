<?php

namespace App\DTOs;

/**
 * @property-read LocationDTO $location
 * @property-read int $typeId
 * @property-read string|null $description
 * @property-read string|null $addressName
 * @property-read string|null $city
 * @property-read string|null $state
 * @property-read string|null $country
 */
class OcurrenceStoreRequestDTO
{
    public readonly LocationDTO $location;

    public readonly int $typeId;

    public readonly ?string $description;

    public readonly ?string $addressName;

    public readonly ?string $city;

    public readonly ?string $state;

    public readonly ?string $country;

    public function __construct(array $data)
    {
        $this->location    = new LocationDTO($data['location']);
        $this->typeId      = $data['type_id'];
        $this->description = $data['description']  ?? '';
        $this->addressName = $data['address_name'] ?? '';
        $this->city        = $data['city']         ?? null;
        $this->state       = $data['state']        ?? null;
        $this->country     = $data['country']      ?? null;
    }

    public function toArray(): array
    {
        return [
            'location'     => $this->location->toArray(),
            'type_id'      => $this->typeId,
            'description'  => $this->description,
            'address_name' => $this->addressName,
            'city'         => $this->city,
            'state'        => $this->state,
            'country'      => $this->country,
        ];
    }
}
