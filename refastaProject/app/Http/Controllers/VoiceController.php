<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class VoiceController extends Controller
{
    public function index() {
        $voice_sub = DB::table('voice_sub')
        ->leftjoin("Eoc_guestquestion","voice_sub.id","=","Eoc_guestquestion.voice_sub_id")
        ->selectRaw("voice_sub.id, voice_sub.subject, voice_sub.date, Eoc_guestquestion.voice_sub_id, voice_sub.zip_name")
        ->where("voice_sub.voice_id", 2)
        ->orderBy("voice_sub.id", "desc")
        ->paginate(10);

        $array = array();
        $array["voice"] = $voice_sub;
        return view('voice.index', $array);

    }

}
