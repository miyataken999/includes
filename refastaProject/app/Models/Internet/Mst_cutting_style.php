<?php

namespace App\Models\Internet;

use \Illuminate\Database\Eloquent\Model;

class Mst_cutting_style extends Model
{
    protected $connection = "refasta_internet_editor";
    protected $table = "mst_cutting_style";
    protected $primaryKey = "id";

    public $timestamps = false;
}