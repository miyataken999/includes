<?php

namespace App\Http\Controllers;

use App\Models\Internet\Mst_chain_type;
use App\Models\Internet\Mst_color_fluo;
use App\Models\Internet\Mst_cutting_style;
use App\Models\Internet\Mst_effect;
use App\Models\Internet\Mst_hahakai;
use App\Models\Internet\Mst_intensity;
use App\Models\Internet\Mst_overtone;
use App\Models\Internet\Mst_producing_area;
use App\Models\Internet\Mst_quality;
use App\Models\Internet\Mst_shape;
use App\MstSearchCategory;
use Illuminate\Http\Request;
use App\Shouhin;
use App\KagoyaImage;
use App\Models\Internet\Mst_brand;
use App\Models\productsDetail;
use App\Models\RsPurchasedResult;
use App\Models\StoreCategory;
use App\Models\StoreSubCategory;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Controllers\ImageResizeController;
use pdo_connect_internet;
use PDO;
class PurchasedResultController extends Controller
{
    public function index(Request $request) {
        $inputs = $request->all();
        $inputs = array_filter($inputs, function ($v) {
                return $v != null;
        });
        $area = $_GET['area'];
        $itemsWithImages = DB::table("rs_purchased_result")
            ->Join('kagoya_result_img', 'rs_purchased_result.product_id', '=', 'kagoya_result_img.product_id')
            ->LeftJoin('mst_brand', 'mst_brand.id', '=', 'rs_purchased_result.product_num')
            ->join('Eoc', 'Eoc.ecc_id', '=', 'rs_purchased_result.ecc_id')
            ->join('box_for_result', 'box_for_result.box_id', '=', 'rs_purchased_result.box_id')
            ->join('mst_Eoc_address', 'mst_Eoc_address.todou', '=', 'rs_purchased_result.ecc_address1')
            ->where('rs_purchased_result.product_num', '!=', 631) 
            ->where('kagoya_result_img.imgpass', '!=', 631) 
            ->where('name_en', '=', $area)
            ->orWhere(function ($query) use ($area) {
                $query->where('todou_en', '=', $area)
                      ->whereNull('name_en');})
            ->select(
                'rs_purchased_result.*',
                'box_for_result.*',
                'kagoya_result_img.*',
                'mst_brand.name as product_name','mst_brand.*',
                'mst_Eoc_address.*');
            if ($itemsWithImages === null) {
                return response()->json(["status" => "error"], 200, [], JSON_UNESCAPED_UNICODE);
            }
            $residentOptions = DB::table("mst_Eoc_address")
                ->distinct()
                ->pluck('todou_en as area');
            if ($request->has('area')) {
                $itemsWithImages->where('mst_Eoc_address.todou_en', $request->input('area'));
            }
            // 買取相場取得、画像取得
            $rs_purchased_results =  $itemsWithImages
              ->orderBy("rs_purchased_result.product_id","desc")
              ->paginate(8);
            foreach($rs_purchased_results as $rs_purchased_result) {
                $priceResult = $this->jouge_keisan($rs_purchased_result);
                $imageData = $this->image_data($rs_purchased_result);
                $rs_purchased_result->image_data =  $imageData;
                $rs_purchased_result->price_result = $priceResult;
            }
            $array = [
                "rs_purchased_result" => $rs_purchased_results,
                "residentOptions" => $residentOptions,
            ];
            
            return view('purchased_result.index', $array);
    }
    public function jouge_keisan($param, $kai = '<br>'){

        $category_id = $param->category_id;
        $product_num = $param->product_num;
        $nyuukin_price = $param->nyuukin_price;

        $housyoku_array = array(12,17,18,19,50,20,51,21,22,23,64,65,66,67,68,69);

        // 入金額が買取額以下だった場合、買取額をベースに３割増の範囲で表示
        if($param->price >= $param->nyuukin_price){
            $price = $param->price;
            $price += $param->cost;
            $price += $param->identification_cost;
            $price += $param->other_cost;
            $kagen = 1;
            $jougen = 1.3;
            return $this->jouge_keisan_gattai($kagen , $jougen , $price);
        }

        // 時計以外の宝飾品大カテ
        if((in_array($category_id,$housyoku_array)) && ($product_num == 631)){
          $cal_price_jouge[0] = $this->round_num($nyuukin_price * 0.8);
          $cal_price_jouge[1] = $this->round_num($nyuukin_price * 0.9);
          if($kai){
            $price = $cal_price_jouge[0]."円～".$cal_price_jouge[1]."円";
          }else{
            $price = $cal_price_jouge[0]."円～".$cal_price_jouge[1]."円";
          }
          return $price;
        }// end 宝飾品

        $price = $nyuukin_price;
        
        if($price <= 1000){
          $price_result = $this->jouge_keisan_gattai(0.01 , 0.1 , $price);
        }elseif($price <= 2000){
          $price_result = $this->jouge_keisan_gattai(0.3 , 0.5 , $price);
        }elseif($price <= 3000){
          $price_result = $this->jouge_keisan_gattai(0.3 , 0.6 , $price);
        }elseif($price <= 5000){
          $price_result = $this->jouge_keisan_gattai(0.4 , 0.6 , $price);
        }elseif($price <= 10000){
          $price_result = $this->jouge_keisan_gattai(0.45 , 0.7 , $price);
        }elseif($price <= 15000){
          $price_result = $this->jouge_keisan_gattai(0.5 , 0.7 , $price);
        }elseif($price <= 18000){
          $price_result = $this->jouge_keisan_gattai(0.55 , 0.75 , $price);
        }elseif($price <= 20000){
          $price_result = $this->jouge_keisan_gattai(0.58 , 0.78 , $price);
        }elseif($price <= 30000){
          $price_result = $this->jouge_keisan_gattai(0.4 , 0.8 , $price);
        }elseif($price <= 40000){
          $price_result = $this->jouge_keisan_gattai(0.4 , 0.85 , $price);
        }elseif($price <= 60000){
          $price_result = $this->jouge_keisan_gattai(0.5 , 0.85 , $price);
        }elseif($price <= 70000){
          $price_result = $this->jouge_keisan_gattai(0.65 , 0.85 , $price);
        }elseif($price <= 80000){
          $price_result = $this->jouge_keisan_gattai(0.7 , 0.85 , $price);
        }elseif($price <= 90000){
          $price_result = $this->jouge_keisan_gattai(0.75 , 0.85 , $price);
        }else{
          $price_result = $this->jouge_keisan_gattai(0.8 , 0.9 , $price);
        }
        return $price_result;
    }
    public function jouge_keisan_gattai($kagen , $jougen , $price){
        $cal_price_jouge[0] = $this->round_num($price*$kagen);
        $cal_price_jouge[1] = $this->round_num($price*$jougen);
        if($price =! 'null'){
            $price = $cal_price_jouge[0]."円～".$cal_price_jouge[1]."円";
        }else{
            $price = $cal_price_jouge[0]."円～".$cal_price_jouge[1]."円";
        }
        return $price;
    }
    public function round_num($num){
      if($num <= 5000){
        $num = $num / 100;
        $num = round($num);
        $num = $num * 100;
      }else{
        $num = $num / 1000;
        $num = round($num);
        $num = $num * 1000;
      }
      if($num < 10){
        $num = 10;
      }
      return number_format($num);
    } 
    public function image_data($data,$width=200){
      $product_id = $data->product_id;
      $zip = $data->zip;
      $box_name = $data->box_name;
      $image_urlounge_img_pass = $zip;
      $image_pieces = explode(".", $image_urlounge_img_pass);
      $image_urlounge_box_name = $box_name;
      $image_data[] = $product_id;
  	if(($product_id)){
  		$ur_url = "/shop/services/image/{$image_urlounge_box_name}/{$image_pieces[1]}/{$product_id}_1.jpg";
  		$ur_width = $width;
  		$ur_resize_url = "/inc_ver01/resize/resize_img.php?url={$ur_url}&width={$ur_width}";
  		$image_data[0] = "<img src='https://kinkaimasu.jp/{$ur_resize_url}' alt='' width='{$ur_width}px'>";
  		for ($i=1; $i < 3; $i++) {
  			//画像圧縮処理
  			$img_i = $i+1;
  			$ur_url = "/shop/services/image/{$image_urlounge_box_name}/{$image_pieces[1]}/{$product_id}_{$img_i}.jpg";
  			$ur_width = $width;
  			$ur_resize_url = "/inc_ver01/resize/resize_img.php?url={$ur_url}&width={$ur_width}";
  			$image_data[$i] = "<img src='https://kinkaimasu.jp/{$ur_resize_url}' alt='' width='{$ur_width}px'>";
  		}
  	}else{
  		$image_data[0] = "<div class='res_img no_img'><span>No image</span><span>画像データはありません。</span></div>";
  	}
    return $image_data;
  }

