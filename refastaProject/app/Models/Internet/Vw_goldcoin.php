<?php

namespace App\Models\Internet;

use \Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Vw_goldcoin extends Model
{
    protected $connection = "refasta_internet_editor";
    protected $table = "vw_goldcoin";
    // protected $primaryKey = "id";

    // public $timestamps = false;
    /**
     * 計算用重量のカラム名
     */
    public $total_weight = "total_weight_for_calc";


    /**
     * 貴金属の価格情報を取得する
     */
    public static function get_property_price()
    {
        $property_price = array();
        $cls_tb_price = DB::table("kinkai.tb_price")->orderBy("resist_data_type_date", "desc")->first();
        $property_price["tb_price"]["gold"] = $cls_tb_price->gold;
        $property_price["tb_price"]["platinum"] = $cls_tb_price->platinum;
        $property_price["tb_price"]["silver"] = $cls_tb_price->silver;
        $property_price["tb_price"]["palladium"] = $cls_tb_price->palladium;
        foreach (DB::table("kinkai.tb_gold")->get() as $item) {
            $property_price["tb_gold"][$item->id] = $item->item_rate * $property_price["tb_price"]["gold"];
        }
        foreach (DB::table("kinkai.tb_platinum")->get() as $item) {
            $property_price["tb_platinum"][$item->id] = $item->item_rate * $property_price["tb_price"]["platinum"];
        }
        foreach (DB::table("kinkai.tb_silver")->get() as $item) {
            $property_price["tb_silver"][$item->id] = $item->item_rate * $property_price["tb_price"]["silver"];
        }
        foreach (DB::table("kinkai.tb_palladium")->get() as $item) {
            $property_price["tb_palladium"][$item->id] = $item->item_rate * $property_price["tb_price"]["palladium"];
        }
        foreach (DB::table("kinkai.tb_combi")->get() as $item) {
            $property_price["tb_combi"][$item->id] = $item->combi_price;
        }

        return $property_price;
    }

    /**
     * マスターデータ取得
     */
    public static function get_master_data()
    {
        $master_data = array();
        $items = DB::table("mst_material")->get();
        foreach($items as $item){
            $master_data['mst_material'][$item->id] = $item->name;
        }
        $items = DB::table("mst_price_unit")->get();
        foreach($items as $item){
            $master_data['mst_price_unit'][$item->id] = $item->name;
        }
        $items = DB::table("mst_gold_property_parents")->get();
        foreach($items as $item){
            $master_data['mst_gold_property_parents'][$item->id] = $item;
        }
        return $master_data;
    }


    /**
     * 商品のタイトル生成
     * @return $coin_array
     */
    public function create_title($coin_array, $master_data)
    {
        $coin_array["oz"] = "";
        if($this->ounce != ''){
            $coin_array["title"] = $this->ounce;
            $coin_array["title"] .= "oz ";
            $coin_array["oz"] = "";
            $coin_array["oz"] = $this->ounce."oz";
    
        }
        $coin_array["title"] = "";
        $coin_array["title"] .= $this->coin_name;
        if($this->material != ''){
            $material = $master_data['mst_material'][$this->material];
            $coin_array["title"] .= $material;
        }
        if(($this->face_value_number != '') && ($this->face_value_unit != '')){
            if($this->face_value != ''){
                $coin_array["title"] .= " ";
                $coin_array["title"] .= $this->face_value;
                $coin_array["gakumen_ttl"] = "";
                $coin_array["gakumen_ttl"] .= $this->face_value;
            }else{
                $gakumen = number_format(str_replace(".00", "", $this->face_value_number),1);
                $gakumen = str_replace(".0","",$gakumen);
                $coin_array["title"] .= $gakumen;
                $face_value_unit = $master_data['mst_price_unit'][$this->face_value_unit];
                $coin_array["title"] .= $face_value_unit;
                $coin_array["gakumen_ttl"] = $gakumen;
                $coin_array["gakumen_ttl"] .= $face_value_unit;
            }
            if($this->ounce != ''){
                $coin_array["gakumen_ttl"] .= " ".$this->ounce;
                $coin_array["gakumen_ttl"] .= "oz(オンス)";
            }
        }
        if($this->reference_quality != ''){
            $coin_array["title"] .= " ";
            $gold_property_parents = $master_data['mst_gold_property_parents'][$this->reference_quality];
            if($gold_property_parents->display_name != ''){
                $coin_array["title"] .= $gold_property_parents->display_name;
            }else{
                $coin_array["title"] .= $gold_property_parents->val;
            }
        }else{
            $gold_property_parents = '';
        }
    
        $coin_array["title"] = str_replace("\"","”",$coin_array["title"]);
        $coin_array["title"] = str_replace("'","’",$coin_array["title"]);
    
        return array(
            'coin_array' => $coin_array,
            'gold_property_parents' => $gold_property_parents,
        );
    }



    /**
     * 金額生成
     */
    public function create_price($coin_array, $gold_property_parents, $property_price)
    {
        if(($this->material != 1) && ($this->material != 4) && ($this->material != 7) && ($this->material != 6) && ($this->country_name == 4) && ($this->face_value_number != '')){
            // 国内で and 金貨+金メダル＋小判以外のもの（と銀メダル？）
            $coin_array["price"] = number_format(floor(($this->face_value_number)*0.95));
        }elseif($gold_property_parents == ''){
            $coin_array["price"] = '';
        }else{
            if(($gold_property_parents->table != '') && ($gold_property_parents->reference_id != '') && ($this->total_weight != '') ){
                $tmp_price = $property_price[$gold_property_parents->table][$gold_property_parents->reference_id];
                $tmp_price = floor($tmp_price);
                $coin_array["before_tmp_price"] = "tmp_price";
                //国内
                if($this->country_name == 4){
                    if($this->material == 1){ //金貨
                        if($this->coefficient != ''){
                            $tmp_price = ($property_price["tb_price"]["gold"]*($this->coefficient));
                        }else{
                            $tmp_price = ($property_price["tb_price"]["gold"]*0.85);
                        }
                    }else{
                        // add 銀メダルとその他のif
                        if(
                            ($this->material == 7) OR($this->material == 4) OR($this->material == 6)
                        ){
                            $tmp_price = $tmp_price*1; //金メダル・小判・銀メダルはそのまま
                        }else{
                            $tmp_price = $tmp_price*0.95;
                        }
                    }
                }// end japan
    
                if(is_numeric($this->{$this->total_weight})){
                    $coin_array["price"] = number_format(floor(($this->{$this->total_weight})*(string)$tmp_price));
                }else{
                    $coin_array["price"] =  "";
                }
                
            }else{
                $coin_array["price"] = '';
            }
        }
        if(($coin_array["price"] != '') && ($coin_array["price"] > 0)){
            $coin_array["price"] .= '<span>円</span>';
        }else{
            $coin_array["price"] = '<span>ASK</span>';
        }
        // $coin_array["tmp_price"] = $tmp_price." total_weight ".$this->$total_weight."<br>".(($this->$total_weight)*$tmp_price)."<br>".number_format(($this->$total_weight)*$tmp_price);
    
        return $coin_array;
    }
}