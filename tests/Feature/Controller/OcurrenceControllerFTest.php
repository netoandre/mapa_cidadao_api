<?php

namespace Tests\Feature;

use App\Models\Ocurrence;
use App\Models\TypeOcurrence;
use App\Models\User;
use Clickbar\Magellan\Data\Geometries\Point;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OcurrenceControllerFTest extends TestCase
{
    use RefreshDatabase;

    public function test_must_list_ocurrences(): void
    {
        Ocurrence::factory()->count(3)->create();

        $response = $this->getJson('/api/ocurrences');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'ocurrences' => [
                    '*' => ['id', 'type_id', 'user_id', 'description', 'address_name', 'city', 'state', 'country', 'is_active', 'created_at', 'updated_at'],
                ],
            ]);
    }

    public function test_user_auth_can_create_ocurrence()
    {
        $user = User::factory()->create();
        $type = TypeOcurrence::factory()->create();

        $payload = [
            'location' => [
                'type'        => 'Point',
                'coordinates' => [-47.9292, -15.7801],
            ],
            'type_id'      => $type->id,
            'description'  => 'Buraco na rua que está dificultando o tráfego',
            'address_name' => 'Rua das Palmeiras, 123',
            'city'         => 'Belém',
            'state'        => 'PA',
            'country'      => 'Brasil',
        ];

        $response = $this->actingAs($user)->postJson('/api/ocurrences', $payload);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'ocurrence' => ['id', 'type_id', 'user_id', 'description', 'location', 'address_name', 'city', 'state', 'country', 'created_at'],
            ]);
        $expectedResult = $payload;

        $expectedResult['user_id']  = $user->id;
        $expectedResult['location'] = Point::makeGeodetic($payload['location']['coordinates'][0], $payload['location']['coordinates'][1]);
        $this->assertDatabaseHas('ocurrences', $expectedResult);
    }

    public function test_user_unauth_canot_create_ocurrence()
    {
        $user = User::factory()->create();
        $type = TypeOcurrence::factory()->create();

        $payload = [
            'location' => [
                'type'        => 'Point',
                'coordinates' => [-47.9292, -15.7801],
            ],
            'type_id'      => $type->id,
            'description'  => 'Buraco na rua que está dificultando o tráfego',
            'address_name' => 'Rua das Palmeiras, 123',
            'city'         => 'Belém',
            'state'        => 'PA',
            'country'      => 'Brasil',
        ];

        $response = $this->postJson('/api/ocurrences', $payload);

        $response->assertStatus(401);

        $expectedResult = $payload;

        $expectedResult['user_id']  = $user->id;
        $expectedResult['location'] = Point::makeGeodetic($payload['location']['coordinates'][0], $payload['location']['coordinates'][1]);
        $this->assertDatabaseMissing('ocurrences', $expectedResult);
    }

    public function test_auth_user_can_delete_ocurrence()
    {
        $user      = User::factory()->create();
        $ocurrence = Ocurrence::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->deleteJson("/api/ocurrences/{$ocurrence->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('ocurrences', [
            'id' => $ocurrence->id,
        ]);
    }

    public function test_other_auth_user_cannot_delete_ocurrence()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $ocurrence = Ocurrence::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($otherUser)->deleteJson("/api/ocurrences/{$ocurrence->id}");

        $response->assertStatus(403);

        $this->assertDatabaseHas('ocurrences', [
            'id' => $ocurrence->id,
        ]);
    }

    public function test_unauth_user_cannot_delete_ocurrence()
    {
        $user      = User::factory()->create();
        $ocurrence = Ocurrence::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->deleteJson("/api/ocurrences/{$ocurrence->id}");

        $response->assertStatus(401);

        $this->assertDatabaseHas('ocurrences', [
            'id' => $ocurrence->id,
        ]);
    }
}
