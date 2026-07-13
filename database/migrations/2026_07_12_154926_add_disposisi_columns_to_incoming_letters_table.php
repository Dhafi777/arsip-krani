<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('incoming_letters', function (Blueprint $table) {
            // Tambahkan 2 kolom baru ini setelah kolom perihal
            $table->string('status_disposisi')->default('By Element')->after('perihal');
            $table->string('file_hasil_disposisi')->nullable()->after('status_disposisi');
        });
    }

    public function down()
    {
        Schema::table('incoming_letters', function (Blueprint $table) {
            $table->dropColumn(['status_disposisi', 'file_hasil_disposisi']);
        });
    }
};