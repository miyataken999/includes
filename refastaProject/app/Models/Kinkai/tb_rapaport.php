<?php

namespace App\Models\Kinkai;

use Illuminate\Database\Eloquent\Model;

class tb_rapaport extends Model
{
    protected $connection = "refasta_kinkai";
    protected $table = "tb_rapaport";
    protected $primaryKey = "id";
}
