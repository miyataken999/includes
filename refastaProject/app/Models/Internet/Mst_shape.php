<?php

namespace App\Models\Internet;

use \Illuminate\Database\Eloquent\Model;

class Mst_shape extends Model
{
    protected $connection = "refasta_internet_editor";
    protected $table = "mst_shape";
    protected $primaryKey = "id";

    public $timestamps = false;
}