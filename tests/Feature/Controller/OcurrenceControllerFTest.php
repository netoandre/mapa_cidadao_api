<?php

namespace Tests\Feature;

use App\Enums\TypeOcurrenceClosure;
use App\Models\Ocurrence;
use App\Models\TypeOcurrence;
use App\Models\User;
use Clickbar\Magellan\Data\Geometries\Point;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OcurrenceControllerFTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('db:seed');
    }

    public function test_must_list_ocurrences(): void
    {
        $ocurrenceActive = Ocurrence::factory(['is_active' => true])->count(3)->create();
        Ocurrence::factory(['is_active' => false])->count(3)->create();

        $response = $this->getJson('/api/ocurrences');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'ocurrences' => [
                    '*' => ['id', 'type_id', 'user_id', 'description', 'address_name', 'city', 'state', 'country', 'is_active', 'created_at', 'updated_at'],
                ],
            ]);

        $response->assertJsonPath('ocurrences.*.is_active', [true, true, true]);

        $this->assertEmpty(collect($response->json('ocurrences'))->where('is_active', false));
        $this->assertCount(3, $response->json('ocurrences'));

    }

    public static function validPayloads(): array
    {

        return [
            'payload mínimo válido' => [
                [
                    'location' => [
                        'type'        => 'Point',
                        'coordinates' => [-46.633308, -23.55052], // São Paulo
                    ],

                    'address_name' => 'Av. Paulista, 1000',
                ],
            ],
            'payload completo válido' => [
                [
                    'location' => [
                        'type'        => 'Point',
                        'coordinates' => [-43.209373, -22.911014], // Rio de Janeiro
                    ],

                    'description'  => 'Problema de iluminação em via pública',
                    'address_name' => 'Rua das Flores, 123',
                    'city'         => 'Rio de Janeiro',
                    'state'        => 'RJ',
                    'country'      => 'Brasil',
                ],
            ],
        ];
    }

    /**
     * @dataProvider validPayloads
     */
    public function test_user_auth_can_create_ocurrence(array $payload): void
    {
        $user = User::factory()->create();
        $type = TypeOcurrence::factory()->create(); // cria aqui

        $payload['type_id'] = $type->id;

        // Envia requisição
        $response = $this->actingAs($user)->postJson('/api/ocurrences', $payload);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'ocurrence' => [
                    'id',
                    'type_id',
                    'user_id',
                    'description',
                    'location',
                    'address_name',
                    'city',
                    'state',
                    'country',
                    'created_at',
                ],
            ]);

        // Prepara dados esperados
        $expected             = $payload;
        $expected['user_id']  = $user->id;
        $expected['location'] = Point::makeGeodetic(
            $payload['location']['coordinates'][0],
            $payload['location']['coordinates'][1]
        );

        // Preencher description e address_name com "" se não estiverem presentes
        foreach (['description', 'address_name'] as $field) {
            if (! array_key_exists($field, $expected)) {
                $expected[$field] = '';
            }
        }

        // Campos city, state, country ficam como null se não estiverem no payload
        foreach (['city', 'state', 'country'] as $field) {
            if (! array_key_exists($field, $expected)) {
                $expected[$field] = null;
            }
        }
        $this->assertDatabaseHas('ocurrences', $expected);
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

    public static function payloadsInactivateOcurrence(): array
    {

        return [
            [
                [
                    'type_closure'         => TypeOcurrenceClosure::RESOLVED->value,
                    'solution_description' => 'Tudo Certo',
                    'resolution_date'      => now()->toDateString(),
                ],
            ],

            [
                [
                    'type_closure' => TypeOcurrenceClosure::MISTAKE->value,
                ],
            ],
            [
                [
                    'type_closure'         => TypeOcurrenceClosure::OTHER->value,
                    'solution_description' => 'Tudo Certo',
                    'resolution_date'      => now()->toDateString(),
                ],
            ],
        ];
    }

    /**
     * @dataProvider payloadsInactivateOcurrence
     */
    public function test_auth_user_can_inactivate_ocurrence(array $payload): void
    {
        $user      = User::factory()->create();
        $ocurrence = Ocurrence::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->postJson("/api/ocurrences/inactivate/{$ocurrence->id}", $payload);

        $response->assertStatus(200);

        if ($payload['type_closure'] === TypeOcurrenceClosure::MISTAKE->value) {
            $this->assertDatabaseMissing('ocurrences', [
                'id'      => $ocurrence->id,
                'user_id' => $user->id,
            ]);
        } else {

            $this->assertDatabaseHas('ocurrences', [
                'id'                   => $ocurrence->id,
                'is_active'            => false,
                'type_closure'         => $payload['type_closure'],
                'solution_description' => $payload['solution_description'] ?? null,
            ]);
        }

    }

    /**
     * @dataProvider payloadsInactivateOcurrence
     */
    public function test_other_auth_user_can_not_inactivate_ocurrence(array $payload): void
    {
        $user      = User::factory()->create();
        $otherUser = User::factory()->create();
        $ocurrence = Ocurrence::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($otherUser)->postJson("/api/ocurrences/inactivate/{$ocurrence->id}", $payload);

        $response->assertStatus(403);

        $this->assertDatabaseMissing('ocurrences', [
            'id'                   => $ocurrence->id,
            'is_active'            => false,
            'type_closure'         => $payload['type_closure'],
            'solution_description' => $payload['solution_description'] ?? null,
        ]);
    }

    /**
     * @dataProvider payloadsInactivateOcurrence
     */
    public function test_unauthenticated_user_can_not_inactivate_ocurrence(array $payload): void
    {
        $user      = User::factory()->create();
        $ocurrence = Ocurrence::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->postJson("/api/ocurrences/inactivate/{$ocurrence->id}", $payload);

        $response->assertStatus(401);

        $this->assertDatabaseMissing('ocurrences', [
            'id'                   => $ocurrence->id,
            'is_active'            => false,
            'type_closure'         => TypeOcurrenceClosure::RESOLVED->value,
            'solution_description' => 'Tudo Certo',
        ]);
    }

    public function test_auth_user_can_not_inactivate_ocurrence_that_is_already_inactivated(): void
    {
        $user      = User::factory()->create();
        $ocurrence = Ocurrence::factory()->create([
            'user_id'   => $user->id,
            'is_active' => false,
        ]);

        $payload = [
            'type_closure'         => TypeOcurrenceClosure::RESOLVED->value,
            'solution_description' => 'Tudo Certo',
        ];
        $response = $this->actingAs($user)->postJson("/api/ocurrences/inactivate/{$ocurrence->id}", $payload);

        $response->assertStatus(422);

        $this->assertDatabaseMissing('ocurrences', [
            'id'                   => $ocurrence->id,
            'is_active'            => false,
            'type_closure'         => TypeOcurrenceClosure::RESOLVED->value,
            'solution_description' => 'Tudo Certo',
        ]);
    }

    public static function payloadsInvalids(): array
    {

        return [
            [
                [
                    'type_closure' => TypeOcurrenceClosure::RESOLVED->value,
                ],
            ],

            [
                [
                    'type_closure' => 'teste',
                ],
            ],
            [
                [
                    'type_closure' => TypeOcurrenceClosure::OTHER->value,
                ],

            ],
            [
                [
                    'type_closure'         => TypeOcurrenceClosure::RESOLVED->value,
                    'solution_description' => 'Tudo Certo',
                    'resolution_date'      => now()->addDay()->toDateString(),
                ],
            ],
        ];
    }

    /**
     * @dataProvider payloadsInvalids
     */
    public function test_user_cannot_register_payload_error(array $payload)
    {

        $user      = User::factory()->create();
        $ocurrence = Ocurrence::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->postJson("/api/ocurrences/inactivate/{$ocurrence->id}", $payload);

        $response->assertStatus(422);

        $this->assertDatabaseMissing('ocurrences', [
            'id'                   => $ocurrence->id,
            'is_active'            => false,
            'type_closure'         => $payload['type_closure'],
            'solution_description' => $payload['solution_description'] ?? null,
        ]);

    }

    public function test_auth_user_can_list_only_his_ocurrences(): void
    {
        $user      = User::factory()->create();
        $otherUser = User::factory()->create();

        Ocurrence::factory()->count(15)->create([
            'user_id' => $user->id,
        ]);

        Ocurrence::factory()->count(3)->create([
            'user_id' => $otherUser->id,
        ]);

        $responsePage1 = $this->actingAs($user)->getJson('/api/ocurrences/my-ocurrences?page=1');
        $responsePage1->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'type_id', 'location', 'type_id', 'user_id', 'description', 'address_name', 'city', 'state', 'country', 'is_active', 'created_at', 'updated_at'],
                ],
                'first_page_url',
                'from',
                'last_page',
                'last_page_url',
                'links' => [
                    '*' => ['url', 'label', 'page', 'active'],
                ],
                'next_page_url',
                'path',
                'per_page',
                'prev_page_url',
                'to',
                'total',
            ]);

        $responseData1 = $responsePage1->json('data');

        foreach ($responseData1 as $ocurrence) {
            $this->assertEquals($user->id, $ocurrence['user_id']);
        }

        $this->assertCount(10, $responseData1);

        $responsePage2 = $this->actingAs($user)->getJson('/api/ocurrences/my-ocurrences?page=2');
        $responsePage2->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'type_id', 'location', 'type_id', 'user_id', 'description', 'address_name', 'city', 'state', 'country', 'is_active', 'created_at', 'updated_at'],
                ],
                'first_page_url',
                'from',
                'last_page',
                'last_page_url',
                'links' => [
                    '*' => ['url', 'label', 'page', 'active'],
                ],
                'next_page_url',
                'path',
                'per_page',
                'prev_page_url',
                'to',
                'total',
            ]);

        $responseData2 = $responsePage2->json('data');

        foreach ($responseData2 as $ocurrence) {
            $this->assertEquals($user->id, $ocurrence['user_id']);
        }

        $this->assertCount(5, $responseData2);
    }

    public function test_unauth_user_cannot_access_ocurrences()
    {

        $response = $this->getJson('/api/ocurrences/my-ocurrences?page=1');

        $response->assertStatus(401);

    }
}
