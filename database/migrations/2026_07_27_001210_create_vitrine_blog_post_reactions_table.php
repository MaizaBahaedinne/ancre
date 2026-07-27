<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('vitrine_blog_post_reactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vitrine_blog_post_id')->constrained('vitrine_blog_posts')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('reaction', 20)->default('like');
            $table->timestamps();

            $table->unique(['vitrine_blog_post_id', 'user_id']);
            $table->index(['vitrine_blog_post_id', 'reaction']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vitrine_blog_post_reactions');
    }
};
