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
        Schema::create('AI_Log', function (Blueprint $table) {
            $table->increments('MaLog');
            $table->unsignedInteger('MaCV');
            $table->date('ThoiGian');
            $table->string('TrangThai', 50);
            $table->text('NoiDungLog');
            $table->timestamps();

            $table->foreign('MaCV')->references('MaCV')->on('HoSoCV')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('AI_Log');
    }
};
