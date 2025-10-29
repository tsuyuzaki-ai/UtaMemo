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
        Schema::create('repertoires', function (Blueprint $table) {
            $table->id();
            $table->string('track_id')->nullable(); // SpotifyトラックID
            $table->string('title'); // 曲名
            $table->string('artist'); // アーティスト名
            $table->string('album_image')->nullable(); // アルバム画像URL
            $table->boolean('is_favorite')->default(false); // お気に入り
            $table->tinyInteger('skill_level')->default(0); // 上達度 (0-3)
            $table->integer('key')->default(0); // キー調整 (-7〜+7)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('repertoires');
    }
};


