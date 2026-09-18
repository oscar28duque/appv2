<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Item;
use App\Models\Payment;
use App\Models\Reservation;
use App\Models\ReservationItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventsCmsTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Client $client;
    protected Item $item;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'email' => 'admin@eventos.com',
        ]);

        $this->client = Client::create([
            'name' => 'Productora Musical SAS',
            'document' => '900123456',
            'email' => 'info@productora.com',
            'phone' => '3001234567',
        ]);

        $this->item = Item::create([
            'code' => 'AUD-TEST',
            'name' => 'Bafle de Audio Test',
            'category' => 'Audio',
            'total_quantity' => 5,
            'daily_rate' => 50000.00,
            'weekend_rate' => 80000.00,
            'status' => 'disponible',
        ]);
    }

    /**
     * Requerimiento 3.2 & 3.4: Disponibilidad inicial de equipos
     */
    public function test_item_initial_availability(): void
    {
        $today = now()->toDateString();
        $this->assertEquals(5, $this->item->getAvailableQuantityForRange($today, $today));
    }

    /**
     * Requerimiento 3.3: Creación de reserva con tarifa diferenciada por día
     */
    public function test_create_reservation_with_daily_pricing(): void
    {
        $startDate = now()->addDays(1)->toDateString();
        $endDate = now()->addDays(2)->toDateString(); // 2 días

        $response = $this->actingAs($this->user)->post(route('admin.reservations.store'), [
            'client_id' => $this->client->id,
            'event_name' => 'Concierto en Auditorio',
            'start_date' => $startDate,
            'end_date' => $endDate,
            'pricing_type' => 'daily',
            'discount' => 10000.00,
            'initial_payment' => 50000.00,
            'payment_method' => 'transferencia',
            'items' => [
                [
                    'item_id' => $this->item->id,
                    'quantity' => 2,
                ],
            ],
        ]);

        $response->assertSessionHas('success');

        // 2 días * 50,000 * 2 unidades = 200,000 subtotal - 10,000 desc = 190,000 total
        $this->assertDatabaseHas('reservations', [
            'client_id' => $this->client->id,
            'subtotal' => 200000.00,
            'total_amount' => 190000.00,
            'paid_amount' => 50000.00,
            'status' => 'confirmada',
        ]);

        // Verificar abono inicial
        $this->assertDatabaseHas('payments', [
            'amount' => 50000.00,
            'payment_method' => 'transferencia',
        ]);

        // Verificar que el stock disponible en esas fechas se redujo a 3 (5 - 2)
        $this->assertEquals(3, $this->item->getAvailableQuantityForRange($startDate, $endDate));
    }

    /**
     * Requerimiento 3.3: Tarifa plana de fin de semana
     */
    public function test_create_reservation_with_weekend_pricing(): void
    {
        $startDate = now()->addDays(5)->toDateString();
        $endDate = now()->addDays(6)->toDateString();

        $response = $this->actingAs($this->user)->post(route('admin.reservations.store'), [
            'client_id' => $this->client->id,
            'event_name' => 'Festival Fin de Semana',
            'start_date' => $startDate,
            'end_date' => $endDate,
            'pricing_type' => 'weekend',
            'items' => [
                [
                    'item_id' => $this->item->id,
                    'quantity' => 3,
                ],
            ],
        ]);

        $response->assertSessionHas('success');

        // Tarifa plana fin de semana: 80,000 * 3 = 240,000
        $this->assertDatabaseHas('reservations', [
            'pricing_type' => 'weekend',
            'subtotal' => 240000.00,
            'total_amount' => 240000.00,
        ]);
    }

    /**
     * Requerimiento 3.4: Detección y bloqueo automático de sobrecupo
     */
    public function test_overbooking_is_prevented(): void
    {
        $startDate = now()->addDays(10)->toDateString();
        $endDate = now()->addDays(11)->toDateString();

        // Intento de reservar 6 unidades cuando solo hay 5
        $response = $this->actingAs($this->user)->post(route('admin.reservations.store'), [
            'client_id' => $this->client->id,
            'event_name' => 'Evento Sobredimensionado',
            'start_date' => $startDate,
            'end_date' => $endDate,
            'pricing_type' => 'daily',
            'items' => [
                [
                    'item_id' => $this->item->id,
                    'quantity' => 6,
                ],
            ],
        ]);

        $response->assertSessionHasErrors(['items']);
        $this->assertDatabaseMissing('reservations', ['event_name' => 'Evento Sobredimensionado']);
    }

    /**
     * Requerimiento 3.7: Registro de abonos parciales y saldo pendiente
     */
    public function test_partial_payment_reduces_pending_balance(): void
    {
        $reservation = Reservation::create([
            'code' => 'RSV-TEST-001',
            'client_id' => $this->client->id,
            'event_name' => 'Boda Test',
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDay()->toDateString(),
            'pricing_type' => 'daily',
            'subtotal' => 500000.00,
            'total_amount' => 500000.00,
            'paid_amount' => 200000.00,
            'status' => 'confirmada',
        ]);

        $this->assertEquals(300000.00, $reservation->pending_balance);

        $response = $this->actingAs($this->user)->post(route('admin.reservations.payment', $reservation), [
            'amount' => 150000.00,
            'payment_method' => 'nequi_daviplata',
            'payment_date' => now()->toDateString(),
            'reference' => 'NQ-999',
        ]);

        $response->assertSessionHas('success');

        $reservation->refresh();
        $this->assertEquals(350000.00, $reservation->paid_amount);
        $this->assertEquals(150000.00, $reservation->pending_balance);
    }

    /**
     * Requerimiento 3.6: Registro de devolución libera disponibilidad
     */
    public function test_return_process_completes_and_frees_equipment(): void
    {
        $today = now()->toDateString();

        $reservation = Reservation::create([
            'code' => 'RSV-TEST-002',
            'client_id' => $this->client->id,
            'event_name' => 'Feria Comercial',
            'start_date' => $today,
            'end_date' => $today,
            'pricing_type' => 'daily',
            'subtotal' => 100000.00,
            'total_amount' => 100000.00,
            'paid_amount' => 100000.00,
            'status' => 'en_curso',
        ]);

        ReservationItem::create([
            'reservation_id' => $reservation->id,
            'item_id' => $this->item->id,
            'quantity' => 4,
            'unit_price' => 50000.00,
            'subtotal' => 100000.00,
        ]);

        // Mientras está en curso, la disponibilidad es 1 (5 - 4)
        $this->assertEquals(1, $this->item->getAvailableQuantityForRange($today, $today));

        // Registrar devolución
        $response = $this->actingAs($this->user)->post(route('admin.reservations.return', $reservation), [
            'return_date_real' => now()->format('Y-m-d H:i:s'),
            'return_status' => 'sin_novedad',
            'return_notes' => 'Equipos probados en recepción, en perfecto estado.',
        ]);

        $response->assertSessionHas('success');

        $reservation->refresh();
        $this->assertEquals('devuelta', $reservation->status);

        // Al estar devuelta, el stock vuelve a estar disponible (5 unidades)
        $this->assertEquals(5, $this->item->getAvailableQuantityForRange($today, $today));
    }

    /**
     * Frontend: Catálogo público responde 200 y muestra equipos
     */
    public function test_public_catalog_and_item_detail(): void
    {
        $homeResponse = $this->get('/');
        $homeResponse->assertStatus(200);
        $homeResponse->assertSee('Bafle de Audio Test');
        $homeResponse->assertSee('AUD-TEST');

        $detailResponse = $this->get(route('items.show', 'AUD-TEST'));
        $detailResponse->assertStatus(200);
        $detailResponse->assertSee('Bafle de Audio Test');
        $detailResponse->assertSee('TARIFA FIN DE SEMANA');
    }
}
