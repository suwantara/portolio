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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('category')->default('tutorial');
            $table->text('excerpt')->nullable();
            $table->string('cover_image', 2048)->nullable();
            $table->longText('content');
            $table->string('status')->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->unsignedSmallInteger('reading_minutes')->default(1);
            $table->timestamps();

            $table->index(['status', 'published_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
