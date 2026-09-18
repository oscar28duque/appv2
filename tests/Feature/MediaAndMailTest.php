<?php

namespace Tests\Feature;

use App\Mail\ContactConfirmation;
use App\Mail\ContactMessage;
use App\Models\Media;
use App\Models\News;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MediaAndMailTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create([
            'email' => 'admin@empresa.com',
        ]);
    }

    protected function createFakeImage(string $name = 'foto.jpg'): UploadedFile
    {
        if (str_ends_with($name, '.png')) {
            $content = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==');
        } else {
            // 1x1 valid JPEG
            $content = base64_decode('/9j/4AAQSkZJRgABAQEASABIAAD/2wBDAP//////////////////////////////////////////////////////////////////////////////////////wgALCAABAAEBAREA/8QAFBABAAAAAAAAAAAAAAAAAAAAAP/aAAgBAQABPxA=');
        }

        return UploadedFile::fake()->createWithContent($name, $content);
    }

    /**
     * Reto A: Subida segura de imágenes y almacenamiento de metadatos
     */
    public function test_user_can_upload_image_safely(): void
    {
        Storage::fake('public');

        $file = $this->createFakeImage('banner.jpg');

        $response = $this->actingAs($this->user)->post(route('admin.media.store'), [
            'name' => 'Banner Principal',
            'file' => $file,
        ]);

        $response->assertRedirect(route('admin.media.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('media', [
            'name' => 'Banner Principal',
            'mime_type' => 'image/jpeg',
        ]);

        $media = Media::where('name', 'Banner Principal')->first();
        $this->assertNotNull($media);
        Storage::disk('public')->assertExists($media->path);
    }

    /**
     * Reto A: Validación en servidor rechaza archivos no permitidos (ej. php/txt)
     */
    public function test_upload_rejects_invalid_file_types(): void
    {
        Storage::fake('public');

        $invalidFile = UploadedFile::fake()->create('script.php', 100, 'text/x-php');

        $response = $this->actingAs($this->user)->post(route('admin.media.store'), [
            'name' => 'Archivo Malicioso',
            'file' => $invalidFile,
        ]);

        $response->assertSessionHasErrors(['file']);
        $this->assertDatabaseMissing('media', ['name' => 'Archivo Malicioso']);
    }

    /**
     * Reto A: Reemplazo de imagen elimina el archivo físico previo del almacenamiento
     */
    public function test_replacing_image_deletes_old_file(): void
    {
        Storage::fake('public');

        $oldFile = $this->createFakeImage('viejo.jpg');
        $oldPath = $oldFile->store('media', 'public');

        $media = Media::create([
            'name' => 'Foto Antigua',
            'path' => $oldPath,
            'mime_type' => 'image/jpeg',
            'size' => 1024,
            'active' => true,
        ]);

        Storage::disk('public')->assertExists($oldPath);

        $newFile = $this->createFakeImage('nuevo.png');

        $response = $this->actingAs($this->user)->put(route('admin.media.update', $media), [
            'name' => 'Foto Renovada',
            'file' => $newFile,
        ]);

        $response->assertRedirect(route('admin.media.index'));

        // El archivo anterior debe haberse eliminado físicamente
        Storage::disk('public')->assertMissing($oldPath);

        $media->refresh();
        $this->assertEquals('Foto Renovada', $media->name);
        Storage::disk('public')->assertExists($media->path);
    }

    /**
     * Reto A: Eliminación de imagen borra el registro y el archivo en Storage
     */
    public function test_deleting_media_removes_file_and_record(): void
    {
        Storage::fake('public');

        $file = $this->createFakeImage('eliminar.jpg');
        $path = $file->store('media', 'public');

        $media = Media::create([
            'name' => 'Por Eliminar',
            'path' => $path,
            'mime_type' => 'image/jpeg',
            'size' => 1024,
        ]);

        Storage::disk('public')->assertExists($path);

        $response = $this->actingAs($this->user)->delete(route('admin.media.destroy', $media));

        $response->assertRedirect(route('admin.media.index'));
        $this->assertDatabaseMissing('media', ['id' => $media->id]);
        Storage::disk('public')->assertMissing($path);
    }

    /**
     * Reto B: Crear noticia y asociarla con imagen de la biblioteca
     */
    public function test_create_news_with_media_relation(): void
    {
        Storage::fake('public');

        $file = $this->createFakeImage('noticia.jpg');
        $path = $file->store('media', 'public');

        $media = Media::create([
            'name' => 'Imagen Noticia',
            'path' => $path,
            'mime_type' => 'image/jpeg',
            'size' => 2048,
        ]);

        $response = $this->actingAs($this->user)->post(route('admin.news.store'), [
            'title' => 'Lanzamiento Nueva Plataforma',
            'slug' => 'lanzamiento-nueva-plataforma',
            'excerpt' => 'Breve extracto de la noticia.',
            'content' => 'Contenido detallado con información oficial del lanzamiento.',
            'media_id' => $media->id,
            'published' => '1',
        ]);

        $response->assertRedirect(route('admin.news.index'));

        $this->assertDatabaseHas('news', [
            'title' => 'Lanzamiento Nueva Plataforma',
            'slug' => 'lanzamiento-nueva-plataforma',
            'media_id' => $media->id,
            'published' => 1,
        ]);

        // Verificar visualización en la página de inicio pública (Sección 5.11)
        $homeResponse = $this->get('/');
        $homeResponse->assertStatus(200);
        $homeResponse->assertSee('Lanzamiento Nueva Plataforma');
        $homeResponse->assertSee(Storage::url($path));

        // Verificar vista individual
        $showResponse = $this->get(route('news.show', 'lanzamiento-nueva-plataforma'));
        $showResponse->assertStatus(200);
        $showResponse->assertSee('Lanzamiento Nueva Plataforma');
        $showResponse->assertSee(Storage::url($path));
    }

    /**
     * Reto C: Envío de correos de contacto al administrador y confirmación al usuario
     */
    public function test_contact_form_sends_admin_and_confirmation_emails(): void
    {
        Mail::fake();

        $contactData = [
            'name' => 'Carlos Mendoza',
            'email' => 'carlos@ejemplo.com',
            'message' => 'Solicito información sobre las inscripciones y requisitos técnicos.',
        ];

        $response = $this->post(route('contact.send'), $contactData);

        $response->assertSessionHas('success');

        // 1. Verificar correo al administrador
        Mail::assertSent(ContactMessage::class, function ($mail) use ($contactData) {
            return $mail->name === $contactData['name'] &&
                   $mail->email === $contactData['email'] &&
                   $mail->userMessage === $contactData['message'];
        });

        // 2. Verificar confirmación al usuario
        Mail::assertSent(ContactConfirmation::class, function ($mail) use ($contactData) {
            return $mail->hasTo($contactData['email']) &&
                   $mail->name === $contactData['name'];
        });
    }

    /**
     * Reto C: Validación del formulario de contacto
     */
    public function test_contact_form_validates_required_fields(): void
    {
        $response = $this->post(route('contact.send'), [
            'name' => '',
            'email' => 'no-es-un-email',
            'message' => '',
        ]);

        $response->assertSessionHasErrors(['name', 'email', 'message']);
    }
}
