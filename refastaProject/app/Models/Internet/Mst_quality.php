<?php

namespace App\Models\Internet;

use \Illuminate\Database\Eloquent\Model;

class Mst_quality extends Model
{
    protected $connection = "refasta_internet_editor";
    protected $table = "mst_quality";
    protected $primaryKey = "id";

    public $timestamps = false;
}