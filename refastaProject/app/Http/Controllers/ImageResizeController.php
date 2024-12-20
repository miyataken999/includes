<?php

namespace App\Http\Controllers;
use App\Models\RsPurchasedResultStoragePath;
use App\Models\RsPurchasedResult;
use Illuminate\Support\Facades\DB;
use Google\Cloud\Storage\StorageClient;

class ImageResizeController extends Controller
{
    public function index($result_id, $image_width = 200)
    {
        $client = new StorageClient([
            'projectId' => 'innate-empire-193705',
            'keyFile' => json_decode(file_get_contents('keys/' . env('GOOGLE_CLOUD_STROAGE_CREDENTIALS')), true),
        ]);
        $bucket = $client->bucket('resultimage'); // 作成したバケット名に画像保存
        $rs_purchased_result = RsPurchasedResult::select(['product_id', 'box_id'])->where('id', $result_id)->first();
        if($rs_purchased_result === null) {
          return response()->json([]);
        }

        $product_id = $rs_purchased_result->product_id;
        // 画像パスがあれば即return
        $rs_purchased_result_storage_paths = RsPurchasedResultStoragePath::where('product_id', $product_id)->orderBy('image_number')->get();
        if (count($rs_purchased_result_storage_paths) > 0) {
          $result_array = $rs_purchased_result_storage_paths->map(function ($rs_purchased_result_storage_path) use($bucket) {
            return $bucket->object(''.$rs_purchased_result_storage_path->storage_path . $rs_purchased_result_storage_path->image_number . '.jpg')
            ->signedUrl(time()+180);
          });  
          return response()->json($result_array);
        }

        $box_res = DB::table('box_for_result')->select(['zip', 'box_name'])->where('box_id', $rs_purchased_result->box_id)->first();
        if($box_res === null) {
          // これは過去？
          // $kagoya_result_img = DB::table('kagoya_result_img')->where('product_id', $product_id)->first();
          // if($kagoya_result_img != null){
          //   if($kagoya_result_img->imgpass != ''){
          //     return response()->json(['storage' => '', $kagoya_result_img->imgpass]);
          //   }
          // }

          // EC系以外の市場販売系の画像
          $market_product_img = DB::table('market_product_img')->where('market_product_id', $rs_purchased_result->market_product_id)->first();
          if($market_product_img != null){
            if($market_product_img->imgpass != ''){
              return response()->json(['storage' => '', $market_product_img->imgpass]);
            }
          }
          $market_product_img = DB::table('market_product_img')->where('product_id', $rs_purchased_result->product_id)->first();
          if($market_product_img != null){
            if($market_product_img->imgpass != ''){
              return response()->json(['storage' => '', $market_product_img->imgpass]);
            }
          }
          return response()->json([]);
        }
        $image_urlounge_img_pass = $box_res->zip;
        $image_pieces = explode(".", $image_urlounge_img_pass);
        $image_urlounge_box_name = $box_res->box_name;
        $product_id = $rs_purchased_result->product_id;

    $context = stream_context_create([
        'ssl' => [
            'verify_peer'      => false,
            'verify_peer_name' => false
        ]
    ]);

        $result_array = [];
        for ($i = 1; $i <= 10; $i++) {
            if (isset($image_pieces[1])) {
              $image_pieces_name = $image_pieces[1];
            } else {
              $image_pieces_name = $image_pieces[0];
            }
          
            $ur_url = "/shop/services/image/{$image_urlounge_box_name}/{$image_pieces_name}/{$product_id}_{$i}.jpg";

            // 画像圧縮処理
            $ur_resize_url = "/inc_ver01/resize/resize_img.php?url={$ur_url}&width={$image_width}";
            $img_file = file_get_contents("https://kinkaimasu.jp" . $ur_resize_url, false, $context);
            if ($img_file == '') {
                // 画像がなかったらその先はもうないので、ここでreturnする
                return response()->json($result_array);
            }

            $folder_pass = "product_image/{$rs_purchased_result->box_id}/{$product_id}/";
            DB::beginTransaction();
            try {
                $bucket->upload($img_file, [
                    'resumable' => true,
                    'name' => "{$folder_pass}{$i}.jpg"
                ]);
                RsPurchasedResultStoragePath::updateOrInsert(
                  [
                    'product_id' => $product_id,
                    'image_number' => $i,
                  ],
                  ['storage_path' => $folder_pass, 'updated_at' => now()]
                );
                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                // todo ここでechoしても仕方ないので何かしらの通知を残す
                echo $e->getMessage()."ImageResizeControllerの画像圧縮処理\n";
                continue;
            }
            $result_array[] = $bucket->object(''.$folder_pass . $i . '.jpg')->signedUrl(time()+180);
        }
        return response()->json($result_array);
    }

}
