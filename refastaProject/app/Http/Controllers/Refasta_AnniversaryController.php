<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class Refasta_AnniversaryController extends Controller
{
        public function index()
    {
        $anniversary = strtotime('2010-02-09 00:00:00');
        $refasta_established_anniversary = (date("Y") - date("Y", $anniversary));
        if (date("md") <= date("md", $anniversary)) {
            $refasta_established_anniversary--;
        }
        return response()->json([
            'refasta_established_anniversary' => $refasta_established_anniversary
        ]);
    }
}
