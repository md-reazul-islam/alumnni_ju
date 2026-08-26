<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('catering_homemade_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('icon')->default('cooking-pot');
            $table->decimal('commission_percentage', 5, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('catering_homemade_listings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->foreignId('catering_homemade_category_id');
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');
            $table->decimal('price', 10, 2);
            $table->string('price_unit')->default('per_item');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->foreignId('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('views_count')->default(0);
            $table->string('tags')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id', 'chl_user_fk')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('catering_homemade_category_id', 'chl_category_fk')->references('id')->on('catering_homemade_categories');
            $table->foreign('approved_by', 'chl_approved_by_fk')->references('id')->on('users')->nullOnDelete();

            $table->index(['status']);
        });

        Schema::create('catering_homemade_listing_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('catering_homemade_listing_id');
            $table->string('path');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->foreign('catering_homemade_listing_id', 'chli_listing_fk')
                ->references('id')->on('catering_homemade_listings')->cascadeOnDelete();
        });

        Schema::create('catering_homemade_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('catering_homemade_listing_id');
            $table->foreignId('buyer_id');
            $table->foreignId('seller_id');
            $table->unsignedInteger('quantity')->default(1);
            $table->enum('status', ['pending', 'ongoing', 'completed', 'cancelled'])->default('pending');
            $table->foreignId('handled_by')->nullable();
            $table->decimal('final_price', 12, 2)->nullable();
            $table->decimal('commission_percentage_snapshot', 5, 2)->nullable();
            $table->decimal('commission_amount', 12, 2)->nullable();
            $table->text('admin_notes')->nullable();
            $table->timestamps();

            $table->foreign('catering_homemade_listing_id', 'cho_listing_fk')
                ->references('id')->on('catering_homemade_listings')->cascadeOnDelete();
            $table->foreign('buyer_id', 'cho_buyer_fk')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('seller_id', 'cho_seller_fk')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('handled_by', 'cho_handled_by_fk')->references('id')->on('users')->nullOnDelete();

            $table->index(['status']);
            $table->index(['seller_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('catering_homemade_orders');
        Schema::dropIfExists('catering_homemade_listing_images');
        Schema::dropIfExists('catering_homemade_listings');
        Schema::dropIfExists('catering_homemade_categories');
    }
};
