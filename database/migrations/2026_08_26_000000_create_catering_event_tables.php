<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('catering_program_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('icon')->default('utensils');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('catering_food_items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('base_price', 10, 2);
            $table->string('unit_label')->default('per plate');
            $table->string('image')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('catering_food_item_category', function (Blueprint $table) {
            $table->id();
            $table->foreignId('catering_food_item_id');
            $table->foreignId('catering_program_category_id');
            $table->foreign('catering_food_item_id', 'cfic_food_item_fk')
                ->references('id')->on('catering_food_items')->cascadeOnDelete();
            $table->foreign('catering_program_category_id', 'cfic_program_category_fk')
                ->references('id')->on('catering_program_categories')->cascadeOnDelete();
            $table->unique(['catering_food_item_id', 'catering_program_category_id'], 'catering_item_category_unique');
        });

        Schema::create('catering_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id');
            $table->foreignId('catering_program_category_id');
            $table->date('event_date');
            $table->unsignedInteger('guest_count')->nullable();
            $table->text('delivery_address')->nullable();
            $table->string('contact_phone')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['submitted', 'priced', 'accepted', 'declined', 'delivered', 'cancelled'])->default('submitted');
            $table->decimal('subtotal', 12, 2)->nullable();
            $table->decimal('tax_percentage_snapshot', 5, 2)->nullable();
            $table->decimal('tax_amount', 12, 2)->nullable();
            $table->decimal('vat_percentage_snapshot', 5, 2)->nullable();
            $table->decimal('vat_amount', 12, 2)->nullable();
            $table->decimal('service_fee_percentage_snapshot', 5, 2)->nullable();
            $table->decimal('service_fee_amount', 12, 2)->nullable();
            $table->decimal('total_amount', 12, 2)->nullable();
            $table->foreignId('priced_by')->nullable();
            $table->timestamp('priced_at')->nullable();
            $table->timestamp('customer_responded_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->enum('payment_status', ['unpaid', 'paid', 'refunded'])->default('unpaid');
            $table->foreignId('delivered_by')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->foreignId('cancelled_by')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->text('admin_notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('customer_id', 'co_customer_fk')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('catering_program_category_id', 'co_category_fk')->references('id')->on('catering_program_categories');
            $table->foreign('priced_by', 'co_priced_by_fk')->references('id')->on('users')->nullOnDelete();
            $table->foreign('delivered_by', 'co_delivered_by_fk')->references('id')->on('users')->nullOnDelete();
            $table->foreign('cancelled_by', 'co_cancelled_by_fk')->references('id')->on('users')->nullOnDelete();

            $table->index(['status']);
            $table->index(['customer_id']);
        });

        Schema::create('catering_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('catering_order_id');
            $table->foreignId('catering_food_item_id')->nullable();
            $table->string('custom_item_name')->nullable();
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('unit_price', 10, 2)->nullable();
            $table->decimal('line_total', 12, 2)->nullable();
            $table->timestamps();

            $table->foreign('catering_order_id', 'coi_order_fk')->references('id')->on('catering_orders')->cascadeOnDelete();
            $table->foreign('catering_food_item_id', 'coi_food_item_fk')->references('id')->on('catering_food_items')->nullOnDelete();
        });

        Schema::create('catering_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('catering_order_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 12, 2);
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

        Schema::create('catering_order_feedback', function (Blueprint $table) {
            $table->id();
            $table->foreignId('catering_order_id')->unique()->constrained()->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedTinyInteger('rating');
            $table->text('comment')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('catering_order_feedback');
        Schema::dropIfExists('catering_payments');
        Schema::dropIfExists('catering_order_items');
        Schema::dropIfExists('catering_orders');
        Schema::dropIfExists('catering_food_item_category');
        Schema::dropIfExists('catering_food_items');
        Schema::dropIfExists('catering_program_categories');
    }
};
