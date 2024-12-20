<?php

namespace App\Models\Internet;

use \Illuminate\Database\Eloquent\Model;

class Mst_hahakai extends Model
{
    protected $connection = "refasta_internet_editor";
    protected $table = "mst_hahakai";
    protected $primaryKey = "id";

    public $timestamps = false;
}