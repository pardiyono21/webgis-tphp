<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AgriLadang extends Model
{
    // Tabel sesuai DDL user
    protected $table = 'agriladang_ar_50k';

    // Primary key
    protected $primaryKey = 'gid';

    // Tidak ada timestamps di DDL
    public $timestamps = false;
}
