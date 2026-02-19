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
        $tables = ['kecamatan', 'agrisawah_ar_50k', 'agriladang_ar_50k', 'agrikebun_ar_50k'];
        
        foreach ($tables as $table) {
            // Fix SRID mismatch (Set to 4326) and ensure dimension 4
            DB::statement("UPDATE $table SET geom = ST_SetSRID(ST_Force4D(geom), 4326) WHERE ST_SRID(geom) != 4326 OR ST_SRID(geom) IS NULL");
            
            // Fix invalid geometries, forcing back to 4D to match column constraints
            DB::statement("UPDATE $table SET geom = ST_Force4D(ST_MakeValid(geom)) WHERE NOT ST_IsValid(geom)");
        }

        // Populate kecamatan_gid using a simpler join
        $agriTables = ['agrisawah_ar_50k', 'agriladang_ar_50k', 'agrikebun_ar_50k'];
        foreach ($agriTables as $table) {
            DB::statement("
                UPDATE $table a
                SET kecamatan_gid = k.gid
                FROM kecamatan k
                WHERE ST_Intersects(k.geom, a.geom)
            ");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No easy way to 'undo' geometry fixes, and kecamatan_gid is dropped in the other migration.
    }
};
