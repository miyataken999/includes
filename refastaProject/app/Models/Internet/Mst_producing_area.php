<?php

namespace App\Models\Internet;

use \Illuminate\Database\Eloquent\Model;

class Mst_producing_area extends Model
{
    protected $connection = "refasta_internet_editor";
    protected $table = "mst_producing_area";
    protected $primaryKey = "id";

    public $timestamps = false;
}