<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations for Events CMS according to Functional Requirements document.
     */
    public function up(): void
    {
        // 1. Catálogo de Artículos / Equipos para Alquiler
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // Ej: AUD-001, ILU-010
            $table->string('name');
            $table->string('category')->index(); // Audio, Iluminación, Video, Estructuras, Mobiliario, Efectos
            $table->text('description')->nullable();
            $table->integer('total_quantity')->default(1);
            $table->decimal('daily_rate', 12, 2)->default(0);
            $table->decimal('weekend_rate', 12, 2)->default(0);
            $table->foreignId('media_id')->nullable()->constrained('media')->nullOnDelete();
            $table->string('status')->default('disponible')->index(); // disponible, en_mantenimiento, dado_de_baja
            $table->timestamps();
        });

        // 2. Clientes
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('document')->nullable()->index(); // CC o NIT
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('company')->nullable();
            $table->string('address')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 3. Reservas / Alquileres
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // Ej: RSV-2026-001
            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
            $table->string('event_name')->nullable();
            $table->string('event_location')->nullable();
            $table->date('start_date')->index();
            $table->date('end_date')->index();
            $table->string('pricing_type')->default('daily'); // daily, weekend
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('discount', 12, 2)->default(0);
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->decimal('paid_amount', 12, 2)->default(0);
            $table->string('status')->default('pendiente')->index(); // pendiente, confirmada, en_curso, devuelta, cancelada
            $table->foreignId('evidence_media_id')->nullable()->constrained('media')->nullOnDelete();
            $table->dateTime('return_date_real')->nullable();
            $table->string('return_status')->nullable(); // sin_novedad, con_danos, faltantes
            $table->text('return_notes')->nullable();
            $table->timestamps();
        });

        // 4. Detalle de Equipos en Reserva
        Schema::create('reservation_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reservation_id')->constrained('reservations')->cascadeOnDelete();
            $table->foreignId('item_id')->constrained('items')->cascadeOnDelete();
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price', 12, 2)->default(0);
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->timestamps();
        });

        // 5. Pagos y Abonos (Cuentas por Cobrar)
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reservation_id')->constrained('reservations')->cascadeOnDelete();
            $table->decimal('amount', 12, 2);
            $table->string('payment_method')->default('transferencia'); // transferencia, efectivo, nequi_daviplata, tarjeta
            $table->string('reference')->nullable();
            $table->date('payment_date');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 6. Gastos Operativos
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->string('category')->index(); // transporte, mantenimiento, combustible, insumos, servicios, otros
            $table->string('description');
            $table->decimal('amount', 12, 2);
            $table->date('expense_date');
            $table->foreignId('receipt_media_id')->nullable()->constrained('media')->nullOnDelete();
            $table->timestamps();
        });

        // 7. Registro de Mantenimiento y Bajas
        Schema::create('maintenance_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained('items')->cascadeOnDelete();
            $table->string('type'); // mantenimiento_preventivo, mantenimiento_correctivo, baja_inventario
            $table->decimal('cost', 12, 2)->default(0);
            $table->text('description');
            $table->string('status')->default('programado'); // programado, en_proceso, completado
            $table->date('maintenance_date');
            $table->timestamps();
        });

        // 8. Personal Operativo (Nómina)
        Schema::create('staff_members', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('role'); // Técnico Audio, Iluminador, Logística, Conductor, Coordinador
            $table->string('document')->nullable();
            $table->string('phone')->nullable();
            $table->decimal('daily_rate', 12, 2)->default(0);
            $table->string('payment_frequency')->default('quincenal'); // diario, semanal, quincenal, mensual
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        // 9. Bitácora Diaria de Trabajo
        Schema::create('work_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_member_id')->constrained('staff_members')->cascadeOnDelete();
            $table->foreignId('reservation_id')->nullable()->constrained('reservations')->nullOnDelete();
            $table->date('work_date');
            $table->decimal('hours_or_days', 8, 2)->default(1);
            $table->decimal('amount_earned', 12, 2)->default(0);
            $table->boolean('paid')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_logs');
        Schema::dropIfExists('staff_members');
        Schema::dropIfExists('maintenance_records');
        Schema::dropIfExists('expenses');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('reservation_items');
        Schema::dropIfExists('reservations');
        Schema::dropIfExists('clients');
        Schema::dropIfExists('items');
    }
};
