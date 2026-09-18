<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Expense;
use App\Models\Item;
use App\Models\Media;
use App\Models\Payment;
use App\Models\Reservation;
use App\Models\ReservationItem;
use App\Models\StaffMember;
use App\Models\WorkLog;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class EventsCmsSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Crear Imágenes en Media para el catálogo
        $sampleMedia = [
            [
                'name' => 'Sistema Line Array RCF',
                'path' => 'media/line_array_audio.jpg',
                'mime_type' => 'image/jpeg',
                'size' => 1024 * 450,
            ],
            [
                'name' => 'Cabezas Móviles Beam 230W',
                'path' => 'media/luces_beam.jpg',
                'mime_type' => 'image/jpeg',
                'size' => 1024 * 380,
            ],
            [
                'name' => 'Pantalla LED P3.9 Outdoor',
                'path' => 'media/pantalla_led.jpg',
                'mime_type' => 'image/jpeg',
                'size' => 1024 * 512,
            ],
            [
                'name' => 'Tarima Modular 6x4 con Techo Truss',
                'path' => 'media/tarima_truss.jpg',
                'mime_type' => 'image/jpeg',
                'size' => 1024 * 620,
            ],
            [
                'name' => 'Consola Digital Behringer X32',
                'path' => 'media/consola_x32.jpg',
                'mime_type' => 'image/jpeg',
                'size' => 1024 * 340,
            ],
        ];

        // Escribir archivos dummy en storage/app/public/media si no existen
        Storage::disk('public')->makeDirectory('media');
        $jpg1x1 = base64_decode('/9j/4AAQSkZJRgABAQEASABIAAD/2wBDAP//////////////////////////////////////////////////////////////////////////////////////wgALCAABAAEBAREA/8QAFBABAAAAAAAAAAAAAAAAAAAAAP/aAAgBAQABPxA=');

        $mediaRecords = [];
        foreach ($sampleMedia as $m) {
            Storage::disk('public')->put($m['path'], $jpg1x1);
            $mediaRecords[] = Media::firstOrCreate(
                ['path' => $m['path']],
                [
                    'name' => $m['name'],
                    'mime_type' => $m['mime_type'],
                    'size' => $m['size'],
                    'active' => true,
                ]
            );
        }

        // 2. Crear Equipos de Inventario
        $itemsData = [
            [
                'code' => 'AUD-001',
                'name' => "Parlante Activo 15' JBL EON 715",
                'category' => 'Audio',
                'description' => 'Potencia 1300W pico, DSP avanzado con Bluetooth y mezclador de 3 canales integrado.',
                'total_quantity' => 8,
                'daily_rate' => 95000.00,
                'weekend_rate' => 150000.00,
                'media_id' => $mediaRecords[0]->id ?? null,
                'status' => 'disponible',
            ],
            [
                'code' => 'AUD-002',
                'name' => 'Consola Digital Behringer X32 Compact',
                'category' => 'Audio',
                'description' => '32 canales, 16 preamplificadores Midas programables, interfaz de audio USB 32x32.',
                'total_quantity' => 3,
                'daily_rate' => 220000.00,
                'weekend_rate' => 350000.00,
                'media_id' => $mediaRecords[4]->id ?? null,
                'status' => 'disponible',
            ],
            [
                'code' => 'ILU-001',
                'name' => 'Cabeza Móvil Beam 7R 230W DMX',
                'category' => 'Iluminación',
                'description' => 'Rueda de 14 colores + blanco, prisma rotatorio de 8 caras y efecto estrobo dinámico.',
                'total_quantity' => 12,
                'daily_rate' => 70000.00,
                'weekend_rate' => 110000.00,
                'media_id' => $mediaRecords[1]->id ?? null,
                'status' => 'disponible',
            ],
            [
                'code' => 'ILU-002',
                'name' => 'Reflector Par LED 54x3W RGBW',
                'category' => 'Iluminación',
                'description' => 'Iluminación arquitectónica y perimetral de escenarios con mezcla suave de colores.',
                'total_quantity' => 24,
                'daily_rate' => 25000.00,
                'weekend_rate' => 40000.00,
                'media_id' => null,
                'status' => 'disponible',
            ],
            [
                'code' => 'VID-001',
                'name' => 'Módulo Pantalla LED P3.9 Outdoor (50x50cm)',
                'category' => 'Video',
                'description' => 'Gabinetes de aluminio ultraliviano, alta tasa de refresco 3840Hz y protección IP65.',
                'total_quantity' => 30,
                'daily_rate' => 80000.00,
                'weekend_rate' => 130000.00,
                'media_id' => $mediaRecords[2]->id ?? null,
                'status' => 'disponible',
            ],
            [
                'code' => 'EST-001',
                'name' => 'Estructura Truss Cuadrada 30x30 (Tramo 2m)',
                'category' => 'Estructuras',
                'description' => 'Aluminio estructural 6082-T6 de alta resistencia para colgar iluminación y audio.',
                'total_quantity' => 16,
                'daily_rate' => 35000.00,
                'weekend_rate' => 55000.00,
                'media_id' => $mediaRecords[3]->id ?? null,
                'status' => 'disponible',
            ],
        ];

        $createdItems = [];
        foreach ($itemsData as $iData) {
            $createdItems[] = Item::firstOrCreate(['code' => $iData['code']], $iData);
        }

        // 3. Crear Clientes
        $client1 = Client::firstOrCreate(
            ['document' => '900.854.123-4'],
            [
                'name' => 'Eventos & Producciones del Norte SAS',
                'email' => 'produccion@eventosdelnorte.com',
                'phone' => '312 456 7890',
                'company' => 'Eventos del Norte',
                'address' => 'Carrera 7 # 120-45, Bogotá',
                'notes' => 'Cliente corporativo habitual. Pago a 15 días.',
            ]
        );

        $client2 = Client::firstOrCreate(
            ['document' => '1020304050'],
            [
                'name' => 'Mariana Restrepo Gómez',
                'email' => 'mariana.restrepo@gmail.com',
                'phone' => '300 987 6543',
                'company' => null,
                'address' => 'Calle 116 # 15-20, Bogotá',
                'notes' => 'Boda familiar campestre en Chia.',
            ]
        );

        // 4. Crear Reservas (Una activa para hoy, una próxima y una con devolución pendiente)
        $today = now();

        // Reserva 1: En Curso hoy (cliente 1)
        $rsv1 = Reservation::firstOrCreate(
            ['code' => 'RSV-2026-0001'],
            [
                'client_id' => $client1->id,
                'event_name' => 'Congreso Nacional de Tecnología 2026',
                'event_location' => 'Centro de Convenciones Ágora',
                'start_date' => $today->copy()->subDay()->toDateString(),
                'end_date' => $today->copy()->addDay()->toDateString(),
                'pricing_type' => 'daily',
                'subtotal' => 1140000.00,
                'discount' => 140000.00,
                'total_amount' => 1000000.00,
                'paid_amount' => 600000.00,
                'status' => 'en_curso',
            ]
        );

        ReservationItem::firstOrCreate(
            ['reservation_id' => $rsv1->id, 'item_id' => $createdItems[0]->id],
            ['quantity' => 2, 'unit_price' => 285000.00, 'subtotal' => 570000.00]
        );
        ReservationItem::firstOrCreate(
            ['reservation_id' => $rsv1->id, 'item_id' => $createdItems[2]->id],
            ['quantity' => 4, 'unit_price' => 210000.00, 'subtotal' => 840000.00]
        );

        Payment::firstOrCreate(
            ['reservation_id' => $rsv1->id, 'reference' => 'TR-45678'],
            [
                'amount' => 600000.00,
                'payment_method' => 'transferencia',
                'payment_date' => $today->copy()->subDay()->toDateString(),
                'notes' => 'Anticipo del 60% por transferencia Bancolombia.',
            ]
        );

        // Reserva 2: Fin de semana confirmada (cliente 2)
        $rsv2 = Reservation::firstOrCreate(
            ['code' => 'RSV-2026-0002'],
            [
                'client_id' => $client2->id,
                'event_name' => 'Boda Mariana & Felipe',
                'event_location' => 'Hacienda San José, Chía',
                'start_date' => $today->copy()->addDays(2)->toDateString(),
                'end_date' => $today->copy()->addDays(4)->toDateString(),
                'pricing_type' => 'weekend',
                'subtotal' => 1250000.00,
                'discount' => 50000.00,
                'total_amount' => 1200000.00,
                'paid_amount' => 1200000.00,
                'status' => 'confirmada',
            ]
        );

        ReservationItem::firstOrCreate(
            ['reservation_id' => $rsv2->id, 'item_id' => $createdItems[0]->id],
            ['quantity' => 4, 'unit_price' => 150000.00, 'subtotal' => 600000.00]
        );
        ReservationItem::firstOrCreate(
            ['reservation_id' => $rsv2->id, 'item_id' => $createdItems[1]->id],
            ['quantity' => 1, 'unit_price' => 350000.00, 'subtotal' => 350000.00]
        );

        Payment::firstOrCreate(
            ['reservation_id' => $rsv2->id, 'reference' => 'NQ-88231'],
            [
                'amount' => 1200000.00,
                'payment_method' => 'nequi_daviplata',
                'payment_date' => $today->toDateString(),
                'notes' => 'Pago 100% anticipado por Nequi.',
            ]
        );

        // Reserva 3: Devolución Atrasada (Alerta del Requerimiento 3.1)
        $rsv3 = Reservation::firstOrCreate(
            ['code' => 'RSV-2026-0003'],
            [
                'client_id' => $client1->id,
                'event_name' => 'Lanzamiento de Marca Automotriz',
                'event_location' => 'Showroom Calle 100',
                'start_date' => $today->copy()->subDays(5)->toDateString(),
                'end_date' => $today->copy()->subDays(2)->toDateString(), // Fecha vencida!
                'pricing_type' => 'daily',
                'subtotal' => 850000.00,
                'discount' => 0.00,
                'total_amount' => 850000.00,
                'paid_amount' => 400000.00,
                'status' => 'en_curso', // No se ha devuelto, por lo tanto alerta de devolución atrasada
            ]
        );

        // 5. Personal Operativo
        $staff1 = StaffMember::firstOrCreate(
            ['name' => 'Alejandro Morales'],
            [
                'role' => 'Técnico de Audio / Sonidista',
                'document' => '1014234567',
                'phone' => '315 222 3344',
                'daily_rate' => 140000.00,
                'payment_frequency' => 'quincenal',
                'active' => true,
            ]
        );

        $staff2 = StaffMember::firstOrCreate(
            ['name' => 'Camilo Torres'],
            [
                'role' => 'Operador de Iluminación',
                'document' => '1020456789',
                'phone' => '311 888 9900',
                'daily_rate' => 130000.00,
                'payment_frequency' => 'quincenal',
                'active' => true,
            ]
        );

        WorkLog::firstOrCreate(
            ['staff_member_id' => $staff1->id, 'work_date' => $today->toDateString()],
            [
                'reservation_id' => $rsv1->id,
                'hours_or_days' => 1,
                'amount_earned' => 140000.00,
                'paid' => false,
                'notes' => 'Operación en vivo en el Congreso de Tecnología.',
            ]
        );

        // 6. Gastos Operativos del Mes
        Expense::firstOrCreate(
            ['description' => 'Flete de camión ida y regreso Ágora'],
            [
                'category' => 'transporte',
                'amount' => 180000.00,
                'expense_date' => $today->copy()->subDay()->toDateString(),
            ]
        );

        Expense::firstOrCreate(
            ['description' => 'Compra cinta gaffer y conectores Speakon'],
            [
                'category' => 'insumos',
                'amount' => 65000.00,
                'expense_date' => $today->toDateString(),
            ]
        );
    }
}
