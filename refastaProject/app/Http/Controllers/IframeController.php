<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use DB;

class IframeController extends Controller
{
    

    /**
     * @param string url : 動画のURL
     */
    public function get($url)
    {
        $array = array();
        $array["url"] = $url;
        return view("iframe.index", $array);
    }
}