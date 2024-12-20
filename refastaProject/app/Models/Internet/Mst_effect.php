<?php

namespace App\Models\Internet;

use \Illuminate\Database\Eloquent\Model;

class Mst_effect extends Model
{
    protected $connection = "refasta_internet_editor";
    protected $table = "mst_effect";
    protected $primaryKey = "id";

    public $timestamps = false;
}