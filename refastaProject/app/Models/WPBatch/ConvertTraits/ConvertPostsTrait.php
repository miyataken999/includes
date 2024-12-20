<?php
namespace App\Models\WPBatch\ConvertTraits;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

trait ConvertPostsTrait
{

    /**
     * POSTSテーブルのコンバート処理
     */
    public function convert_posts()
    {
        // kinkai convert
        $kinkaiGetPosts = $this->get_posts('kinkai_lounge');
        $this->upsertNewWPPosts($kinkaiGetPosts, 'kinkai_lounge');

        // brakai convert
        $brakaiGetPosts = $this->get_posts('brakai_lounge');
        $this->upsertNewWPPosts($brakaiGetPosts, 'brakai_lounge');

    }

    /**
     * lounge WPからpostsデータを取得
     * @param string $convert_type
     * @return Collection
     */
    public function get_posts(string $convert_type): Collection
    {
        if ($convert_type === 'kinkai_lounge') {
            $table = 'kinkaiwordpress.wp_posts';
        } elseif ($convert_type === 'brakai_lounge') {
            $table = 'brakaiwordpress.wp_posts';
        }
        $posts = DB::table($table)->where('post_type', 'post')->where('post_status', 'publish')->get();
        return $posts;
    }

    /**
     * @param Collection $posts
     * @param string $convert_type
     * @return bool
     */
    public function upsertNewWPPosts(Collection $items, string $convert_type): bool
    {
        $targetTableName = 'wp_posts';
        $oldIdColumnName = 'ID';

        foreach ($items as $item) {

            // new WPへ追加（テスト時はテストDB）
            $upsertArray = array(
                'post_author' => $item->post_author,
                'post_date' => $item->post_date, // 投稿日
                'post_date_gmt' => $item->post_date_gmt, // 投稿日（gmt）
                'post_content' => $item->post_content,
                'post_title' => $item->post_title,
                'post_excerpt' => $item->post_excerpt, // 記事の抜粋。loungeWPでは記事内埋め込み画像のキャプションとして使用されている
                'post_status' => $item->post_status,
                'comment_status' => $item->comment_status,// コメントを受け付けるかどうか。post_type「post」「attachment」デフォルト値open。他close
                'ping_status' => $item->ping_status,// トラックバック・ピンバックを受け付けるかどうか。post_type「post」デフォルト値open。他close
                'post_password' => $item->post_password, // 使用しているテーブル無し
                'post_name' => $item->post_name,
                'to_ping' => $item->to_ping, // PINGを打ったURLのリスト。使用しているテーブル無し
                'pinged' => $item->pinged, // PINGを打ったURLのリスト。使用しているテーブル無し
                'post_modified' => $item->post_modified,
                'post_modified_gmt' => $item->post_modified_gmt,
                'post_content_filtered' => $item->post_content_filtered,
                'post_parent' => $item->post_parent,
                'guid' => $item->guid, // 記事や画像などのオブジェクトに付与されている一意(重複しない)のID。WordPressのフィードを作成するのに使用される。
                'menu_order' => $item->menu_order, // 投稿の順序
                'post_type' => $item->post_type,
                'post_mime_type' => $item->post_mime_type,
                'comment_count' => $item->comment_count,
            );




            $this->upsertTable($item, $targetTableName, $oldIdColumnName, $upsertArray, $convert_type);
        }
        return true;
    }


}


