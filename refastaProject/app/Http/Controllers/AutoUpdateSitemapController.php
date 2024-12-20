<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class AutoUpdateSitemapController
{
    public function index()
    {
        //パスワード保護のterm_taxonomy_idを取得
        $refastawp_term_taxonomy = DB::table('refasta-kinkai_wp.refastawp_term_taxonomy')
            ->leftJoin('refasta-kinkai_wp.refastawp_terms', 'refasta-kinkai_wp.refastawp_term_taxonomy.term_id', '=', 'refasta-kinkai_wp.refastawp_terms.term_id')
            ->where('refastawp_terms.name', '=', 'パスワード保護')
            ->first();

        //７つの条件にあう投稿データを取得
        $refastawp_posts = DB::table('refasta-kinkai_wp.refastawp_posts')
            ->select('ID', 'post_title', 'post_status', 'post_type')
            ->where('post_type', '=', 'post')
            ->where('post_status', '=', 'publish')
            ->where('post_title', 'not like', '%再利用%') 
            ->where('post_title', 'not like', '%brandlist_name%')
            ->where('post_title', 'not like', '%テスト%')
            ->where('post_title', '!=', '')
            ->where('post_title', 'not like', '%gのダイヤモンド買取価格相場%')
            ->orderBy('ID')
            ->get();

        //取得した投稿データ（object_id=投稿ID）ごとに、「パスワード保護」タグ（term_taxonomy_id=47）がついているか検証
        //投稿IDを配列に格納する
        $toukou_ids = array();

        //本番化されている投稿データの条件
        foreach($refastawp_posts as $refastawp_post){
            
            $refastawp_term_relationships = DB::table('refasta-kinkai_wp.refastawp_term_relationships')
                ->where('object_id', '=', $refastawp_post->ID)
                ->where('term_taxonomy_id', '=', $refastawp_term_taxonomy->term_taxonomy_id)
                ->first();

            //パスワード保護のタグがない場合の条件式
            if($refastawp_term_relationships == NULL){
                //$toukou_idsに投稿IDを追加（$toukou_ids[] = 投稿ID）
                $toukou_ids[] = $refastawp_post->ID;
            }
        }

        //配列の宣言
        $toukou_ids_insert_array = array();
        $toukou_ids_update_array = array();

        foreach($toukou_ids as $toukou_id){
            //sitemapsテーブルに投稿IDが存在するかどうかを判定
            $sitemaps_data = DB::table('sitemaps')
                ->where('post_id', '=', $toukou_id)
                ->first();
            
            //sitemapsテーブルに投稿IDが存在しない場合の条件式
            if($sitemaps_data == NULL) {
                //追加する
                $toukou_ids_insert_array[] = $toukou_id;
            }else{
                //更新する
                $toukou_ids_update_array[] = $toukou_id;
            }
        }

        //配列$toukou_ids_insert_arrayと$toukou_ids_update_arrayを合体する
        $toukou_ids_merge = array_merge($toukou_ids_insert_array, $toukou_ids_update_array);



        //追加
        //タイトル、URLを取得する
        //配列$toukou_ids_insert_arrayから投稿IDを取り出す
        foreach($toukou_ids_insert_array as $toukou_id_insert){
            //投稿IDをキーにして、タイトルを取得する get_titleメソッドを使用する
            //タイトルの取得
            $title = $this->get_title($toukou_id_insert); //$toukou_ids_insertという引数をキーにしてget_titleメソッドに渡す
            //URLの取得
            $url = $this->get_url($toukou_id_insert);
            //表示ドメインの取得
            $custom_field = $this->get_custom_field($toukou_id_insert);
            $domain = $custom_field['page_domain'];
            $display_all_domain = $custom_field['display_all_domain'];
            //WPのカテゴリーの取得
            $categories = $this->get_categories($toukou_id_insert);
            //投稿日の取得
            $date = $this->get_date($toukou_id_insert);
            //更新日
            $modified = $this->get_modified($toukou_id_insert);
            //追加する処理
            $insert = $this->insert($toukou_id_insert, $title, $url, $date, $modified, $domain, $categories, $display_all_domain);
        }

        //更新
        //タイトル、URLを取得する
        //配列$toukou_ids_update_arrayから投稿IDを取り出す
        foreach($toukou_ids_update_array as $toukou_id_update){
            //投稿IDをキーにして、タイトルを取得する get_titleメソッドを使用する
            //タイトルの取得
            $title = $this->get_title($toukou_id_update);
            //URLの取得
            $url = $this->get_url($toukou_id_update);
            //表示ドメインの取得
            $custom_field = $this->get_custom_field($toukou_id_update);
            $domain = $custom_field['page_domain'];
            $display_all_domain = $custom_field['display_all_domain'];
            //WPのカテゴリーの取得
            $categories = $this->get_categories($toukou_id_update);
            //投稿日の取得
            $date = $this->get_date($toukou_id_update);
            //更新日
            $modified = $this->get_modified($toukou_id_update);
            //追加する処理
            $update = $this->update($toukou_id_update, $title, $url, $date, $modified, $domain, $categories, $display_all_domain);
        }

        //削除
        //配列の宣言 get_delete_dataメソッドを使用する
        $toukou_ids_delete_array = $this->get_delete_data($toukou_ids_merge);
        foreach($toukou_ids_delete_array as $toukou_id_delete){
            //削除する処理
            $delete = $this->delete($toukou_id_delete);
        }

    }

    //タイトルを取得するメソッド
    public function get_title($post_id)
    {
        $data = DB::table('refasta-kinkai_wp.refastawp_posts')->where("ID", $post_id)->first();

        //refastawp_postsテーブルのIDがnullか否かでタイトルを取得するかしないかの条件分岐
        if ($data != null) {
            return $data->post_title;
        } else {
            return "";
        }
    }

    //URLを取得するメソッド
    public function get_url($post_id)
    {
        $url = 'https://rifa.life/refasta_wordpress/wp-json/wp/v2/posts/' . $post_id;
        $data = file_get_contents($url);

        $data = mb_strstr($data, '{');
        $data = trim($data);
        $json = json_decode($data, true);
        $link = $json['link'] ?? '';
        return $link;
    }

    //表示ドメインを取得するメソッド
    public function get_custom_field($post_id)
    {
        //https://rifa.life/refasta_wordpress/wp-json/wp/v2/posts/16307
        $url = 'https://rifa.life/refasta_wordpress/wp-json/wp/v2/posts/' . $post_id;
        $data = file_get_contents($url);
        $data = mb_strstr($data, '{');
        $data = trim($data);
        $json = json_decode($data, true);
        return array(
            'page_domain' => $json['acf']['page_domain'],
            'display_all_domain' => $json['acf']['display_all_domain'],
        );
    }

//    page_domain
//    display_all_domain

    //wpのカテゴリーを取得するメソッド
    public function get_categories($post_id)
    {
        //テスト用として$post_id
        $url = 'https://rifa.life/refasta_wordpress/wp-json/wp/v2/categories?exclude=&post=' . $post_id;
        $data = file_get_contents($url);
        $data = trim($data);
        $categories = json_decode($data, true);

        //受け皿
        $categories_id_array = array();
        foreach($categories as $category){
            //値を取得する
            $category_id = $category['id'];
            //値を格納する
            $categories_id_array[] =  $category_id;
        }
            return json_encode($categories_id_array);
    }


    //投稿日を取得するメソッド
    public function get_date($post_id)
    {
        $data = DB::table('refasta-kinkai_wp.refastawp_posts')->where("ID", $post_id)->first();

        //refastawp_postsテーブルのIDがnullか否かでタイトルを取得するかしないかの条件分岐
        if ($data != null) {
            return $data->post_date;
        } else {
            return "";
        }
    }

    //更新日を取得するメソッド
    public function get_modified($post_id)
    {
        $data = DB::table('refasta-kinkai_wp.refastawp_posts')->where("ID", $post_id)->first();

        //refastawp_postsテーブルのIDがnullか否かでタイトルを取得するかしないかの条件分岐
        if ($data != null) {
            return $data->post_modified;
        } else {
            return "";
        }
    }

    //insert($toukou_id_insert, $title, $url, $date, $modified, $domain, $categories, $display_all_domain)
    //追加する処理
    public function insert($toukou_id_insert, $title, $url, $date, $modified, $domain, $categories, $display_all_domain)
    {
        $created_at = date("Y-m-d H:i:s");
        //追加する処理を記入していく
        DB::table('sitemaps')
            ->insert([
                //投稿IDの追加
                'post_id' => $toukou_id_insert,
                //タイトルの追加
                'page_title' => $title,
                //URLの追加
                'url' => $url,
                //作成した時間を追加
                'created_at' => $created_at,
                //wp_categoriesカラムを追加
                'wp_categories' => $categories,
                'domain' => $domain,
                'page_created_at' => $date,
                'page_updated_at' => $modified,
                'all_site_check' => $display_all_domain,

            ]);
    }

    //更新する処理
    public function update($toukou_id_update, $title, $url, $date, $modified, $domain, $categories, $display_all_domain)
    {
        $updated_at  = date("Y-m-d H:i:s");
        //更新する処理を記載していく
        DB::table('sitemaps')
            //更新対象を更新するための処理
            ->where('post_id', '=', $toukou_id_update)
            ->update([
                'page_title' => $title,
                'url' => $url,
                'updated_at' => $updated_at,
                'wp_categories' => $categories,
                'domain' => $domain,
                'page_created_at' => $date,
                'page_updated_at' => $modified,
                'all_site_check' => $display_all_domain,
            ]);
    }


    //削除対象の取得
    public function get_delete_data($toukou_ids_merge)
    {
        //削除対象とするものを取得する
        $get_delete_data = DB::table('sitemaps')
            ->where('is_not_delete', '=', '0')
            //本番化されている投稿idにはない　本番化されている投稿id以外
            ->whereNotIn('post_id', $toukou_ids_merge)
            ->get();
        return $get_delete_data;
    }

    //削除する処理
    public function delete($toukou_id_delete)
    {
        //削除する処理を記載していく
        DB::table('sitemaps')
            //削除対象を削除するための処理
            ->where('post_id', '=', $toukou_id_delete)
            ->delete();
    }

    //カテゴリー一覧の取得
    public function get_all_categories()
    {
        echo "開始" . date("His") . "<br>";
        for($pagei=1; $pagei<10000; $pagei++) {
            $url = "https://rifa.life/refasta_wordpress/wp-json/wp/v2/categories?per_page=100&page=" . $pagei;
            $data = file_get_contents($url);
            $data = trim($data);

            $categories = json_decode($data, true);
            if(!is_array($categories)){
                echo "終了" . date("His");
                return 'error';
            }
            if(count($categories) === 0){
                echo "終了" . date("His");
                return '終了';
            }

            foreach ($categories as $category) {
                //値を取得する
                $category_id = $category['id'];
                $category_name = $category['name'];
                $now_time = date("Y-m-d H:i:s");

                $target = DB::table('mst_sitemap_categories')->where('id', $category_id)->first();

                if ($target == NULL) {
                    DB::table('mst_sitemap_categories')
                        ->insert([
                            //IDの追加
                            'id' => $category_id,
                            //nameの追加
                            'name' => $category_name,
                            //作成した時間を追加
                            'created_at' => $now_time,
                        ]);
                } else {
                    DB::table('mst_sitemap_categories')
                        ->where('id', $category_id)
                        ->update([
                            //nameの更新
                            'name' => $category_name,
                            //作成した時間を更新
                            'updated_at' => $now_time,
                        ]);
                }

            }
        }
    }


}