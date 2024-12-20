<?php

namespace App\Models\Internet;

use Illuminate\Support\Facades\DB;
use \Illuminate\Database\Eloquent\Model;

class Mst_sitemap_categories extends Model
{
    protected $connection = "refasta_internet_editor";
    protected $table = "mst_sitemap_categories";
    protected $primaryKey = "id";

    public $timestamps = false;


    /**
     * カテゴリー別ページURLのありなし取得
     * @return bool
     */
    public function is_exists_url()
    {
        $sitemaps = DB::table('sitemaps')->get();
        foreach($sitemaps as $sitemap){
            if($this->is_display_domain($sitemap) === false){
                continue;
            }
            $categories = json_decode($sitemap->wp_categories, true);
            if(is_array($categories)){
                foreach($categories as $category){
                    if($category == $this->id){
                        return true;
                    }
                }
            }
        }
        return false;
    }


    /**
     * 同じドメイン設定、もしくは全サイト表示に絞る
     */
    public function is_display_domain($sitemap)
    {
        if($sitemap->all_site_check === 1){
            return true;
        }elseif(isset($_SERVER["HTTP_WP_DOMAIN"])){
            if($_SERVER["HTTP_WP_DOMAIN"] == $sitemap->domain){
                return true;
            }
        }
        return false;
    }

}