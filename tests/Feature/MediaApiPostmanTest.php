<?php

namespace Tests\Feature;

use App\Models\Media;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MediaApiPostmanTest extends TestCase
{
    use RefreshDatabase;

    protected function createFakeImage(string $name = 'banner.jpg'): UploadedFile
    {
        $content = base64_decode('/9j/4AAQSkZJRgABAQEASABIAAD/2wBDAP//////////////////////////////////////////////////////////////////////////////////////wgALCAABAAEBAREA/8QAFBABAAAAAAAAAAAAAAAAAAAAAP/aAAgBAQABPxA=');
        return UploadedFile::fake()->createWithContent($name, $content);
    }

    public function test_api_login_and_media_crud_flow(): void
    {
        Storage::fake('public');

        $user = User::factory()->create([
            'email' => 'admin@eventos.com',
            'password' => bcrypt('admin123456'),
        ]);

        // 1. Login API
        $loginRes = $this->postJson('/api/login', [
            'email' => 'admin@eventos.com',
            'password' => 'admin123456',
        ]);
        $loginRes->assertStatus(200);
        $token = $loginRes->json('token');
        $this->assertNotEmpty($token);

        // 2. Subir imagen
        $uploadRes = $this->withToken($token)->postJson('/api/media', [
            'name' => 'Carpa Gigante',
            'file' => $this->createFakeImage('carpa.jpg'),
        ]);
        $uploadRes->assertStatus(201)
            ->assertJson([
                'status' => 'success',
                'data' => [
                    'name' => 'Carpa Gigante',
                ],
            ]);

        $mediaId = $uploadRes->json('data.id');

        // 3. Listar imágenes
        $listRes = $this->withToken($token)->getJson('/api/media');
        $listRes->assertStatus(200);

        // 4. Validar rechazo de archivo no permitido
        $failRes = $this->withToken($token)->postJson('/api/media', [
            'name' => 'Archivo Malicioso',
            'file' => UploadedFile::fake()->create('script.php', 50, 'text/x-php'),
        ]);
        $failRes->assertStatus(422)
            ->assertJsonValidationErrors(['file']);

        // 5. Eliminar imagen
        $deleteRes = $this->withToken($token)->deleteJson("/api/media/{$mediaId}");
        $deleteRes->assertStatus(200);
        $this->assertDatabaseMissing('media', ['id' => $mediaId]);
    }
}
