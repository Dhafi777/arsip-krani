<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::table('incoming_letters', function (Blueprint $table) {
            // Ini akan otomatis membuat kolom 'deleted_at'
            $table->softDeletes(); 
        });
    }
    public function down() {
        Schema::table('incoming_letters', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};