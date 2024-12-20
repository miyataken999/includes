<?php

namespace App\Models\Internet;

use Illuminate\Support\Facades\DB;
use \Illuminate\Database\Eloquent\Model;

class Zendesk_chats extends Model
{
//    protected $connection = "refasta_internet_editor";
    protected $table = "internet.zendesk_chats";
    protected $primaryKey = "id";

  protected $casts = [
    'open_flag' => 'boolean',
  ];

}
