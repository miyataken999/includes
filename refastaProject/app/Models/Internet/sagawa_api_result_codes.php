<?php

namespace App\Models\Internet;

use Illuminate\Database\Eloquent\Model;

class sagawa_api_result_codes extends Model
{
    protected $connection = "refasta_internet_editor";
	protected $table = "internet.sagawa_api_result_codes";
    protected $primaryKey = "id";
}
