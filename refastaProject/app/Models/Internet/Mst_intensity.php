<?php

namespace App\Models\Internet;

use \Illuminate\Database\Eloquent\Model;

class Mst_intensity extends Model
{
    protected $connection = "refasta_internet_editor";
    protected $table = "mst_Intensity";
    protected $primaryKey = "ID0";

    public $timestamps = false;
}