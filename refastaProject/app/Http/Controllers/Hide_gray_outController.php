<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;


class Hide_gray_outController extends Controller
{
    public function index() {

        $gray_out ="
            SELECT * FROM `internet`.`mst_brand_batch_test`
            WHERE (`bag` = '0' or `bag` = '' or `bag` = NULL) 
            AND (`wallet` = '0'  or `wallet` = '' or `wallet` = NULL)
            AND (`accessories` = '0'  or `accessories` = '' or   `accessories` = NULL)
            AND (`catch` = '0'  or `catch` = '' or `catch` = NULL)
            AND (`shoes` = '0'  or `shoes` = '' or `shoes` = NULL)
            AND `outer` = '0'  or `outer` = '' or `outer` = NULL
            AND `tops` = '0'  or `tops` = '' or `tops` = NULL
            AND `camisole` = '0'  or `camisole` = '' or `camisole` = NULL
            AND `bottoms` = '0'  or `bottoms` = '' or `bottoms` = NULL
            AND `underwear` = '0'  or `underwear` = '' or `underwear` = NULL
            AND `Legwear` = '0'  or `Legwear` = '' or `Legwear` = NULL
            AND `hat` = '0'  or `hat` = '' or `hat` = NULL
            AND `other` = '0'  or `other` = '' or `other` = NULL
            AND `kids` = '0'  or `kids` = '' or `kids` = NULL
            AND `cosmetics` = '0'  or `cosmetics` = '' or `cosmetics` = NULL
            AND `gold` = '0'  or `gold` = '' or `gold` = NULL
            AND `tableware` = '0'  or `tableware` = '' or `tableware` = NULL
            AND `umbrella` = '0'  or `umbrella` = '' or `umbrella` = NULL
            AND `season` = '0'  or `season` = '' or `season` = NULL
            AND `new` = '0'  or `new` = '' or `new` = NULL
            LIMIT 5
           ";

        //    $brand_hidden_items = DB::select(
        //     SELECT * FROM `internet`.`mst_brand_batch_test`
        //     WHERE `bag` = '0' or `bag` = '' or `bag` = NULL
        //     AND `wallet` = '0'  or `wallet` = '' or `wallet` = NULL
        //     AND `accessories` = '0'  or `accessories` = '' or   `accessories` = NULL
        //     AND `catch` = '0'  or `catch` = '' or `catch` = NULL
        //     AND `shoes` = '0'  or `shoes` = '' or `shoes` = NULL
        //     AND `outer` = '0'  or `outer` = '' or `outer` = NULL
        //     AND `tops` = '0'  or `tops` = '' or `tops` = NULL
        //     AND `camisole` = '0'  or `camisole` = '' or `camisole` = NULL
        //     AND `bottoms` = '0'  or `bottoms` = '' or `bottoms` = NULL
        //     AND `underwear` = '0'  or `underwear` = '' or `underwear` = NULL
        //     AND `Legwear` = '0'  or `Legwear` = '' or `Legwear` = NULL
        //     AND `hat` = '0'  or `hat` = '' or `hat` = NULL
        //     AND `other` = '0'  or `other` = '' or `other` = NULL
        //     AND `kids` = '0'  or `kids` = '' or `kids` = NULL
        //     AND `cosmetics` = '0'  or `cosmetics` = '' or `cosmetics` = NULL
        //     AND `gold` = '0'  or `gold` = '' or `gold` = NULL
        //     AND `tableware` = '0'  or `tableware` = '' or `tableware` = NULL
        //     AND `umbrella` = '0'  or `umbrella` = '' or `umbrella` = NULL
        //     AND `season` = '0'  or `season` = '' or `season` = NULL
        //     AND `new` = '0'  or `new` = '' or `new` = NULL
        //     LIMIT 5
        // );
    //    var
//        var_dump($gray_out);
        $brand_hidden_items = DB::select($gray_out);
//        $id = $data[0]->id ?? 999;
        var_dump($brand_hidden_items);
//        return $data;
//        foreach ($brand_hidden_items as $data_key ) {
//            DB::table("ws_export_order_data")->insert($save_data);
//        }
    }
}

