<?php

namespace Tests\Feature\Controller;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RegisterControllerFTest extends TestCase
{
    use RefreshDatabase;

    public function test_registers_a_user_and_returns_token()
    {
        $payload = [
            'name'                  => 'Anakin Skywalker',
            'email'                 => 'anakins@gmail.com',
            'password'              => 'StrongP@ssword123',
            'date_birth'            => '1999-01-01',
            'password_confirmation' => 'StrongP@ssword123',
        ];

        $response = $this->postJson('api/register', $payload);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'user' => [
                'id',
                'name',
                'email',
                'date_birth',
                'created_at',
                'updated_at',
            ],
            'token',
        ]);

        $this->assertDatabaseHas('users', [
            'email'      => $payload['email'],
            'name'       => $payload['name'],
            'date_birth' => Carbon::parse($payload['date_birth'])->format('Y-m-d H:i:s'),
        ]);

        $this->assertTrue(Hash::check('StrongP@ssword123', User::first()['password']));
    }

    public function test_user_cannot_register_payload_error()
    {
        $payload = [
            'email'                 => 'anakins@example.com',
            'password'              => 'StrongP@ssword123',
            'date_birth'            => '1999-01-01',
            'password_confirmation' => 'StrongP@ssword123',
        ];

        $response = $this->postJson('api/register', $payload);

        $response->assertStatus(422);

        $this->assertDatabaseMissing('users', [
            'email'      => $payload['email'],
            'date_birth' => Carbon::parse($payload['date_birth'])->format('Y-m-d H:i:s'),
        ]);

    }
}
