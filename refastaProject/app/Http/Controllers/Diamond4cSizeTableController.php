<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

use App\Models\Kinkai\Dia_size_table;

class Diamond4cSizeTableController extends Controller
{
    public function index(Request $request)
    {
        $dia_size_table_array = Dia_size_table::orderBy('id')->get();//dia_size_tableテーブルのレコードを取得する
        $array = array(
            'dia_size_table_array' => $dia_size_table_array,
        );
        return view("4c_sizetable.index", $array);
    }
}