<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('donation_campaigns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->enum('category', [
                'scholarship', 'research', 'student_support', 'infrastructure',
                'emergency_fund', 'alumni_association', 'general_fund',
            ])->default('general_fund');
            $table->decimal('goal_amount', 14, 2)->nullable();
            $table->decimal('raised_amount', 14, 2)->default(0);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->enum('status', ['active', 'completed', 'cancelled'])->default('active');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('donations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('donor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('donation_campaign_id')->nullable()->constrained()->nullOnDelete();
            $table->string('donor_name')->nullable();
            $table->string('donor_email')->nullable();
            $table->decimal('amount', 12, 2);
            $table->string('currency', 3)->default('USD');
            $table->enum('category', [
                'scholarship', 'research', 'student_support', 'infrastructure',
                'emergency_fund', 'alumni_association', 'general_fund',
            ])->default('general_fund');
            $table->boolean('is_anonymous')->default(false);
            $table->enum('payment_method', ['card', 'bank_transfer', 'paypal', 'other'])->default('card');
            $table->enum('payment_status', ['pending', 'completed', 'failed', 'refunded'])->default('pending');
            $table->string('transaction_reference')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['payment_status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('donations');
        Schema::dropIfExists('donation_campaigns');
    }
};
