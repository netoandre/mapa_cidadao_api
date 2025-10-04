<?php

namespace App\DTOs;

use DateTime;

/**
 * @property string      $type_closure
 * @property string|null $solution_description
 * @property DateTime|null $resolution_date
 */
class InactiveOcurrenceRequestDTO
{
    public string $type_closure;

    public ?string $solution_description;
    public ?DateTime $resolution_date;

    public function __construct(array $data)
    {
        $this->type_closure = $data['type_closure'];
        $this->solution_description = $data['solution_description'] ?? null;
        $this->resolution_date = isset($data['resolution_date']) ? new DateTime($data['resolution_date']) : null;
    }
}
