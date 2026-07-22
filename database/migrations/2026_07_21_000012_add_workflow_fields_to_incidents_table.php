<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('incidents', function (Blueprint $table) {
            $table->string('workflow_status', 30)->default('ouvert')->after('type_incident');
            $table->timestamp('opened_at')->nullable()->after('workflow_status');
            $table->foreignId('responsable_personnel_id')->nullable()->after('opened_at')->constrained('personnels')->nullOnDelete();
            $table->timestamp('taken_at')->nullable()->after('responsable_personnel_id');
            $table->timestamp('resolved_at')->nullable()->after('taken_at');
            $table->timestamp('closed_at')->nullable()->after('resolved_at');

            $table->index(['workflow_status', 'opened_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('incidents', function (Blueprint $table) {
            $table->dropConstrainedForeignId('responsable_personnel_id');
            $table->dropColumn([
                'workflow_status',
                'opened_at',
                'taken_at',
                'resolved_at',
                'closed_at',
            ]);
        });
    }
};