<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthApiSecurityTest extends TestCase
{
    use RefreshDatabase;

    // ==========================================
    // 01 Registro
    // ==========================================

    /**
     * Prueba 1 – Registro válido (201 Created)
     */
    public function test_prueba_1_registro_valido(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Usuario Prueba',
            'email' => 'usuario@test.com',
            'password' => 'Segura12345',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'user' => ['id', 'name', 'email'],
            ]);

        $this->assertArrayNotHasKey('password', $response->json('user'));
        $this->assertDatabaseHas('users', ['email' => 'usuario@test.com']);
    }

    /**
     * Prueba 2 – Email inválido (422 Unprocessable Entity)
     */
    public function test_prueba_2_email_invalido(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Usuario Prueba',
            'email' => 'correo-invalido',
            'password' => 'Segura12345',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    /**
     * Prueba 3 – Campos faltantes (422 Unprocessable Entity)
     */
    public function test_prueba_3_campos_faltantes(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Usuario Prueba',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'password']);
    }

    /**
     * Prueba 4 – Contraseña insuficiente menos de 8 caracteres (422)
     */
    public function test_prueba_4_password_insuficiente(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Usuario',
            'email' => 'usuario2@test.com',
            'password' => '123',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    /**
     * Prueba 5 – Usuario duplicado (422 Unprocessable Entity)
     */
    public function test_prueba_5_usuario_duplicado(): void
    {
        User::factory()->create([
            'email' => 'usuario@test.com',
        ]);

        $response = $this->postJson('/api/register', [
            'name' => 'Usuario Prueba',
            'email' => 'usuario@test.com',
            'password' => 'Segura12345',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    // ==========================================
    // 02 Login
    // ==========================================

    /**
     * Prueba 6 – Login correcto (200 OK y Bearer Token)
     */
    public function test_prueba_6_login_correcto(): void
    {
        User::factory()->create([
            'email' => 'usuario@test.com',
            'password' => Hash::make('Segura12345'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'usuario@test.com',
            'password' => 'Segura12345',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['message', 'token', 'token_type'])
            ->assertJson([
                'token_type' => 'Bearer',
            ]);

        $this->assertNotEmpty($response->json('token'));
    }

    /**
     * Prueba 7 – Login incorrecto / Contraseña errónea (401 Unauthorized)
     */
    public function test_prueba_7_login_incorrecto(): void
    {
        User::factory()->create([
            'email' => 'usuario@test.com',
            'password' => Hash::make('Segura12345'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'usuario@test.com',
            'password' => 'ContraseñaIncorrecta',
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Las credenciales no son correctas.',
            ]);

        $this->assertNull($response->json('token'));
    }

    /**
     * Prueba 8 – Usuario inexistente (401 Unauthorized sin enumeración)
     */
    public function test_prueba_8_usuario_inexistente(): void
    {
        $response = $this->postJson('/api/login', [
            'email' => 'noexiste@test.com',
            'password' => 'Segura12345',
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Las credenciales no son correctas.',
            ]);
    }

    // ==========================================
    // 03 Perfil
    // ==========================================

    /**
     * Prueba 9 – Acceder al perfil sin token (401 Unauthorized)
     */
    public function test_prueba_9_perfil_sin_token(): void
    {
        $response = $this->getJson('/api/profile');

        $response->assertStatus(401);
    }

    /**
     * Prueba 10 – Token inválido / forjado (401 Unauthorized)
     */
    public function test_prueba_10_token_invalido(): void
    {
        $response = $this->withToken('123456789')->getJson('/api/profile');

        $response->assertStatus(401);
    }

    /**
     * Prueba 11 – Token válido (200 OK con datos de perfil)
     */
    public function test_prueba_11_token_valido(): void
    {
        $user = User::factory()->create([
            'name' => 'Juan Carlos',
            'email' => 'juancarlos@test.com',
        ]);

        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withToken($token)->getJson('/api/profile');

        $response->assertStatus(200)
            ->assertJson([
                'id' => $user->id,
                'name' => 'Juan Carlos',
                'email' => 'juancarlos@test.com',
            ]);
    }

    /**
     * Prueba 12 – No expone información sensible (password, remember_token)
     */
    public function test_prueba_12_informacion_sensible(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withToken($token)->getJson('/api/profile');

        $response->assertStatus(200);
        $data = $response->json();

        $this->assertArrayNotHasKey('password', $data);
        $this->assertArrayNotHasKey('password_hash', $data);
        $this->assertArrayNotHasKey('remember_token', $data);
    }

    // ==========================================
    // 04 Logout
    // ==========================================

    /**
     * Prueba 13 – Logout revoca token activo (200 OK)
     */
    public function test_prueba_13_logout(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withToken($token)->postJson('/api/logout');

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Sesión cerrada correctamente',
            ]);
    }

    /**
     * Prueba 13.1 – Perfil post-logout con token revocado (401 Unauthorized)
     */
    public function test_prueba_13_1_perfil_post_logout_token_invalidado(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        // Cierre de sesión
        $this->withToken($token)->postJson('/api/logout');

        // Olvidar guard en memoria para que Sanctum revalide contra BD
        $this->app['auth']->forgetGuards();

        // Intento de reutilizar token
        $response = $this->withToken($token)->getJson('/api/profile');

        $response->assertStatus(401);
    }

    // ==========================================
    // 05 Pruebas de seguridad
    // ==========================================

    /**
     * Prueba 14 – Solicitudes repetitivas / Rate Limiting (429 Too Many Requests)
     */
    public function test_prueba_14_solicitudes_repetitivas_rate_limiting(): void
    {
        $email = 'bruteforce@test.com';

        // Realizar 5 intentos fallidos
        for ($i = 0; $i < 5; $i++) {
            $response = $this->postJson('/api/login', [
                'email' => $email,
                'password' => 'ContraseñaIncorrecta',
            ]);
            $this->assertEquals(401, $response->status());
        }

        // El 6to intento debe activar el Rate Limiting (429)
        $responseThrottle = $this->postJson('/api/login', [
            'email' => $email,
            'password' => 'ContraseñaIncorrecta',
        ]);

        $responseThrottle->assertStatus(429);
    }
}
