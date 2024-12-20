<?php

namespace App\Models\Internet;

use Illuminate\Support\Facades\DB;
use \Illuminate\Database\Eloquent\Model;

class Sitemap extends Model
{
    protected $connection = "refasta_internet_editor";
    protected $table = "sitemaps";
    protected $primaryKey = "id";

    public $timestamps = false;


    /**
     * 同じドメイン設定、もしくは全サイト表示に絞る
     */
    public function is_display_domain()
    {
        if($this->is_hidden === 1){
            return false;
        }if($this->all_site_check === 1){
            return true;
        }elseif(isset($_SERVER["HTTP_WP_DOMAIN"])){
            if($_SERVER["HTTP_WP_DOMAIN"] == $this->domain){
                return true;
            }
        }
        return false;
    }

}