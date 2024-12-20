<?php

namespace App\Models\Internet;

use \Illuminate\Database\Eloquent\Model;

class Mst_chain_type extends Model
{
    protected $connection = "refasta_internet_editor";
    protected $table = "mst_chain_type";

    public $timestamps = false;
}