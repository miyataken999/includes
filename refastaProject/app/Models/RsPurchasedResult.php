<?php

namespace App\Models;

use App\kagoya_result_img;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class RsPurchasedResult extends Model
{
    protected $table = 'rs_purchased_result';

    const RESULT_DETAIL_PAGE_COLUMNS = [
        // 商品一覧
        'product_num' => 'ブランド名',
        'jewelry_name' => '宝石名',
        'category_id' => '大カテゴリー',
        'sub_category_id1' => '中カテゴリー',
        // NEW D在 start
//        'Parent_stone' => 'カラット', // 商品一覧にもある
        'DA_INTENSITY' => 'インテンシティ',
        'DA_OVERTONE' => 'オーバートーン',
        'DA_COLOR' => 'カラー',
        'DA_CLARITY' => 'クラリティ',
        'DA_CUT' => 'カット',
        'DA_POLISH' => 'ポリッシュ',
        'DA_SYMMETRY' => 'シンメトリ',
        'DA_FLUO' => '蛍光度',
        'DA_COLOR_FLUO' => '蛍光色',
        'DA_WIDE' => '幅',
        'PMT_EV001_DA_APPRAISER' => '鑑定期間',
        // NEW_D在　end
        'line' => 'ライン',
        'item_name' => 'アイテム名',
        'shape_supplement' => '補足',
        'product_style' => '形状名',
//        'parts' => '金具(パーツ)', // rs_purchased_resultにない
//        'country_of_origin' => '原産国', // rs_purchased_resultにない
//        'zipper' => 'ファスナー', // rs_purchased_resultにない
//        'guarantee' => 'ギャランティ', // rs_purchased_resultにない
        'designer' => 'デザイナー',
//        'yahoo_sozai' => '素材', // rs_purchased_resultにない
        'amount' => '内容量',
        'motif' => 'モチーフ',
        'producing_area' => '産地',
        'hahakai' => '母貝',
        'effect' => '効果',
        'shape' => 'シェイプ',
        'cutting_style' => 'カッティングスタイル',
        'Setting' => 'セッティング',
        'processing' => '処理',
        'chain_type' => 'チェーン種類',
        'Sleeve_Length' => '袖丈',
        'length' => '丈',
        'yahoo_kataban' => '型番',
        'stamp' => '刻印',
        'yahoo_condition2' => 'コンディション',
        'gold_property' => '金性',
        'Gram' => '金性グラム',
        'Parent_stone' => '親石',
        'Aside_stone' => '脇石',
        'number_of_stones' => '石の個数',
        'Ring_size' => 'リングサイズ',
        'satei_hi' => '査定日',
    ];

    const APPRAISER = [
        5 => '主代　正美',
        9 => '杉　兼太朗',
        19 => '長谷川　ひろ美',
        119 => '神路　竜之介',
        152 => 'Moa Kang',
        154 => '永守　世一郎',
    ];
    public $housyoku_Array = array(12,17,18,19,50,20,51,21,22,23,64,65,66,67,68,69);

    /**
     * 画像がない場合の代替画像の取得
     * 宝飾
     * 1 ブランド＋中カテ
     * 2 中カテ
     * 3 大カテ
     * 服飾
     * 1 ブランド(ノーブランド以外)＋型番
     * 2 ブランド＋中カテ
     * 3 ブランド＋大カテ
     * 4 中カテ
     * 5 大カテ
     * 入金額が一番高いやつ
     * @return string 画像URL。なければ空文字を返す。
     **/
    public function sample_img(): string
    {
      $rs_purchased_result_query = DB::table('rs_purchased_result')
        ->leftJoin('kagoya_result_img', 'rs_purchased_result.product_id', 'kagoya_result_img.product_id')
        ->leftJoin('market_product_img', 'rs_purchased_result.market_product_id', 'market_product_img.market_product_id')
        ->selectRaw('kagoya_result_img.imgpass as kagoya_result_img_pass, market_product_img.imgpass as market_product_img_pass')
        ->whereRaw("(kagoya_result_img.imgpass IS NOT NULL or market_product_img.imgpass IS NOT NULL)")
        ->orderBy(DB::raw('rs_purchased_result.nyuukin_price'), 'desc');

      if(in_array($this->category_id,$this->housyoku_Array)){
        // 宝飾クエリ
        // 1 ブランド＋中カテ
        $rs_purchased_result_query_clone = clone $rs_purchased_result_query;
        $rs_purchased_result = $rs_purchased_result_query_clone->whereRaw('rs_purchased_result.product_num = ? and rs_purchased_result.sub_category_id1 = ?',
          [$this->product_num, $this->sub_category_id1]
        )->first();
        if($rs_purchased_result != null){
          return $rs_purchased_result->market_product_img_pass ?: $rs_purchased_result->kagoya_result_img_pass;
        }
        // 2 中カテ
        $rs_purchased_result_query_clone = clone $rs_purchased_result_query;
        $rs_purchased_result = $rs_purchased_result_query_clone->whereRaw('rs_purchased_result.sub_category_id1 = ?',
          [$this->sub_category_id1]
        )->first();
        if($rs_purchased_result != null){
          return $rs_purchased_result->market_product_img_pass ?: $rs_purchased_result->kagoya_result_img_pass;
        }

        // 3 大カテ
        $rs_purchased_result_query_clone = clone $rs_purchased_result_query;
        $rs_purchased_result = $rs_purchased_result_query_clone->whereRaw('rs_purchased_result.category_id = ?',
          [$this->category_id]
        )->first();
        if($rs_purchased_result != null){
          return $rs_purchased_result->market_product_img_pass ?: $rs_purchased_result->kagoya_result_img_pass;
        }
      }else{
        // 服飾クエリ
        // 1 ブランド(ノーブランド以外)＋型番
        $replace_where = "
          `rs_purchased_result`.`product_id` > 0
          AND
          (
            `rs_purchased_result`.`yahoo_kataban` = ?
            AND `rs_purchased_result`.`yahoo_kataban` is not null
            AND `rs_purchased_result`.`yahoo_kataban` != ''
            AND `rs_purchased_result`.`yahoo_kataban` != '-'
            AND `rs_purchased_result`.`yahoo_kataban` != 'ー'
            AND `rs_purchased_result`.`yahoo_kataban` != '---'
            AND `rs_purchased_result`.`yahoo_kataban` not like '%不明%'
            AND `rs_purchased_result`.`yahoo_kataban` not like '%製%'
          ) AND (
            `rs_purchased_result`.`product_num` = ?
            AND `rs_purchased_result`.`product_num` > 0
            AND `rs_purchased_result`.`product_num` != 631
          )
        ";
        $rs_purchased_result_query_clone = clone $rs_purchased_result_query;
        $rs_purchased_result = $rs_purchased_result_query_clone->whereRaw($replace_where,
          [$this->yahoo_kataban, $this->product_num]
        )->first();
        if($rs_purchased_result != null){
          return $rs_purchased_result->market_product_img_pass ?: $rs_purchased_result->kagoya_result_img_pass;
        }

        // 2 ブランド＋中カテ
        $rs_purchased_result_query_clone = clone $rs_purchased_result_query;
        $rs_purchased_result = $rs_purchased_result_query_clone->whereRaw('rs_purchased_result.product_num = ? and rs_purchased_result.sub_category_id1 = ?',
          [$this->product_num, $this->sub_category_id1]
        )->first();
        if($rs_purchased_result != null){
          return $rs_purchased_result->market_product_img_pass ?: $rs_purchased_result->kagoya_result_img_pass;
        }

        // 3 ブランド＋大カテ
        $rs_purchased_result_query_clone = clone $rs_purchased_result_query;
        $rs_purchased_result = $rs_purchased_result_query_clone->whereRaw('rs_purchased_result.product_num = ? and rs_purchased_result.category_id = ?',
          [$this->product_num, $this->category_id]
        )->first();
        if($rs_purchased_result != null){
          return $rs_purchased_result->market_product_img_pass ?: $rs_purchased_result->kagoya_result_img_pass;
        }
        // 4 中カテ
        $rs_purchased_result_query_clone = clone $rs_purchased_result_query;
        $rs_purchased_result = $rs_purchased_result_query_clone->whereRaw('rs_purchased_result.sub_category_id1 = ?',
          [$this->sub_category_id1]
        )->first();
        if($rs_purchased_result != null){
          return $rs_purchased_result->market_product_img_pass ?: $rs_purchased_result->kagoya_result_img_pass;
        }
        // 5 大カテ
        $rs_purchased_result_query_clone = clone $rs_purchased_result_query;
        $rs_purchased_result = $rs_purchased_result_query_clone->whereRaw('rs_purchased_result.category_id = ?',
          [$this->category_id]
        )->first();
        if($rs_purchased_result != null){
          return $rs_purchased_result->market_product_img_pass ?: $rs_purchased_result->kagoya_result_img_pass;
        }
      }
      return '';// 全てない場合
    }
}
