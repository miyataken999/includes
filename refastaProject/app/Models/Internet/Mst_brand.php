<?php

namespace App\Models\Internet;

use \Illuminate\Database\Eloquent\Model;

class Mst_brand extends Model
{
    protected $connection = "refasta_internet_editor";
    protected $table = "mst_brand";
    protected $primaryKey = "id";

    public $timestamps = false;
}