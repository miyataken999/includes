<?php
namespace App\Models\WPBatch\ConvertTraits;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

trait ConvertPostTagTrait
{
    public function convert_post_tag()
    {
        // kinkai convert
        $this->upsertNewWPPostTag($this->get_post_tag('kinkai_lounge'), 'kinkai_lounge');
        // brakai convert
        $this->upsertNewWPPostTag($this->get_post_tag('brakai_lounge'), 'brakai_lounge');
    }

    /**
     * lounge WPからpostsデータを取得
     * @param string $convert_type
     * @return Collection
     */
    public function get_post_tag(string $convert_type): Collection
    {
        $table = $this->targetDB . '.refasta_wp_batch_relations';

        return DB::table($table)
        ->where('convert_type', $convert_type)
        ->where('table', 'wp_posts')
        ->get();
    }

    /**
     * @param Collection $items
     * @param string $convert_type
     * @return bool
     */
    public function upsertNewWPPostTag(Collection $items  , string $convert_type): bool
    {
        $targetTableName = 'wp_term_relationships';
        $oldIdColumnName = 'object_id';
        $oldIdColumnName2 = 'term_taxonomy_id';

        foreach ($items as $item) {
            ob_start();
            echo date("YmdHis") . '<br>';
            $buffer = ob_get_contents();
            ob_end_clean();
            echo $buffer;
            if($convert_type === 'kinkai_lounge') {
                $posttag_id = 120;
            } elseif($convert_type === 'brakai_lounge') {
                $posttag_id = 120;
            }
                if($item->new_id === null){
                    continue;
                }
                $new_object_id = $item->new_id;

                    $upsertArray = array(
                        'object_id' => $new_object_id,
                        'term_taxonomy_id' => $posttag_id
                    );

                    $target = DB::table($this->targetDB . '.refastawp_term_relationships')->where('object_id' , $new_object_id)->where('term_taxonomy_id' , $posttag_id)->first();
                    
                    if($target === null){
                        DB::table($this->targetDB . '.refastawp_term_relationships')->insert($upsertArray);
                    }
                    
        }
        return true;
    }

}


