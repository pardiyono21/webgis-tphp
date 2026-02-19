<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AgriKebun extends Model
{
    // Tabel sesuai DDL user
    protected $table = 'agrikebun_ar_50k';

    // Primary key
    protected $primaryKey = 'gid';

    // Tidak ada timestamps di DDL
    public $timestamps = false;
}
