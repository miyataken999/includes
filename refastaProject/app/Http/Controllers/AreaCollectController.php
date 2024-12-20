<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Internet\mst_Eoc_address;
class AreaCollectController extends Controller
{
    public function index(Request $request){
        $area = $request->input('area');
        if ($area) {
            $todou_en = mst_Eoc_address::where('name_en', $area)
                ->orWhere(function ($query) use ($area) {
                    $query->where('todou_en', $area)
                        ->whereNull('name_en');
                })
                ->value('todou_en');
    
            if ($todou_en) {
                $cityNames = mst_Eoc_address::where('todou_en', $todou_en)
                    ->whereNotNull('name_en')
                    ->pluck('name','name_en');
            } else {
                $cityNames = []; 
            }
        } else {
            $cityNames = [];
        }
        if ($cityNames === null) {
            return response()->json(["status" => "error"], 200, [], JSON_UNESCAPED_UNICODE);
        }
        $residentOptions = DB::table("mst_Eoc_address")
            ->distinct()
            ->pluck('todou_en as area');

        $data = [
            'cityNames' => $cityNames,
            'residentOptions' => $residentOptions,
            'area' => $area,
        ];
        return view("purchased_cityname.index", $data);
    }
}