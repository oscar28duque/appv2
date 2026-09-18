<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ContactPostmanTest extends TestCase
{
    use RefreshDatabase;

    public function test_postman_json_submission_success(): void
    {
        Mail::fake();

        $response = $this->postJson('/contacto', [
            'name' => 'Carlos Perez',
            'email' => 'carlos@ejemplo.com',
            'message' => 'Deseo cotizar carpas para este sabado.',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'data' => [
                    'admin_recipient' => 'oscarduquegar@gmail.com',
                    'client_recipient' => 'carlos@ejemplo.com',
                ],
            ]);
    }

    public function test_postman_json_validation_errors(): void
    {
        $response = $this->postJson('/contacto', [
            'name' => '',
            'email' => 'correo-invalido',
            'message' => '',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'message']);
    }
}
