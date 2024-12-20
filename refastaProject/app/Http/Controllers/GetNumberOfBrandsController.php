<?php
namespace App\Http\Controllers;

use DB;
class GetNumberOfBrandsController extends Controller
{
    public function report()
    {
        $brands = DB::table("mst_brand")->select("id")->get();
        // $count = floor(count($brands) / 1000) * 1000;
        $count = count($brands);
        $count = $count / 1000;
        $count = floor($count);
        $count = $count * 1000;
        $count = number_format($count);
        return $count;
    }
}
