<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('carpool_driver_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('license_number');
            $table->date('license_expiry')->nullable();
            $table->text('bio')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected', 'suspended'])->default('pending');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->boolean('is_active')->default(true);
            $table->decimal('total_earned', 12, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('carpool_cars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('carpool_driver_profile_id')->constrained()->cascadeOnDelete();
            $table->string('make');
            $table->string('model');
            $table->unsignedSmallInteger('year')->nullable();
            $table->string('color')->nullable();
            $table->string('plate_number');
            $table->unsignedTinyInteger('total_seats');
            $table->string('photo')->nullable();
            $table->timestamps();
        });

        Schema::create('carpool_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('carpool_driver_profile_id')->constrained()->cascadeOnDelete();
            $table->foreignId('carpool_car_id')->constrained()->restrictOnDelete();
            $table->string('origin');
            $table->string('destination');
            $table->date('departure_date');
            $table->time('departure_time');
            $table->decimal('price_per_seat', 10, 2);
            $table->unsignedTinyInteger('seats_offered');
            $table->unsignedTinyInteger('seats_booked')->default(0);
            $table->enum('status', ['pending', 'approved', 'rejected', 'cancelled', 'completed'])->default('pending');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'departure_date']);
        });

        Schema::create('carpool_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('carpool_schedule_id')->constrained()->cascadeOnDelete();
            $table->foreignId('passenger_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedTinyInteger('seats')->default(1);
            $table->decimal('seat_price_snapshot', 10, 2);
            $table->decimal('total_fare', 10, 2);
            $table->enum('status', ['requested', 'accepted', 'declined', 'confirmed', 'completed', 'cancelled', 'expired'])->default('requested');
            $table->timestamp('driver_responded_at')->nullable();
            $table->timestamp('payment_deadline_at')->nullable();
            $table->enum('payment_status', ['unpaid', 'paid', 'refunded'])->default('unpaid');
            $table->decimal('commission_percentage_snapshot', 5, 2)->nullable();
            $table->decimal('commission_amount', 10, 2)->nullable();
            $table->decimal('driver_payout_amount', 10, 2)->nullable();
            $table->enum('payout_status', ['pending', 'paid'])->default('pending');
            $table->timestamp('paid_out_at')->nullable();
            $table->foreignId('paid_out_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('cancelled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('cancellation_reason')->nullable();
            $table->timestamps();

            $table->unique(['carpool_schedule_id', 'passenger_id']);
            $table->index(['status', 'payment_deadline_at']);
        });

        Schema::create('carpool_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('carpool_booking_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('usd');
            $table->enum('status', ['pending', 'succeeded', 'failed', 'refunded'])->default('pending');
            $table->string('gateway')->default('stripe');
            $table->string('stripe_checkout_session_id')->nullable()->unique();
            $table->string('gateway_reference')->nullable();
            $table->text('failure_reason')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('refunded_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('carpool_payments');
        Schema::dropIfExists('carpool_bookings');
        Schema::dropIfExists('carpool_schedules');
        Schema::dropIfExists('carpool_cars');
        Schema::dropIfExists('carpool_driver_profiles');
    }
};