    /**
     * 買取実績詳細のコンテンツ生成
     */
    public function purchased_result_detail(Request $request, $product_id=378811)
    {
        // $userIp = $request->ip();
        // $allowedIP = '160.16.203.204';
        // if ($userIp !== $allowedIP) {
        //     return response()->json([
        //         'errors' => "リクエストが正しくありません。",
        //     ], Response::HTTP_FORBIDDEN, [], JSON_UNESCAPED_UNICODE);
        // }
        $rsPurchasedResult = RsPurchasedResult::find($product_id);
        if ($rsPurchasedResult === null) {
          return response()->json([
            'errors' => "該当の商品が見つかりませんでした。",
          ], Response::HTTP_NOT_FOUND, [], JSON_UNESCAPED_UNICODE);
        }

        // 画像Resize処理
        $resizeController = new ImageResizeController($request);
        $imageUrls = json_decode($resizeController->index($product_id, $image_width = 200)->getContent(), true);
        $img_sub = [];
        $is_sample = false;
        if ((isset($imageUrls['storage'])) && ($imageUrls['storage'] === '')) {
            $img_main = $imageUrls[0];
        } else {
            if (count($imageUrls) > 0) {
                $img_main = $imageUrls[0];
                foreach ($imageUrls as $i => $imageUrl) {
                  $next = $i+1;
                  $img_sub[] = $imageUrl;
                }
            } else {
                $img_main = $rsPurchasedResult->sample_img();
                $is_sample = true;
            }
        }
        $product_images = [
            'img_main' => $img_main,
            'is_sample' => $is_sample,
            'img_sub' => $img_sub,
        ];
        $title = $this->createTitle($rsPurchasedResult);
        $priceRange = $this->jouge_keisan($rsPurchasedResult);

        $subCategoryId1 = $rsPurchasedResult->sub_category_id1;
        // 中カテゴリがご負担金 or キャンペーンの時何も返さない
        if ($subCategoryId1 === 839 || $subCategoryId1 === 840) {
            return response()->json([
                'errors' => "該当の商品が見つかりませんでした。",
            ], Response::HTTP_NOT_FOUND, [], JSON_UNESCAPED_UNICODE);
        }

        // リンク作成
        $storeSubCategory = StoreSubCategory::find($subCategoryId1);
        $colorstoneId = $storeSubCategory->colorstone_id ?? null;
        if (! is_null($colorstoneId)) {
            $MstSearchCategory = MstSearchCategory::where('table', 'mst_colorstone')->where('column_name', 'colorstone_id')->where('value', $colorstoneId)->first();
            $getParameter = $MstSearchCategory->get_parameter ?? '';
            if ($getParameter === 's_stone') {
                $MstSearchCategoryId = $MstSearchCategory->id ?? '';
                $searchJewelryUrl = 'https://kinkaimasu.jp/result/?search=(' . $getParameter . '=' . $MstSearchCategoryId . '#result_area_ttl';
                $jewelryName = $MstSearchCategory->search_name;
            }
        }

        $mstBrandId = Mst_brand::find($rsPurchasedResult->product_num)->id;
        if (! is_null($mstBrandId)) {
            $MstSearchCategory = MstSearchCategory::where('table', 'mst_brand')->where('column_name', 'product_num')->where('value', $mstBrandId)->first();
            $getParameter = $MstSearchCategory->get_parameter ?? '';
            if ($getParameter === 's_brand') {
                $MstSearchCategoryId = $MstSearchCategory->id ?? '';
                $searchBrandUrl = 'https://kinkaimasu.jp/result/?search=(' . $getParameter . '=' . $MstSearchCategoryId . '#result_area_ttl';
                $brandName = $MstSearchCategory->search_name;
            }
        }

        $linkArray = [
            'brandUrl' => $searchBrandUrl ?? null,
            'brandName' => $brandName ?? null,
            'jewelryUrl' => $searchJewelryUrl ?? null,
            'jewelryName' => $jewelryName ?? null,
        ];

        // 買取方法
        $purchaseMethod = [
            'method' => empty($rsPurchasedResult->Eoc_unfinished_id) ? '宅配' : '店頭',
            'prefecture_name' => $rsPurchasedResult->ecc_address1 ?? '',
        ];

        // ブランド詳細
        $brandDetail = '';

        // 商品ディティール
        $productDetail = [];
        foreach (RsPurchasedResult::RESULT_DETAIL_PAGE_COLUMNS as $columnName => $columnLabel) {
            if ($columnName === 'jewelry_name') { // 宝石名
                if (!empty($jewelryName)) $productDetail[$columnLabel] = $jewelryName;
                continue;
            }
            
            if (!empty($rsPurchasedResult->$columnName)) {
                if ($columnName === 'product_num') { // ブランド名
                    $brandName = Mst_brand::find($rsPurchasedResult->$columnName)->name ?? '';
                    if ($rsPurchasedResult->$columnName === 631) continue; // ノーブランドの時は保存しない
                    $productDetail[$columnLabel] = $brandName;
                    $brandDetailValue = Mst_brand::find($rsPurchasedResult->$columnName)->brand_details ?? '';
                    if (isset($brandDetailValue)) {
                        $brandDetail = $brandDetailValue;
                        $brandDetail = str_replace(["\r\n", "\r", "\n"], '<br>', $brandDetail);
                    }
                    continue;
                }
                if ($columnName === 'category_id') { // 大カテゴリー
                    $productDetail[$columnLabel] = StoreCategory::where('category_id', $rsPurchasedResult->$columnName)->first()->category_name ?? '';
                    continue;
                }
                if ($columnName === 'sub_category_id1') { // 中カテゴリー
                    $subCategoryName = $storeSubCategory->name ?? '';
                    // 大カテがあれば頭につけて、カテゴリーに変換
                    if (isset($productDetail['大カテゴリー'])) {
                        $productDetail['カテゴリー'] = $subCategoryName . $productDetail['大カテゴリー'];
                        unset($productDetail['大カテゴリー']);
                    } else {
                        $productDetail[$columnLabel] = $storeSubCategory->name ?? '';
                    }
                    continue;
                }
                if ($columnName === 'DA_WIDE' && $rsPurchasedResult->DA_WIDE && $rsPurchasedResult->DA_DEPTH) { // サイズ
                    $productDetail['サイズ'] = $rsPurchasedResult->DA_WIDE . '-' . $rsPurchasedResult->DA_HIGH . '×' . $rsPurchasedResult->DA_DEPTH . 'mm';
                    continue;
                }
                if ($columnName === 'shape_supplement') { // 補足
                    if ($rsPurchasedResult['中カテゴリー'] === $rsPurchasedResult->$columnName) {
                        continue;
                    }
                    $appraisalDate = $rsPurchasedResult->$columnName;
                    continue;
                }
                if ($columnName === 'product_style') { // 形状名
                    if ($rsPurchasedResult['大カテゴリー'] === $rsPurchasedResult->$columnName) {
                        continue;
                    }
                    $appraisalDate = $rsPurchasedResult->$columnName;
                    continue;
                }
                if ($columnName === 'producing_area') { // 産地
                    $productDetail[$columnLabel] = Mst_producing_area::find($rsPurchasedResult->$columnName)->name ?? '';
                    continue;
                }
                if ($columnName === 'hahakai') { // 母貝
                    $productDetail[$columnLabel] = Mst_hahakai::find($rsPurchasedResult->$columnName)->name ?? '';
                    continue;
                }
                if ($columnName === 'effect') { // 効果
                    $productDetail[$columnLabel] = Mst_effect::find($rsPurchasedResult->$columnName)->name ?? '';
                    continue;
                }
                if ($columnName === 'shape') { // シェイプ
                    $productDetail[$columnLabel] = Mst_shape::find($rsPurchasedResult->$columnName)->name ?? '';
                    continue;
                }
                if ($columnName === 'cutting_style') { // カッティングスタイル
                    $productDetail[$columnLabel] = Mst_cutting_style::find($rsPurchasedResult->$columnName)->name ?? '';
                    continue;
                }
                if ($columnName === 'chain_type') { // チェーン種類
                    $productDetail[$columnLabel] = Mst_chain_type::find($rsPurchasedResult->$columnName)->name ?? '';
                    continue;
                }
                if ($columnName === 'number_of_stones') { // 石の個数
                    $productDetail[$columnLabel] = $rsPurchasedResult->$columnName . '個';
                    continue;
                }
                if ($columnName === 'Gram') { // 金性グラム
                    if ($rsPurchasedResult->unit) {
                        $productDetail[$columnLabel] = $rsPurchasedResult->$columnName . $rsPurchasedResult->unit;
                    } else {
                        $productDetail[$columnLabel] = $rsPurchasedResult->$columnName;
                    }
                    continue;
                }
                if ($columnName === 'Parent_stone' || $columnName === 'Aside_stone') { // 親石 or 脇石
                    $productDetail[$columnLabel] = $rsPurchasedResult->$columnName . 'ct';
                    continue;
                }
                if ($columnName === 'satei_hi') { // 査定日
                    $appraisalDate = Carbon::parse($rsPurchasedResult->$columnName)->format('Y年m月');
                    continue;
                }

                $productDetail[$columnLabel] = $rsPurchasedResult->$columnName;
            }
        }

        // 採寸画面の採寸詳細
        if (isset($rsPurchasedResult->yahoo_size)) {
            $productDetail['買取詳細'] = strip_tags($rsPurchasedResult->yahoo_size);
        }

        // 査定人
        $appraiser = RsPurchasedResult::APPRAISER[$rsPurchasedResult->satei_by] ?? '';

        return view('purchased_result.result_table', [
            'product_images' => $product_images,
            'title' => $title,
            'priceRange' => $priceRange,
            'appraisalDate' => $appraisalDate ?? null,
            'linkArray' => $linkArray,
            'purchaseMethod' => $purchaseMethod,
            'productDetail' => $productDetail,
            'brandDetail' => $brandDetail,
            'appraiser' => $appraiser,
        ]);
    }

