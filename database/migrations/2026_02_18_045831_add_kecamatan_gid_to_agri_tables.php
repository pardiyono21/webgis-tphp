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
        $tables = ['agrisawah_ar_50k', 'agriladang_ar_50k', 'agrikebun_ar_50k'];
        
        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->integer('kecamatan_gid')->nullable()->index();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = ['agrisawah_ar_50k', 'agriladang_ar_50k', 'agrikebun_ar_50k'];
        
        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->dropColumn('kecamatan_gid');
            });
        }
    }
};
