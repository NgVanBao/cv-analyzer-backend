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
        Schema::create('tu_dien_ky_nang', function (Blueprint $table) {
            $table->increments('MaKyNang');
            $table->string('TenKyNang', 100);
            $table->string('LoaiKyNang', 50);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tu_dien_ky_nang');
    }
};