    /**
     * タイトルを生成するメソッド
     *
     * @return string 生成されたタイトル
     */
    public function createTitle($rsPurchasedResult): string
    {
        //前半ステータス
        if($rsPurchasedResult->sub_category_id1 == 415){
            return $this->title_dia($rsPurchasedResult);
        }

        $zenhan = "";
        $serachMasterTables = [
            [
                'function_name' => 'zenhanMstSel',
                'field_name' => 'producing_area',
                'kugiri' => "･",
                'mst_key' => 'id',
                'mst_sel_name' => 'name',
            ],
            [
                'function_name' => 'zenhanMstSel',
                'field_name' => 'designer',
                'kugiri' => "･",
                'mst_key' => 'id',
                'mst_sel_name' => 'name',
            ],
            [
                'function_name' => 'zenhanMstSel',
                'field_name' => 'collar_neck_line',
                'kugiri' => "･",
                'mst_key' => 'id',
                'mst_sel_name' => 'name'],

            [
                'function_name' => 'zenhanMstSel',
                'field_name' => 'breast',
                'kugiri' => "･",
                'mst_key' => 'id',
                'mst_sel_name' => 'name'],
            [
                'function_name' => 'zenhanMstSel',
                'field_name' => 'silhouette',
                'kugiri' => " ",
                'mst_key' => 'id',
                'mst_sel_name' => 'name'
            ],

            [
                'function_name' => 'zenhanMstSel',
                'field_name' => 'sleeve',
                'kugiri' => " ",
                'mst_key' => 'id',
                'mst_sel_name' => 'name'],
            [
                'function_name' => 'zenhanMstSel',
                'field_name' => 'number_of_stones',
                'kugiri' => " ",
                'mst_key' => 'id',
                'mst_sel_name' => 'name'],

            [
                'function_name' => 'zenhanMstSel',
                'field_name' => 'hahakai',
                'kugiri' => "･",
                'mst_key' => 'id',
                'mst_sel_name' => 'name'],

            [
                'function_name' => 'zenhanMstSel',
                'field_name' => 'toe',
                'kugiri' => "･",
                'mst_key' => 'id',
                'mst_sel_name' => 'name'],

            [
                'function_name' => 'zenhanMstSel',
                'field_name' => 'heel',
                'kugiri' => " ",
                'mst_key' => 'id',
                'mst_sel_name' => 'name'
            ],
            [
                'function_name' => 'zenhan',
                'field_name' => 'line',
                'kugiri' => "･",
            ],
            [
                'function_name' => 'zenhan',
                'field_name' => 'item_name',
                'kugiri' => "･",
            ],
            [
                'function_name' => 'zenhan',
                'field_name' => 'Sleeve_Length',
                'kugiri' => "･",
            ],
            [
                'function_name' => 'zenhan',
                'field_name' => 'motif',
                'kugiri' => " ",
            ],
            [
                'function_name' => 'zenhan',
                'field_name' => 'handle',
                'kugiri' => "･",
            ],
            [
                'function_name' => 'zenhan',
                'field_name' => 'length',
                'kugiri' => " ",
            ],
            [
                'function_name' => 'both',
                'field_name' => 'shape_supplement',
                'kugiri' => "･",
                'custom_field_name' => 'sub_category_id1',
                'mst_name' => "store_sub_categories",
                'mst_key' => 'id',
                'mst_sel_name' => 'name',
            ],
            [
                'function_name' => 'zenhanMstSel',
                'field_name' => 'side_gem',
                'kugiri' => "･",
                'mst_key' => 'id',
                'mst_sel_name' => 'name',
            ],
            [
                'function_name' => 'both',
                'field_name' => 'product_style',
                'kugiri' => "",
                'custom_field_name' => 'category_id',
                'mst_name' => "store_categories",
                'mst_key' => 'category_id',
                'mst_sel_name' => 'category_name',
            ],
            [
                'function_name' => 'zenhanMstSel',
                'field_name' => 'cloth',
                'kugiri' => " ",
                'mst_key' => 'id',
                'mst_sel_name' => 'name',
            ],
            [
                'function_name' => 'zenhan',
                'field_name' => 'amount',
            	'kugiri' => "",
            ],
            [
                'function_name' => 'zenhanMstSel',
                'field_name' => 'effect',
                'kugiri' => "･",
                'mst_key' => 'id',
                'mst_sel_name' => 'name',
            ],
            [
                'function_name' => 'zenhan',
                'field_name' => 'processing',
                'kugiri' => "･",
            ],
            [
                'function_name' => 'zenhanMstSel',
                'field_name' => 'chain_type',
                'kugiri' => "･",
                'mst_key' => 'id',
                'mst_sel_name' => 'name',
            ],
        ];

        foreach($serachMasterTables as $serachMasterTable) {
            $fieldName = $serachMasterTable['field_name'];
            $primaryValue = $rsPurchasedResult->{$fieldName};

            if ($serachMasterTable['function_name'] === 'both') {
                if (is_null($primaryValue)) {
                    $customFieldName = $serachMasterTable['custom_field_name'];
                    $customPrimaryValue = $rsPurchasedResult->{$customFieldName};
                    $zenhan = $this->zenhanMstSel($zenhan,$fieldName,$customPrimaryValue,$serachMasterTable['mst_key'],$serachMasterTable['mst_sel_name'],$serachMasterTable['kugiri'],$serachMasterTable['mst_name']);
                } else {
                    $zenhan = $this->zenhan($zenhan,$primaryValue,$serachMasterTable['kugiri']);
                }
            }

            if (is_null($primaryValue)) continue;
            if ($serachMasterTable['function_name'] === 'zenhanMstSel') {
                $zenhan = $this->zenhanMstSel($zenhan,$fieldName,$primaryValue,$serachMasterTable['mst_key'],$serachMasterTable['mst_sel_name'],$serachMasterTable['kugiri']);
            }
            if ($serachMasterTable['function_name'] === 'zenhan') {
                $zenhan = $this->zenhan($zenhan,$primaryValue,$serachMasterTable['kugiri']);
            }
        }

        $gold_property = $rsPurchasedResult->gold_property;
        if ($gold_property != ""){$gold_property = "/".$gold_property;}

        $Gram = $rsPurchasedResult->Gram;
        if ($Gram != ""){
            if ($rsPurchasedResult->unit != ""){
                if ($Gram != $rsPurchasedResult->Parent_stone){
                    $Gram = "-".$Gram.$rsPurchasedResult->unit;
                } else {
                    $Gram = "";
                }
            } else {
                $Gram = "-".$Gram."g";
            }
        }

        $Parent_stone = $rsPurchasedResult->Parent_stone;

        $Aside_stone = $rsPurchasedResult->Aside_stone;
        if ($Aside_stone != "") $Aside_stone = "/FD:".$Aside_stone."ct";

        $market_product_color_quality = "";
        if ($rsPurchasedResult->market_product_color_quality != ""){
            $market_product_color_quality = Mst_quality::find($rsPurchasedResult->market_product_color_quality);
            if (!is_null($market_product_color_quality)) {
                $market_product_color_quality = "/色品質：".$market_product_color_quality->name;
            }
        }

        $market_product_defect_quality = "";
        if ($rsPurchasedResult->market_product_defect_quality != ""){
            $market_product_defect_quality = Mst_quality::find($rsPurchasedResult->market_product_defect_quality);
            if (!is_null($market_product_defect_quality)) {
                $market_product_defect_quality = "/瑕疵品質：".$market_product_defect_quality->name;
            }
        }

        if (isset($rsPurchasedResult->Appraiser)) {
            $Appraiser = $rsPurchasedResult->Appraiser;
            if ($Appraiser != "") $Appraiser = "/".$Appraiser;
        } else {
            $Appraiser = '';
        }

        $Ring_size = $rsPurchasedResult->Ring_size;
        if ($Ring_size != "") $Ring_size = "/".$Ring_size;

        if (isset($rsPurchasedResult->Remarks)) {
            $Remarks = $rsPurchasedResult->Remarks;
            if ($Remarks != "") $Remarks = "/".$Remarks;
        } else {
            $Remarks = '';
        }

        $yahoo_kataban = $rsPurchasedResult->yahoo_kataban;
        if ($yahoo_kataban != "") $yahoo_kataban = "/".$yahoo_kataban;

        $title = $zenhan . $gold_property . $Gram . $Parent_stone . $Aside_stone . $market_product_color_quality . $market_product_defect_quality . $Appraiser . $Ring_size . $Remarks . $yahoo_kataban;

        return str_replace('・','･',$title);
    }

