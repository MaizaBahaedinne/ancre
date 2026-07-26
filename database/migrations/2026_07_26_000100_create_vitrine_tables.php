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
        Schema::create('vitrine_settings', function (Blueprint $table) {
            $table->id();
            $table->string('site_name')->default('Ancre Des Elites');
            $table->string('tagline')->nullable();
            $table->string('hero_title')->nullable();
            $table->text('hero_subtitle')->nullable();
            $table->string('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('parent_space_url')->nullable();
            $table->string('map_embed_url', 2048)->nullable();
            $table->string('facebook_url', 2048)->nullable();
            $table->string('instagram_url', 2048)->nullable();
            $table->string('tiktok_url', 2048)->nullable();
            $table->string('youtube_url', 2048)->nullable();
            $table->timestamps();
        });

        Schema::create('vitrine_pages', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title');
            $table->string('hero_title')->nullable();
            $table->text('hero_subtitle')->nullable();
            $table->longText('content')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });

        Schema::create('vitrine_services', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('icon')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('vitrine_schedules', function (Blueprint $table) {
            $table->id();
            $table->string('day_label');
            $table->string('open_at', 20)->nullable();
            $table->string('close_at', 20)->nullable();
            $table->boolean('is_closed')->default(false);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('vitrine_social_posts', function (Blueprint $table) {
            $table->id();
            $table->string('platform');
            $table->string('post_url', 2048);
            $table->string('thumbnail_url', 2048)->nullable();
            $table->text('caption')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        DB::table('vitrine_pages')->insert([
            [
                'slug' => 'home',
                'title' => 'Accueil',
                'hero_title' => 'Bienvenue a Ancre Des Elites',
                'hero_subtitle' => 'Une garderie moderne, rassurante et stimulante pour l\'eveil de chaque enfant.',
                'content' => 'Nous accueillons vos enfants dans un cadre securise et bienveillant, avec des activites educatives, creatives et ludiques adaptees a chaque age.',
                'sort_order' => 1,
                'is_published' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'slug' => 'about',
                'title' => 'A propos',
                'hero_title' => 'Notre mission educative',
                'hero_subtitle' => 'Accompagner chaque enfant vers l\'autonomie, la curiosite et la confiance.',
                'content' => 'Ancre Des Elites place l\'enfant au centre: ecoute, respect du rythme et partenariat actif avec les familles.',
                'sort_order' => 2,
                'is_published' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'slug' => 'services',
                'title' => 'Services',
                'hero_title' => 'Des services complets pour votre serenite',
                'hero_subtitle' => 'Garde, eveil, suivi quotidien et communication continue avec les parents.',
                'content' => 'Nos services sont pensés pour combiner securite, developpement et confort.',
                'sort_order' => 3,
                'is_published' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'slug' => 'activities',
                'title' => 'Activites',
                'hero_title' => 'La vie de la garderie en images',
                'hero_subtitle' => 'Retrouvez nos publications Facebook, Instagram et TikTok.',
                'content' => 'Ateliers, fetes, creations et moments forts partages avec les enfants.',
                'sort_order' => 4,
                'is_published' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'slug' => 'contact',
                'title' => 'Contact',
                'hero_title' => 'Contactez Ancre Des Elites',
                'hero_subtitle' => 'Nous sommes a votre disposition pour toute information ou demande de visite.',
                'content' => 'Vous pouvez nous joindre par telephone, email ou via nos reseaux sociaux.',
                'sort_order' => 5,
                'is_published' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        DB::table('vitrine_settings')->insert([
            'site_name' => 'Ancre Des Elites',
            'tagline' => 'Garderie et eveil',
            'hero_title' => 'Un environnement sur, joyeux et stimulant pour vos enfants',
            'hero_subtitle' => 'Inscriptions ouvertes. Rejoignez une garderie qui allie pedagogie, bienveillance et securite.',
            'address' => 'Tunis, Tunisie',
            'phone' => '+216 00 000 000',
            'email' => 'contact@ancredeselites.tn',
            'parent_space_url' => '/login',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('vitrine_services')->insert([
            [
                'title' => 'Eveil pedagogique',
                'description' => 'Ateliers educatifs, expression orale, logique et decouverte du monde.',
                'icon' => 'fa-solid fa-book-open-reader',
                'sort_order' => 1,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Activites creatives',
                'description' => 'Peinture, musique, motricite et jeux sensoriels adaptes par tranche d\'age.',
                'icon' => 'fa-solid fa-palette',
                'sort_order' => 2,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Suivi quotidien',
                'description' => 'Communication avec les parents sur le rythme, les repas et les progres.',
                'icon' => 'fa-solid fa-heart-pulse',
                'sort_order' => 3,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        DB::table('vitrine_schedules')->insert([
            ['day_label' => 'Lundi', 'open_at' => '08:00', 'close_at' => '18:00', 'is_closed' => false, 'sort_order' => 1, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['day_label' => 'Mardi', 'open_at' => '08:00', 'close_at' => '18:00', 'is_closed' => false, 'sort_order' => 2, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['day_label' => 'Mercredi', 'open_at' => '08:00', 'close_at' => '18:00', 'is_closed' => false, 'sort_order' => 3, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['day_label' => 'Jeudi', 'open_at' => '08:00', 'close_at' => '18:00', 'is_closed' => false, 'sort_order' => 4, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['day_label' => 'Vendredi', 'open_at' => '08:00', 'close_at' => '18:00', 'is_closed' => false, 'sort_order' => 5, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['day_label' => 'Samedi', 'open_at' => '08:00', 'close_at' => '13:00', 'is_closed' => false, 'sort_order' => 6, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['day_label' => 'Dimanche', 'open_at' => null, 'close_at' => null, 'is_closed' => true, 'sort_order' => 7, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vitrine_social_posts');
        Schema::dropIfExists('vitrine_schedules');
        Schema::dropIfExists('vitrine_services');
        Schema::dropIfExists('vitrine_pages');
        Schema::dropIfExists('vitrine_settings');
    }
};
