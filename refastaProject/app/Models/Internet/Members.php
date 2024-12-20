<?php

namespace App\Models\Internet;

use Illuminate\Support\Facades\DB;
use \Illuminate\Database\Eloquent\Model;

class Members extends Model
{
//    protected $connection = "refasta_internet_editor";
    protected $table = "internet.members";
    protected $primaryKey = "id";
}