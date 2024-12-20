<?php
namespace App\Models\WPBatch;

use Illuminate\Support\Facades\DB;

class WPBatch
{
    // convert_posts
    use ConvertTraits\ConvertPostsTrait;

    // convert_posts_media
    use ConvertTraits\ConvertMediaTrait;

    // convert_posts_postparent
    use ConvertTraits\ConvertPostParentTrait;

    // convert_post_meta
    use ConvertTraits\ConvertPostmetaTrait;

    // convert_terms
    use ConvertTraits\ConvertTermsTrait;

    // convert_terms
    use ConvertTraits\ConvertTermmetaTrait;

    // convert_post_tag
    use ConvertTraits\ConvertTermTaxonomyTrait;

    // convert_term_relationships
    use ConvertTraits\ConvertTermRelationshipsTrait;

    // convert_term_relationships
    use ConvertTraits\ConvertPostTagTrait;

    public $targetDB = 'refasta-kinkai_wp';
    // テストは　refasta-kinkai_wp_batchtest


    public function batch()
    {
        // 第一
        echo "<h1>convert_posts start</h1><br>";
        $this->convert_posts(); // 10秒
        echo "<h1>convert_media start</h1><br>";
        $this->convert_media(); // 2分
        echo "<h1>convert_terms start</h1><br>";
        $this->convert_terms(); // 40秒

        // 第二
        echo "<h1>convert_post_parent start</h1><br>";
        $this->convert_post_parent(); // 2分
        echo "<h1>convert_post_meta start</h1><br>";
        $this->convert_post_meta(); // 6分？

        // 第三
        echo "<h1>convert_termmeta start</h1><br>";
        $this->convert_termmeta(); // 1秒
        echo "<h1>convert_term_taxonomy start</h1><br>";
        $this->convert_term_taxonomy(); // 40秒
        echo "<h1>convert_term_relationships start</h1><br>";
        $this->convert_term_relationships(); // 4分
        echo "<h1>convert_post_tag start</h1><br>";
        $this->convert_post_tag(); // 50秒
        echo "<h1>THE END.</h1>";
    }


    /**
     * relationテーブルを利用したテーブルのupsert
     * @param $item 取得したレコード
     * @param string $targetTableName 反映するテーブル名 ex wp_posts
     * @param string $oldIdColumnName テーブルのプライマリーキーのカラム名 ex ID
     * @param array $upsertArray upsertしたいデータ
     * @param string $convert_type
     * @param string|null $oldIdColumnName2
     * @return void
     * @throws \Exception
     */
    public function upsertTable($item, string $targetTableName, string $oldIdColumnName, array $upsertArray, string $convert_type, string $oldIdColumnName2 = null)
    {
        $relationTableName = 'refasta_wp_batch_relations';
        $newWPHeadName = 'refasta';

            // term_relationshipsのterm_taxonomy_id判別
            if ($oldIdColumnName2 === null) {
                $old_id_2 = 0;
            } else {
                $old_id_2 = $item->$oldIdColumnName2;
            }

        // ここからしたはそのままでOK
        // relationテーブルにあれば（すでに追加済み）、updateする。
        $relation_table = DB::table($this->targetDB . '.' . $relationTableName)
            ->where('table', $targetTableName)
            ->where('old_id', $item->$oldIdColumnName)
            ->where('convert_type', $convert_type)
            ->where('old_id_2', $old_id_2)
            ->first();

        ob_start();
        echo date("YmdHis") . '<br>';
        $buffer = ob_get_contents();
        ob_end_clean();
        echo $buffer;

        if ($relation_table === null) {
            // ない場合
            // DB::beginTransaction();// トランザクション開始
            try {

                
                $insertID = DB::table($this->targetDB . '.' . $newWPHeadName . $targetTableName)->insertGetID($upsertArray);

                
            } catch (\Throwable $e) {
                // DB::rollBack();// なかったことにする
                throw new \Exception($e->getMessage());
            }
            // refasta_wp_batch_relations
            // relationsテーブルにold_post_idとnew_post_idを追加
            $relationArray = array(
                'table' => $targetTableName, // テーブル名
                'old_id' => $item->$oldIdColumnName, // 旧ID
                'new_id' => $insertID, // 新ID
                'convert_type' => $convert_type, // どこからか
                'created_at' => date("Y-m-d H:i:s"),
                'old_id_2' => $old_id_2, // どこからか
            );
            try {
                DB::table($this->targetDB . '.' . $relationTableName)->insert($relationArray);
            } catch (\Throwable $e) {
                // DB::rollBack();// なかったことにする
                throw new \Exception($e->getMessage());
            }
            // DB::commit();// 実際にDBに反映されるcommit処理
            return $insertID;
            
        } else {
            // ある場合


            DB::table($this->targetDB . '.' . $newWPHeadName . $targetTableName)->where($oldIdColumnName, $relation_table->new_id)->update($upsertArray);
            return $relation_table->new_id;
        }
        
    }
}
