<?php

namespace App\Models\Urlounge;

use Illuminate\Database\Eloquent\Model;

class analysisMail_mailgun_webhook_logs extends Model
{
    protected $connection = "urlounge_internet";
		protected $table = "analysisMail_mailgun_webhook_logs";
    protected $primaryKey = "id";
}
