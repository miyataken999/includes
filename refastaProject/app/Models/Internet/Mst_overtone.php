<?php

namespace App\Models\Internet;

use \Illuminate\Database\Eloquent\Model;

class Mst_overtone extends Model
{
    protected $connection = "refasta_internet_editor";
    protected $table = "mst_overtone";
    protected $primaryKey = "Id";

    public $timestamps = false;
}