<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

use App\Models\Internet\Mst_sitemap_categories;
use App\Models\Internet\Sitemap;


class SitemapController extends Controller
{
    //カテゴリーの一覧
    public function category_list()
    {
        $mst_sitemap_categories = Mst_sitemap_categories::where('is_hidden', '=', '0' )->get();

        $array = array(
            'mst_sitemap_categories' => $mst_sitemap_categories,
        );

        return view("sitemap.index", $array);
    }

    //カテゴリー別ページURL一覧
    public function index_by_category_id(Request $request)
    {
        $category_id = $_GET['id'];
        $sitemaps = Sitemap::get(); //$sitemaps：sitemapsテーブルのデータ

        $sitemaps_urls = array(); //$sitemaps_urls：sitemapsテーブルのURL

        //sitemapテーブルのデータを回す
        foreach($sitemaps as $sitemap){
            if($sitemap->is_display_domain() === false){
                continue;
            }

            $category = $sitemap->wp_categories; //wp_categoryテーブルのデータが格納されている変数
            $categories_data = json_decode($category, true);
            $link = $sitemap->url;
            $link = str_replace('refasta_wordpress/', '', $link);
            if(isset($_SERVER["HTTP_WP_DOMAIN"])){
                $link = str_replace('rifa.life/', $_SERVER["HTTP_WP_DOMAIN"] . '/', $link);
            }
            
            if(is_array($categories_data)){
                foreach($categories_data as $category_data){
                    if($category_data == $category_id){
                        $sitemaps_urls[] = array(
                            'url' => $link,
                            'title' => $sitemap->page_title,
                        );
                        continue;
                    }
                }
            }
        }
        //タイトルを取得
        $mst_category = DB::table("mst_sitemap_categories")->where("id", $category_id)->first();
        $page_title = $mst_category->name ?? "サイトマップ";

        $array = array(
            'sitemaps_urls' => $sitemaps_urls,
            'page_title' => $page_title,
        );

        return view("sitemap.index_by_category_id", $array);

    }

}