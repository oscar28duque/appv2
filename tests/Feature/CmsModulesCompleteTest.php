<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Expense;
use App\Models\Item;
use App\Models\MaintenanceRecord;
use App\Models\Media;
use App\Models\Reservation;
use App\Models\ReservationItem;
use App\Models\StaffMember;
use App\Models\User;
use App\Models\WorkLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CmsModulesCompleteTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'email' => 'admin@eventos.com',
            'name' => 'Administrador CMS',
        ]);
    }

    protected function createFakeImage(string $name = 'test.jpg'): UploadedFile
    {
        $content = base64_decode('/9j/4AAQSkZJRgABAQEASABIAAD/2wBDAP//////////////////////////////////////////////////////////////////////////////////////wgALCAABAAEBAREA/8QAFBABAAAAAAAAAAAAAAAAAAAAAP/aAAgBAQABPxA=');
        return UploadedFile::fake()->createWithContent($name, $content);
    }

    /**
     * Seguridad y Control de Acceso: Los invitados son redirigidos al Login
     */
    public function test_guest_cannot_access_cms_and_is_redirected_to_login(): void
    {
        $response = $this->get(route('dashboard'));
        $response->assertRedirect(route('login'));

        $responseItems = $this->get(route('admin.items.index'));
        $responseItems->assertRedirect(route('login'));

        $responseClients = $this->get(route('admin.clients.index'));
        $responseClients->assertRedirect(route('login'));

        $responseFinances = $this->get(route('admin.finances.index'));
        $responseFinances->assertRedirect(route('login'));
    }

    /**
     * Requerimiento 3.1: Dashboard General con KPIs en tiempo real
     */
    public function test_authenticated_user_can_access_dashboard_and_see_kpis(): void
    {
        Item::create([
            'code' => 'LGT-001',
            'name' => 'Foco Par LED',
            'category' => 'Iluminación',
            'total_quantity' => 10,
            'daily_rate' => 20000,
            'weekend_rate' => 35000,
            'status' => 'disponible',
        ]);

        $response = $this->actingAs($this->user)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Centro de Control');
        $response->assertSee('DISPONIBILIDAD HOY');
        $response->assertSee('Iluminación');
    }

    /**
     * Requerimiento 3.5: Directorio de Clientes (Creación, Actualización y Eliminación)
     */
    public function test_admin_can_manage_clients(): void
    {
        // 1. Crear Cliente
        $responseStore = $this->actingAs($this->user)->post(route('admin.clients.store'), [
            'name' => 'Corporación Eventos Globales',
            'document' => '901234567-8',
            'email' => 'contacto@globales.com',
            'phone' => '3119876543',
            'company' => 'Globales Inc',
            'address' => 'Avenida Principal # 45-12',
            'notes' => 'Cliente corporativo preferente',
        ]);

        $client = Client::where('document', '901234567-8')->first();
        $this->assertNotNull($client);
        $responseStore->assertRedirect(route('admin.clients.show', $client));
        $this->assertDatabaseHas('clients', ['email' => 'contacto@globales.com']);

        // 2. Actualizar Cliente
        $responseUpdate = $this->actingAs($this->user)->put(route('admin.clients.update', $client), [
            'name' => 'Corporación Eventos Globales SAS',
            'document' => '901234567-8',
            'email' => 'nuevo_email@globales.com',
            'phone' => '3119876543',
            'company' => 'Globales Inc SAS',
            'address' => 'Calle Nueva # 10-20',
        ]);

        $responseUpdate->assertRedirect(route('admin.clients.show', $client));
        $this->assertDatabaseHas('clients', ['email' => 'nuevo_email@globales.com']);

        // 3. Eliminar Cliente sin reservas
        $responseDelete = $this->actingAs($this->user)->delete(route('admin.clients.destroy', $client));
        $responseDelete->assertRedirect(route('admin.clients.index'));
        $this->assertDatabaseMissing('clients', ['id' => $client->id]);
    }

    /**
     * Requerimiento 3.5: Protección de integridad referencial en clientes con reservas
     */
    public function test_admin_cannot_delete_client_with_reservations(): void
    {
        $client = Client::create([
            'name' => 'Cliente Con Reservas',
            'document' => '12345678',
            'email' => 'cliente@reserva.com',
        ]);

        Reservation::create([
            'code' => 'RSV-INT-001',
            'client_id' => $client->id,
            'event_name' => 'Bautizo',
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDay()->toDateString(),
            'pricing_type' => 'daily',
            'subtotal' => 100000,
            'total_amount' => 100000,
            'paid_amount' => 50000,
            'status' => 'confirmada',
        ]);

        $response = $this->actingAs($this->user)->delete(route('admin.clients.destroy', $client));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('clients', ['id' => $client->id]);
    }

    /**
     * Requerimiento 3.2: Gestión de Inventario de Equipos (Creación con Imagen y Edición)
     */
    public function test_admin_can_manage_items(): void
    {
        Storage::fake('public');

        $image = $this->createFakeImage('parled.jpg');

        $responseStore = $this->actingAs($this->user)->post(route('admin.items.store'), [
            'code' => 'MIC-SHU-01',
            'name' => 'Micrófono Inalámbrico Shure',
            'category' => 'Audio',
            'description' => 'Micrófono vocal profesional de alta fidelidad',
            'total_quantity' => 8,
            'daily_rate' => 35000,
            'weekend_rate' => 55000,
            'file' => $image,
            'status' => 'disponible',
        ]);

        $responseStore->assertRedirect(route('admin.items.index'));
        $this->assertDatabaseHas('items', ['code' => 'MIC-SHU-01']);

        $item = Item::where('code', 'MIC-SHU-01')->first();
        $this->assertNotNull($item->media_id);

        // Actualizar artículo
        $responseUpdate = $this->actingAs($this->user)->put(route('admin.items.update', $item), [
            'code' => 'MIC-SHU-01',
            'name' => 'Micrófono Inalámbrico Shure Modificado',
            'category' => 'Audio',
            'total_quantity' => 10,
            'daily_rate' => 40000,
            'weekend_rate' => 60000,
            'status' => 'disponible',
        ]);

        $responseUpdate->assertRedirect(route('admin.items.index'));
        $this->assertDatabaseHas('items', [
            'code' => 'MIC-SHU-01',
            'name' => 'Micrófono Inalámbrico Shure Modificado',
            'total_quantity' => 10,
        ]);
    }

    /**
     * Requerimiento 3.10: Historial técnico de mantenimiento y cambio de estado
     */
    public function test_admin_can_register_item_maintenance(): void
    {
        $item = Item::create([
            'code' => 'EST-TRA-01',
            'name' => 'Estructura Truss 3m',
            'category' => 'Estructuras',
            'total_quantity' => 4,
            'daily_rate' => 45000,
            'weekend_rate' => 70000,
            'status' => 'disponible',
        ]);

        // Registrar mantenimiento preventivo
        $response = $this->actingAs($this->user)->post(route('admin.items.maintenance', $item), [
            'type' => 'mantenimiento_preventivo',
            'cost' => 50000,
            'description' => 'Ajuste de tuercas y pasadores de seguridad',
            'status' => 'en_proceso',
            'maintenance_date' => now()->toDateString(),
        ]);

        $response->assertSessionHas('success');
        $item->refresh();
        $this->assertEquals('en_mantenimiento', $item->status);
        $this->assertDatabaseHas('maintenance_records', [
            'item_id' => $item->id,
            'type' => 'mantenimiento_preventivo',
            'cost' => 50000,
        ]);

        // Registrar baja de inventario
        $responseBaja = $this->actingAs($this->user)->post(route('admin.items.maintenance', $item), [
            'type' => 'baja_inventario',
            'cost' => 0,
            'description' => 'Estructura doblada irreparable durante tormenta',
            'status' => 'completado',
            'maintenance_date' => now()->toDateString(),
        ]);

        $responseBaja->assertSessionHas('success');
        $item->refresh();
        $this->assertEquals('dado_de_baja', $item->status);
    }

    /**
     * Requerimiento 3.5: Carga de evidencia fotográfica en reserva
     */
    public function test_admin_can_upload_reservation_evidence(): void
    {
        Storage::fake('public');

        $client = Client::create([
            'name' => 'Cliente Evidencias',
            'document' => '11223344',
            'email' => 'evidencias@test.com',
        ]);

        $reservation = Reservation::create([
            'code' => 'RSV-EVD-001',
            'client_id' => $client->id,
            'event_name' => 'Graduación Colegios',
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDay()->toDateString(),
            'pricing_type' => 'daily',
            'subtotal' => 200000,
            'total_amount' => 200000,
            'paid_amount' => 200000,
            'status' => 'en_curso',
        ]);

        $evidenceFile = $this->createFakeImage('entrega_equipos.jpg');

        $response = $this->actingAs($this->user)->post(route('admin.reservations.evidence', $reservation), [
            'evidence_file' => $evidenceFile,
        ]);

        $response->assertSessionHas('success');
        $reservation->refresh();
        $this->assertNotNull($reservation->evidence_media_id);

        $media = Media::find($reservation->evidence_media_id);
        Storage::disk('public')->assertExists($media->path);
    }

    /**
     * Requerimientos 3.7 & 3.8: Registro y eliminación de Gastos Operativos
     */
    public function test_admin_can_manage_operational_expenses(): void
    {
        Storage::fake('public');

        $receipt = $this->createFakeImage('recibo_gasolina.jpg');

        $responseStore = $this->actingAs($this->user)->post(route('admin.finances.expense.store'), [
            'category' => 'transporte',
            'description' => 'Combustible camión de carga evento norte',
            'amount' => 120000.00,
            'expense_date' => now()->toDateString(),
            'receipt' => $receipt,
        ]);

        $responseStore->assertSessionHas('success');

        $expense = Expense::where('description', 'Combustible camión de carga evento norte')->first();
        $this->assertNotNull($expense);
        $this->assertEquals(120000.00, (float)$expense->amount);
        $this->assertNotNull($expense->receipt_media_id);

        // Eliminar gasto
        $responseDelete = $this->actingAs($this->user)->delete(route('admin.finances.expense.destroy', $expense));
        $responseDelete->assertSessionHas('success');
        $this->assertDatabaseMissing('expenses', ['id' => $expense->id]);
    }

    /**
     * Requerimiento 3.9: Gestión de Personal Operativo, Bitácora de Turnos y Nómina
     */
    public function test_admin_can_manage_staff_and_worklogs(): void
    {
        // 1. Crear colaborador
        $responseStaff = $this->actingAs($this->user)->post(route('admin.staff.store'), [
            'name' => 'Andrés Restrepo',
            'role' => 'Técnico de Audio e Iluminación',
            'document' => '1020304050',
            'phone' => '3201234567',
            'daily_rate' => 90000.00,
            'payment_frequency' => 'diario',
        ]);

        $responseStaff->assertSessionHas('success');
        $staff = StaffMember::where('document', '1020304050')->first();
        $this->assertNotNull($staff);

        // 2. Registrar turno en bitácora
        $responseWorkLog = $this->actingAs($this->user)->post(route('admin.staff.worklog.store'), [
            'staff_member_id' => $staff->id,
            'work_date' => now()->toDateString(),
            'hours_or_days' => 1.5,
            'notes' => 'Montaje general y prueba de sonido en tarima',
        ]);

        $responseWorkLog->assertSessionHas('success');

        $workLog = WorkLog::where('staff_member_id', $staff->id)->first();
        $this->assertNotNull($workLog);
        $this->assertEquals(135000.00, (float)$workLog->amount_earned); // 90000 * 1.5
        $this->assertFalse((bool)$workLog->paid);

        // 3. Liquidar / pagar turno
        $responsePay = $this->actingAs($this->user)->patch(route('admin.staff.worklog.pay', $workLog));
        $responsePay->assertSessionHas('success');

        $workLog->refresh();
        $this->assertTrue((bool)$workLog->paid);
    }
}
