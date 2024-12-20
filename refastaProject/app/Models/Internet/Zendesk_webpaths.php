<?php

namespace App\Models\Internet;

use Illuminate\Support\Facades\DB;
use \Illuminate\Database\Eloquent\Model;

class Zendesk_webpaths extends Model
{
//    protected $connection = "refasta_internet_editor";
    protected $table = "internet.zendesk_webpaths";
    protected $primaryKey = "id";
}