<?php

namespace App\Models\Internet;

use \Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $connection = "refasta_internet_editor";
    protected $primaryKey = "id";

    public $timestamps = false;
}