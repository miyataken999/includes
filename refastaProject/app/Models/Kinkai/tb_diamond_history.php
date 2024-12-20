<?php

namespace App\Models\Kinkai;

use Illuminate\Database\Eloquent\Model;

class tb_diamond_history extends Model
{
    protected $connection = "refasta_kinkai";
    protected $table = "tb_diamond_history";
    protected $primaryKey = "id";
}
