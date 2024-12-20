<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;

use App\Models\Internet\Zendesk__data;
use App\Models\Internet\Zendesk_chats;
use App\Models\Internet\Members_favorite;

class RealChatDetailController extends Controller
{
    public function index(Request $request)
    {
        //chat_idをキーにして任意の詳細ページを表示する
        if(!isset($_GET['id'])){
            return 'URLが正しくありません。';
        }
        //zendesk__dataテーブルのレコードを取得する
        $zendesk__data = Zendesk__data::where('open_flag', true)->where('check_flag', false)->where('chat_id', '=', $_GET['id'])->orderBy('chat_id')->first();
        if ($zendesk__data === null) {
            return 'URLが正しくありません。';
        }
        $zendesk_chats_array = Zendesk_chats::where('chat_id', '=', $_GET['id'])
            ->where('open_flag', true)
            ->where('sender_type', '!=', 'NULL')
            ->where('sender_type', '!=', 'Trigger')
            ->orderBy('id')
            ->get();

        // タグ
        $tags = explode("\n", $zendesk__data->hash_tag);
        // 同じカテゴリの投稿
        $zendesk__data_same_category_posts = Zendesk__data::where('open_flag', true)->where('check_flag', false)->where('category', '=', $zendesk__data->category)->orderBy('chat_id')->limit(10)->get();
        // 同じタグの投稿
        $zendesk__data_same_tag_posts = Zendesk__data::where('open_flag', true)->where('check_flag', false);
        $zendesk__data_same_tag_posts->where(function($query) use($tags) {
            foreach($tags as $tag) {
                $query->orWhere('hash_tag', 'like', '%'.$tag.'%');
            }
            return $query;
        });
        $zendesk__data_same_tag_posts = $zendesk__data_same_tag_posts->orderBy('chat_id', 'desc')->limit(10)->get();

        //「参考になった」ボタンで使用
        //参考になったの数を取得
        $members_favorite = Members_favorite::where('category', '=', 'チャット')->where('item_id', '=', $zendesk__data->chat_id);
        $fav_count = $members_favorite->count();

        $array = array(
            'realchat_detail_chat_id' => $_GET['id'],
            'zendesk__data' => $zendesk__data,
            'zendesk_chats_array' => $zendesk_chats_array,
            'tags' => $tags,
            'zendesk__data_same_category_posts' => $zendesk__data_same_category_posts,
            'zendesk__data_same_tag_posts' => $zendesk__data_same_tag_posts,
            'fav_count' => $fav_count,
        );
        return view("realchat_detail.index", $array);
    }



    public function updateCount(Request $request) {
        $category = 'チャット';
        $updated_at  = date("Y-m-d H:i:s");
        $is_up = $request->input('is_up');
        $tokumei_id = $request->input('tokumei_id');
        $item_id = $request->input('item_id');

        if ($is_up == 'true') {
            Members_favorite::create(['category' => $category, 'update_at' => $updated_at, 'tokumei_id' => $tokumei_id, 'item_id' => $item_id]);
        } else {
            Members_favorite::where([
                'category' => $category,
                'tokumei_id' => $tokumei_id,
                'item_id' => $item_id
            ])->delete();
        }
    }

    public function getFlag(Request $request) {
        $category = 'チャット';
        $tokumei_id = $request->input('tokumei_id');
        $item_id = $request->input('item_id');

        $flag = Members_favorite::where(['category' => $category, 'tokumei_id' => $tokumei_id, 'item_id' => $item_id])->exists();
        return $flag ? 'true' : 'false';
    }


    public function get_title_and_description($id) {
        $zendesk__data = Zendesk__data::where('open_flag', true)->where('check_flag', false)->where('chat_id', '=', $id)->orderBy('chat_id')->first();
        if ($zendesk__data === null) {
            return "";
        }

        $array = array();
        $array["title"] = $zendesk__data->title;
        $json = json_encode($array);
        return $json;

    }
}
