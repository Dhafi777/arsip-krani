<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('action'); // Contoh: 'Tambah', 'Edit', 'Hapus'
            $table->text('description'); // Contoh: 'Krani Daxxa menambahkan surat...'
            $table->timestamps();
        });
    }
    public function down() {
        Schema::dropIfExists('activity_logs');
    }
};