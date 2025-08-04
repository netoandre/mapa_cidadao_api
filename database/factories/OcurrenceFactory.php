<?php

namespace Database\Factories;

use App\Models\Ocurrence;
use App\Models\TypeOcurrence;
use App\Models\User;
use Clickbar\Magellan\Data\Geometries\Point;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ocurrence>
 */
class OcurrenceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Ocurrence::class;

    public function definition(): array
    {
        return [
            'location'     => Point::makeGeodetic($this->faker->latitude(), $this->faker->longitude()),
            'type_id'      => TypeOcurrence::factory(),
            'user_id'      => User::factory(),
            'description'  => $this->faker->paragraph(),
            'address_name' => $this->faker->streetAddress(),
            'city'         => $this->faker->city(),
            'state'        => $this->faker->stateAbbr(),
            'country'      => $this->faker->country(),
            'is_active'    => true,
        ];
    }
}
