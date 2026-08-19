<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('vitrine_settings')) {
            return;
        }

        Schema::table('vitrine_settings', function (Blueprint $table): void {
            if (!Schema::hasColumn('vitrine_settings', 'countdown_enabled')) {
                $table->boolean('countdown_enabled')->default(false)->after('youtube_url');
            }
            if (!Schema::hasColumn('vitrine_settings', 'countdown_target_at')) {
                $table->dateTime('countdown_target_at')->nullable()->after('countdown_enabled');
            }
            if (!Schema::hasColumn('vitrine_settings', 'countdown_timezone')) {
                $table->string('countdown_timezone', 64)->nullable()->after('countdown_target_at');
            }
            if (!Schema::hasColumn('vitrine_settings', 'countdown_title')) {
                $table->string('countdown_title')->nullable()->after('countdown_timezone');
            }
            if (!Schema::hasColumn('vitrine_settings', 'countdown_subtitle')) {
                $table->string('countdown_subtitle')->nullable()->after('countdown_title');
            }
            if (!Schema::hasColumn('vitrine_settings', 'countdown_expired_label')) {
                $table->string('countdown_expired_label')->nullable()->after('countdown_subtitle');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('vitrine_settings')) {
            return;
        }

        Schema::table('vitrine_settings', function (Blueprint $table): void {
            $columns = [
                'countdown_enabled',
                'countdown_target_at',
                'countdown_timezone',
                'countdown_title',
                'countdown_subtitle',
                'countdown_expired_label',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('vitrine_settings', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
