<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('paiements', function (Blueprint $table) {
            $table->string('reference', 30)->nullable()->after('id');
        });

        $paiements = DB::table('paiements')
            ->select('id', 'annee')
            ->orderBy('id')
            ->get();

        foreach ($paiements as $paiement) {
            $year = (int) ($paiement->annee ?: now()->format('Y'));
            $reference = sprintf('PAY-%s-%06d', $year, $paiement->id);

            DB::table('paiements')
                ->where('id', $paiement->id)
                ->update(['reference' => $reference]);
        }

        Schema::table('paiements', function (Blueprint $table) {
            $table->unique('reference');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('paiements', function (Blueprint $table) {
            $table->dropUnique(['reference']);
            $table->dropColumn('reference');
        });
    }
};
