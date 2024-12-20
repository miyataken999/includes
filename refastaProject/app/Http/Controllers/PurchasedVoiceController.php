<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class PurchasedVoiceController extends Controller
{
    public function index(Request $request)
    {
        $inputs = $request->all();
        $inputs = array_filter($inputs, function ($v) {
                return $v != null;
        });
        $area = $_GET['area'];
        $query = DB::table('voice_sub')
            ->leftJoin("Eoc_guestquestion", "voice_sub.id", "=", "Eoc_guestquestion.voice_sub_id")
            ->join('mst_Eoc_address', 'mst_Eoc_address.todou', '=', 'Eoc_guestquestion.customer-address')
            ->selectRaw
                ("voice_sub.id, voice_sub.subject, voice_sub.date,voice_sub.zip_name,
                    Eoc_guestquestion.voice_sub_id,Eoc_guestquestion.`service-story`")
            ->where("voice_sub.voice_id", 2)
            ->where('name_en', '=', $area)
            ->orWhere(function ($query) use ($area) {
                $query->where('todou_en', '=', $area)
                      ->whereNull('name_en');});
        if ($query === null) {
            return response()->json(["status" => "error"], 200, [], JSON_UNESCAPED_UNICODE);
        }
        $voice_sub = $query->orderBy("voice_sub.id", "desc")
            ->paginate(6);
        $residentOptions = DB::table("mst_Eoc_address")
            ->distinct()
            ->pluck('todou_en as area');
        if ($request->has('area')) {
            $query->where('mst_Eoc_address.todou_en', $request->input('area'));
        }
        $array = [
            "voice" => $voice_sub,
            "residentOptions" => $residentOptions,
        ];

        return view('purchased_voice.index', $array);
    }
}