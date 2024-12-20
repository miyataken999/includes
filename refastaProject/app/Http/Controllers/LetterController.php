<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class LetterController extends Controller
{
    public function index() {
        $letter = DB::table('voice_sub')
            ->whereRaw("((voice_id=1) OR (voice_id=4)) AND ((preview_kin_brand=1) OR (preveiw_kin_tegami=1)) ")
            ->orderBy("id", "desc")
            ->paginate(12);

        $links = $letter->links("vendor.pagination.custom");
        $links = str_replace("rifa.life/refastaProject/", "kinkaimasu.jp/", $links);

        // $links = str_replace("?page=", "?hosokawa_test=aaa&page=", $links);



        $array = array();
        $array["letter"] = $letter;
        $array["links"] = $links;
        return view ('letter.index', $array);
            
    }

}