    public function title_dia($rsPurchasedResult)
    {
        $title = "ダイヤモンドルース/Diamond";

        $colmnname_DA = 'DA_WEIGHT';
        $colmnname = 'PMT_EV001_DA_WEIGHT';
        if($rsPurchasedResult->Parent_stone != ""){
            $title .= "/" . $rsPurchasedResult->Parent_stone.'ct';
        }elseif($rsPurchasedResult->$colmnname_DA != ""){
            $title .= "/" . (floor($rsPurchasedResult->$colmnname_DA*100)/100).'ct';
        }elseif($rsPurchasedResult->$colmnname != ""){
            $title .= "/" . (floor($rsPurchasedResult->$colmnname*100)/100).'ct';
        }

        if ($rsPurchasedResult->DA_INTENSITY != ""){
            $DA_INTENSITY = Mst_intensity::find($rsPurchasedResult->DA_INTENSITY);
            if (!is_null($DA_INTENSITY)) {
                $title .= "/" . $DA_INTENSITY->Name;
            }
        } elseif ($rsPurchasedResult->PMT_EV001_DA_INTENSITY != "") {
            $PMT_EV001_DA_INTENSITY = Mst_intensity::find($rsPurchasedResult->PMT_EV001_DA_INTENSITY);
            if (!is_null($PMT_EV001_DA_INTENSITY)) {
                $title .= "/" . $PMT_EV001_DA_INTENSITY->Name;
            }
        }

        if ($rsPurchasedResult->DA_OVERTONE != "") {
            $PMT_EV001_DA_INTENSITY = Mst_overtone::find($rsPurchasedResult->DA_OVERTONE);
            if (!is_null($PMT_EV001_DA_INTENSITY)) {
                $title .= "/" . $PMT_EV001_DA_INTENSITY->Name;
            }
        } elseif ($rsPurchasedResult->PMT_EV001_DA_OVERTONE != "") {
            $PMT_EV001_DA_INTENSITY = Mst_overtone::find($rsPurchasedResult->PMT_EV001_DA_OVERTONE);
            if (!is_null($PMT_EV001_DA_INTENSITY)) {
                $title .= "/" . $PMT_EV001_DA_INTENSITY->Name;
            }
        }

        $colmnname_DA = 'DA_COLOR';
        $colmnname = 'PMT_EV001_DA_COLOR';
        if ($rsPurchasedResult->$colmnname_DA != "") {
            if ($title != "") $title .= "/";
            $title .= $rsPurchasedResult->$colmnname_DA;
        } elseif ($rsPurchasedResult->$colmnname != "") {
            if ($title != "") $title .= "/";
            $title .= $rsPurchasedResult->$colmnname;
        }

        $colmnname_DA = 'DA_CLARITY';
        $colmnname = 'PMT_EV001_DA_CLARITY';
        if($rsPurchasedResult->$colmnname_DA != ""){
            if($title != ""){$title .= "/";}
            $title .= $rsPurchasedResult->$colmnname_DA;
        }if($rsPurchasedResult->$colmnname != ""){
            if($title != ""){$title .= "/";}
            $title .= $rsPurchasedResult->$colmnname;
        }

        $colmnname_DA = 'DA_CUT';
        $colmnname = 'PMT_EV001_DA_CUT';
        if($rsPurchasedResult->$colmnname_DA != ""){
            if($title != ""){$title .= "/";}
            $title .= $rsPurchasedResult->$colmnname_DA;
        }elseif($rsPurchasedResult->$colmnname != ""){
            if($title != ""){$title .= "/";}
            $title .= $rsPurchasedResult->$colmnname;
        }

        $colmnname_DA = 'DA_FLUO';
        $colmnname = 'PMT_EV001_DA_FLUO';
        if($rsPurchasedResult->$colmnname_DA != ""){
            if($title != ""){$title .= "/";}
            $title .= $rsPurchasedResult->$colmnname_DA;
        }elseif($rsPurchasedResult->$colmnname != ""){
            if($title != ""){$title .= "/";}
            $title .= $rsPurchasedResult->$colmnname;
        }

        if($rsPurchasedResult->DA_COLOR_FLUO != ""){
            $Mst_color_fluo = Mst_color_fluo::find($rsPurchasedResult->DA_COLOR_FLUO);
            if (!is_null($Mst_color_fluo)) {
                $title .= "/" . $Mst_color_fluo->Name;
            }
        }elseif($rsPurchasedResult->PMT_EV001_DA_COLOR_FLUO != ""){
            $Mst_color_fluo = Mst_color_fluo::find($rsPurchasedResult->PMT_EV001_DA_COLOR_FLUO);
            if (!is_null($Mst_color_fluo)) {
                $title .= "/" . $Mst_color_fluo->Name;
            }
        }

        //サイズ
        if(($rsPurchasedResult->DA_WIDE > 0) && ($rsPurchasedResult->DA_HIGH > 0) && ($rsPurchasedResult->DA_DEPTH > 0)){
            $size = "{$rsPurchasedResult->DA_WIDE}-{$rsPurchasedResult->DA_HIGH}×{$rsPurchasedResult->DA_DEPTH}mm";
        }elseif(($rsPurchasedResult->PMT_EV001_DA_WIDE > 0) && ($rsPurchasedResult->PMT_EV001_DA_HIGH > 0) && ($rsPurchasedResult->PMT_EV001_DA_DEPTH > 0)){
            $size = "{$rsPurchasedResult->PMT_EV001_DA_WIDE}-{$rsPurchasedResult->PMT_EV001_DA_HIGH}×{$rsPurchasedResult->PMT_EV001_DA_DEPTH}mm";
        }else{
            $size = "";
        }
        if($size != ""){
            if($title != ""){$title .= "/";}
            $title .= $size;
        }

        //為替
        if(isset($rsPurchasedResult->DA_RATE)){
            $rsPurchasedResult->DA_RATE = $rsPurchasedResult->DA_RATE+5;
            if($rsPurchasedResult->DA_RATE == 5){
                $rsPurchasedResult->DA_RATE = '';
            }else{
                $rsPurchasedResult->DA_RATE = '為替(対US)'.$rsPurchasedResult->DA_RATE.'円';
            }
            $colmnname = 'DA_RATE';
        }else{
            $rsPurchasedResult->PMT_EV001_DA_RATE = $rsPurchasedResult->PMT_EV001_DA_RATE+5;
            if($rsPurchasedResult->PMT_EV001_DA_RATE == 5){
                $rsPurchasedResult->PMT_EV001_DA_RATE = '';
            }else{
                $rsPurchasedResult->PMT_EV001_DA_RATE = '為替(対US)'.$rsPurchasedResult->PMT_EV001_DA_RATE.'円';
            }
            $colmnname = 'PMT_EV001_DA_RATE';
        }
        if($rsPurchasedResult->$colmnname != ""){
            if($title != ""){$title .= "/";}
            $title .= $rsPurchasedResult->$colmnname;
        }

        return $title;
    }

    public function zenhanMstSel($zenhan,$fieldName,$primaryValue,$mstKey,$mstSelName,$kugiri,$customMstTable=null)
    {
        if($primaryValue != ""){
            $mstName = $customMstTable ?: "mst_{$fieldName}";
            try {
                $item = DB::table($mstName)
                ->select($mstSelName)
                ->where($mstKey, $primaryValue)
                ->first();

                if ($item) {
                    $primaryValue = $item->{$mstSelName};
                    if (!empty($zenhan)) {
                        $zenhan .= $kugiri;
                    }
                    $zenhan .= $primaryValue;
                }
            } catch (\Exception $e) {
                // エラー処理
            }
        }
        return $zenhan;
    }

    public function zenhan($zenhan,$fielddata,$kugiri)
    {
        if($fielddata != ""){
            if($zenhan != ""){$zenhan .= $kugiri;}
            $zenhan .= $fielddata;
        }
        return $zenhan;
    }
}
