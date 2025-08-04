<?php

namespace Database\Factories;

use App\Enums\TypeOcurrenceEnum;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TypeOcurrence>
 */
class TypeOcurrenceFactory extends Factory
{
    use HasFactory;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->randomElement(TypeOcurrenceEnum::values()),
        ];
    }
}
