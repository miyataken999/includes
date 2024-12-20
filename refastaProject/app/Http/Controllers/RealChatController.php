<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\Internet\Zendesk__data;
use App\Models\Internet\Mst_revoco_category;
use App\Models\Internet\Zendesk_agent;

class RealChatController extends Controller
{
    public function index(Request $request)
    {
        $ua_data = '';
        if($ua_data == 'sp'){
            $display_count = 5;//表示件数
            $btn_count = 3;//ページャーのボタンの数
        }else{
            $display_count = 10;
            $btn_count = 5;
        }

        //検索の処理のところ
        $zendesk__datas_query = Zendesk__data::where('open_flag', true)->where('check_flag', false);
        if(!empty($_GET['agent'])) {
            $zendesk__datas_query->where('agent_names', '=', $_GET['agent']);
        }
        if(!empty($_GET['category'])) {
            $zendesk__datas_query->where('category', '=', $_GET['category']);
        }
        if(!empty($_GET['rating'])) {
            $zendesk__datas_query->where('rating', '=', $_GET['rating']);
        }
        if(!empty($_GET['tag'])) {
            $zendesk__datas_query->where('hash_tag', 'like', '%'.$_GET['tag'].'%');
        }
        if(!empty($_GET['keyword'])) {
            $zendesk__datas_query->where(function($query) {
                $keyword = htmlspecialchars($_GET['keyword'], ENT_QUOTES);
                return $query->where('comment', 'LIKE', "%{$keyword}%")
                    ->orWhere('category', 'LIKE', "%{$keyword}%")
                    ->orWhere('title', 'LIKE', "%{$keyword}%")
                    ->orWhere('best_answer', 'LIKE', "%{$keyword}%")
                    ->orWhere('hash_tag', 'LIKE', "%{$keyword}%")
                    ->orWhere('agent_names', 'LIKE', "%{$keyword}%")
                    ->orWhere('department_name', 'LIKE', "%{$keyword}%");
            });
        }
        $zendesk__datas_query->orderBy('chat_id', 'desc');
        $Zendesk__data_total_count = $zendesk__datas_query->get()->count();

        $Zendesk__data_items = $zendesk__datas_query->paginate($display_count);
        //検索処理　終了

        //カテゴリーのマスタ取得
        $Mst_revoco_category = Mst_revoco_category::orderBy('sort')->orderBy('id')->get();
        //担当者のマスタ取得
        $Zendesk_agent = Zendesk_agent::orderBy('id')->get();

        $array = array(
            'Zendesk__data_items' => $Zendesk__data_items,
            'Zendesk__data_total_count' => $Zendesk__data_total_count,
            'Mst_revoco_category' => $Mst_revoco_category,
            'Zendesk_agent' => $Zendesk_agent,
        );
        return view("realchat.index", $array);
    }
}
