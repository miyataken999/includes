<?php
namespace App\Http\Controllers;

use App\Models\WPBatch\WPBatch;


class WpBatchController extends Controller
{
    function batch(){
        ini_set('memory_limit', '2048M');
        set_time_limit(0);
        $WPBatch = new WPBatch();
        return $WPBatch->batch();
    }
}
