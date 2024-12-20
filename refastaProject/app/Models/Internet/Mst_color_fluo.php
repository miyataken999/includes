<?php

namespace App\Models\Internet;

use \Illuminate\Database\Eloquent\Model;

class Mst_color_fluo extends Model
{
    protected $connection = "refasta_internet_editor";
    protected $table = "mst_color_fluo";
    protected $primaryKey = "Id";

    public $timestamps = false;
}