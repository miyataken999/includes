<?php

namespace App\Http\Controllers\Tasks;

use Illuminate\Http\Request;
use DB;

class TaskController
{
    /**
     * 規約HTMLをファイルキャッシュ
     */
    public static function put_kiyaku_html()
    {
        $url = 'https://rifa.life/refastaProject/get_wp_posts/42832';
        $html = file_get_contents($url);
        $put_file_path = storage_path('app') . '/public/kiyaku.html';
        file_put_contents($put_file_path, $html);
    }
}