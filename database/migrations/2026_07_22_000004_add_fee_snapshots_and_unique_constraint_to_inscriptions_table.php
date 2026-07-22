<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inscriptions', function (Blueprint $table) {
            if (! Schema::hasColumn('inscriptions', 'annual_registration_fee')) {
                $table->decimal('annual_registration_fee', 10, 2)->default(0)->after('package_id');
            }

            if (! Schema::hasColumn('inscriptions', 'package_monthly_total')) {
                $table->decimal('package_monthly_total', 10, 2)->default(0)->after('annual_registration_fee');
            }

            if (! Schema::hasColumn('inscriptions', 'total_amount')) {
                $table->decimal('total_amount', 10, 2)->default(0)->after('package_monthly_total');
            }
        });

        $uniqueIndexExists = false;

        if (DB::getDriverName() === 'mysql') {
            $uniqueIndexExists = collect(DB::select("SHOW INDEX FROM inscriptions WHERE Key_name = 'inscriptions_enfant_id_annee_scolaire_unique'"))->isNotEmpty();
        }

        if (! $uniqueIndexExists) {
            Schema::table('inscriptions', function (Blueprint $table) {
                $table->unique(['enfant_id', 'annee_scolaire'], 'inscriptions_enfant_id_annee_scolaire_unique');
            });
        }
    }

    public function down(): void
    {
        Schema::table('inscriptions', function (Blueprint $table) {
            $table->dropUnique('inscriptions_enfant_id_annee_scolaire_unique');

            $table->dropColumn([
                'annual_registration_fee',
                'package_monthly_total',
                'total_amount',
            ]);
        });
    }
};