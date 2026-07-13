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
        Schema::create('dispositions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('incoming_letter_id')->constrained('incoming_letters')->onDelete('cascade');
            
            // Input awal Krani saat membuat disposisi (Ceklis Head)
            $table->enum('tujuan_head', ['RHE', 'OH', 'BSH']); 
            
            // 3 Status sesuai business logic Anda
            $table->enum('status_disposisi', ['by_elemen', 'ke_head', 'diteruskan'])->default('by_elemen');
            
            // Atribut ini diisi nanti saat status berubah menjadi 'diteruskan'
            $table->string('file_hasil_disposisi')->nullable(); // Upload scan lembar fisik yang sudah diisi Head
            $table->string('diteruskan_ke_bagian')->nullable(); // Diinput Krani berdasarkan centang/catatan dari Head (misal: SKH, TAN, dll)

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dispositions');
    }
};
