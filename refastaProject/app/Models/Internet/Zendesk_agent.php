<?php

namespace App\Models\Internet;

use Illuminate\Support\Facades\DB;
use \Illuminate\Database\Eloquent\Model;

class Zendesk_agent extends Model
{
//    protected $connection = "refasta_internet_editor";
    protected $table = "internet.zendesk_agent";
    protected $primaryKey = "id";
}