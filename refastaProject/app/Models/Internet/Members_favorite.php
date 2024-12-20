<?php

namespace App\Models\Internet;

use Illuminate\Support\Facades\DB;
use \Illuminate\Database\Eloquent\Model;

class Members_favorite extends Model
{
//    protected $connection = "refasta_internet_editor";
    protected $table = "members_favorite";
    protected $primaryKey = "id";
    const UPDATED_AT = 'update_at';
    protected $fillable = ['category', 'tokumei_id', 'item_id'];
}