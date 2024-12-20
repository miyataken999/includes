<?php

namespace App\Models\Internet;

use Illuminate\Support\Facades\DB;
use \Illuminate\Database\Eloquent\Model;

class Brand_price extends Model
{
//    protected $connection = "refasta_internet_editor";
    protected $table = "brand_price";
    protected $primaryKey = "id";
}