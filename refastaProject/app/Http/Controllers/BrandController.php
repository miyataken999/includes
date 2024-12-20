<?php

namespace App\Http\Controllers;

use DB;

use App\Models\Internet\Mst_brand;

class BrandController
{
    /**
     * mst_brandの買取していない対象の項目arr
     * @var string[]
     */
    public $hide_rule_arr = [
        "bag"
        ,"wallet"
        ,"accessories"
        ,"catch"
        ,"shoes"
        ,"outer"
        ,"tops"
        ,"camisole"
        ,"bottoms"
        ,"underwear"
        ,"Legwear"
        ,"hat"
        ,"other"
        ,"kids"
        ,"cosmetics"
        ,"gold"
        ,"tableware"
        ,"umbrella"
        ,"season"
        ,"new"
    ];

    /**
     * 買取していないブランドの絞り込みwhere作成
     * @return string
     */
    public function make_where_hide_rule(){
        $where = "";
        foreach($this->hide_rule_arr as $hide_column){
            if($where != ""){
                $where .= " AND ";
            }
            $where .= "(`{$hide_column}` = 0 OR `{$hide_column}` = '' OR `{$hide_column}` IS NULL)";
        }
        return $where;
    }


    /**
     * 買取していないブランドのフラグを立てる
     * is_all_gray
     * @return void
     */
    public function make_hide_flag()
    {
        set_time_limit(0);
        $whereRaw = $this->make_where_hide_rule();
        $all_hide_brand_data = Mst_brand::whereRaw("($whereRaw)")->get();

        foreach($all_hide_brand_data as $hide_brand_datum){
            $hide_brand_datum->is_all_gray = 1;
            try{
                $hide_brand_datum->save();
            }catch (\Throwable $th){
                echo $th->getMessage();
                exit;
            }
            echo $hide_brand_datum->id;
            echo "<br>";
            echo $hide_brand_datum->name;
            echo "<br>";
            @ob_flush();
            @flush();
        }
    }
}