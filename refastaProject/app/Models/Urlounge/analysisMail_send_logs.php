<?php

namespace App\Models\Urlounge;

use Illuminate\Database\Eloquent\Model;

class analysisMail_send_logs extends Model
{
    protected $connection = "urlounge_internet";
		protected $table = "analysisMail_send_logs";
    protected $primaryKey = "send_log_id";
}
