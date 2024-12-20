<?php
namespace App\Models\WPBatch\ConvertTraits;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

trait ConvertPostParentTrait
{

    /**
     * POST_PARENTテーブルのコンバート処理
     */
    public function convert_post_parent()
    {
        // kinkai convert
        $kinkaiGetPostParent = $this->get_post_parent('kinkai_lounge');
        $this->upsertNewWPPostParent($kinkaiGetPostParent, 'kinkai_lounge');

        // brakai convert
        $brakaiGetPostParent = $this->get_post_parent('brakai_lounge');
        $this->upsertNewWPPostParent($brakaiGetPostParent, 'brakai_lounge');

    }

    /**
     * lounge WPからpostParentデータを取得
     * @param string $convert_type
     * @return Collection
     */
    public function get_post_parent(string $convert_type): Collection
    {
        if ($convert_type === 'kinkai_lounge') {
            $table = 'kinkaiwordpress.wp_posts';
        } elseif ($convert_type === 'brakai_lounge') {
            $table = 'brakaiwordpress.wp_posts';
        }
        $post_parent = DB::table($table)->where('post_type', 'post')->orWhere('post_type', 'attachment')->get();
        return $post_parent;
    }

    /**
     * @param Collection $post_parent
     * @param string $convert_type
     * @return bool
     */
    public function upsertNewWPPostParent(Collection $items, string $convert_type): bool
    {
        $targetTableName = 'wp_posts';
        $oldIdColumnName = 'post_parent';

        foreach ($items as $item) {

        $new_post_parent = DB::table($this->targetDB . '.refasta_wp_batch_relations')
        ->where('table', 'wp_posts')
        ->where('old_id', $item->post_parent)
        ->where('convert_type', $convert_type)
        ->first();

        if($new_post_parent === null){
            continue;
        }

        $new_post_parent_id = $new_post_parent->new_id;

            // new WPへ追加（テスト時はテストDB）
            $upsertArray = array(
                'post_parent' => $new_post_parent_id,
            );




            $this->upsertTable($item, $targetTableName, $oldIdColumnName, $upsertArray, $convert_type);
        }
        return true;
    }


}


