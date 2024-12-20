<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Internet\mst_Eoc_address;
class PurchasedAreaSettingController extends Controller
{
    public function index(Request $request)
    {
        //mst_Eoc_addressテーブルのデータを取得
        $area = $_GET['area'];
        $mst_Eoc_address = mst_Eoc_address::where('name_en', '=', $area)
            ->orWhere(function ($query) use ($area) {
                $query->where('todou_en', '=', $area)
                      ->whereNull('name_en');})
            ->first();
        if ($mst_Eoc_address === null) {
            return response()->json(["status" => "error"], 200, [], JSON_UNESCAPED_UNICODE);
        }
        
        $mst_Eoc_address = json_decode($mst_Eoc_address, true);
        $mst_Eoc_address_array = array(
            "status" => "success",
            'name' => $mst_Eoc_address['name'], //市区町村日本語
            'name_en' => $mst_Eoc_address['name_en'], //市区町村英語
            'todou' => $mst_Eoc_address['todou'], //都道府県日本語
            'todou_en' => $mst_Eoc_address['todou_en'], //都道府県英語
            'top_image' => $mst_Eoc_address['top_image'], //top_image
            'top_image_sp' => $mst_Eoc_address['top_image_sp'], //top_image_sp
            'top_image_txt' => $mst_Eoc_address['top_image_txt'], //top_image_txt
        );
       
        $array = array(
            'purchased_area' => $mst_Eoc_address_array,
        );
        return response()->json($array, 200, [], JSON_UNESCAPED_UNICODE);
    }
}
