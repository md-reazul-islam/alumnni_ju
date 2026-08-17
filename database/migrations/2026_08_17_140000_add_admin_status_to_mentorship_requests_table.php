<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mentorship_requests', function (Blueprint $table) {
            $table->enum('admin_status', ['pending', 'approved', 'rejected'])->default('pending')->after('status');
            $table->foreignId('admin_reviewed_by')->nullable()->after('admin_status')->constrained('users')->nullOnDelete();
            $table->timestamp('admin_reviewed_at')->nullable()->after('admin_reviewed_by');
        });

        // Grandfather in mentorships that were already activated under the old single-approval
        // flow, so they don't retroactively appear as "awaiting admin approval".
        DB::table('mentorship_requests')
            ->whereIn('id', DB::table('mentorships')->select('mentorship_request_id'))
            ->update(['admin_status' => 'approved', 'admin_reviewed_at' => now()]);
    }

    public function down(): void
    {
        Schema::table('mentorship_requests', function (Blueprint $table) {
            $table->dropConstrainedForeignId('admin_reviewed_by');
            $table->dropColumn(['admin_status', 'admin_reviewed_at']);
        });
    }
};
