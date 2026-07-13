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
        Schema::create('incoming_letters', function (Blueprint $table) {
            $table->id();
            $table->string('no_agenda')->unique();
            $table->string('no_surat');
            $table->date('tgl_surat');
            $table->date('tgl_masuk');
            $table->enum('jenis_surat', ['internal', 'eksternal']);
            $table->string('pengirim');
            $table->text('perihal');
            $table->string('file_surat')->nullable(); // Path softfile PDF
            $table->enum('status_surat', ['baru', 'didisposisikan', 'selesai'])->default('baru');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incoming_letters');
    }
};
