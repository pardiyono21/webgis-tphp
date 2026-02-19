<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kecamatan extends Model
{
    // Beritahu Laravel nama tabel aslinya
    protected $table = 'kecamatan';

    // Beritahu primary key-nya (cek di DBeaver, biasanya 'gid' atau 'id')
    protected $primaryKey = 'gid';

    // Matikan timestamps karena tabel SHP tidak punya kolom created_at/updated_at
    public $timestamps = false;

    // Matikan proteksi mass assignment
    protected $guarded = [];
}
