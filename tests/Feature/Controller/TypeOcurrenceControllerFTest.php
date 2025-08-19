<?php

namespace Tests\Feature;

use App\Models\TypeOcurrence;
use Database\Seeders\TypeOcurrenceSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TypeOcurrenceControllerFTest extends TestCase
{
    use RefreshDatabase;




    public function test_returns_all_types_ocurrences()
    {
        // Roda apenas o seeder que cria os 5 registros
        $this->seed(TypeOcurrenceSeeder::class);

        // Chama a API
        $response = $this->getJson('/api/types-ocurrence');

        // Faz asserções
        $response->assertStatus(200)
            ->assertJsonStructure([
                'types_ocurrences' => [
                    '*' => [
                        'id',
                        'name',
                        'created_at',
                        'updated_at',
                    ]
                ]
            ])
            ->assertJsonCount(5, 'types_ocurrences');
    }


}