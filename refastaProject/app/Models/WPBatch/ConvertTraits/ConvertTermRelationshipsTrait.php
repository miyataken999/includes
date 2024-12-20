<?php
namespace App\Models\WPBatch\ConvertTraits;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

trait ConvertTermRelationshipsTrait
{
    public function convert_term_relationships()
    {
        // kinkai convert
        $this->upsertNewWPTermRelationships($this->get_term_relationships('kinkai_lounge'), 'kinkai_lounge');
        // brakai convert
        $this->upsertNewWPTermRelationships($this->get_term_relationships('brakai_lounge'), 'brakai_lounge');
    }

    /**
     * lounge WPからpostsデータを取得
     * @param string $convert_type
     * @return Collection
     */
    public function get_term_relationships(string $convert_type): Collection
    {
        if ($convert_type === 'kinkai_lounge') {
            $table = 'kinkaiwordpress.wp_term_relationships';
        } elseif ($convert_type === 'brakai_lounge') {
            $table = 'brakaiwordpress.wp_term_relationships';
        }
        return DB::table($table)->get();
    }

    /**
     * @param Collection $items
     * @param string $convert_type
     * @return bool
     */
    public function upsertNewWPTermRelationships(Collection $items  , string $convert_type): bool
    {
        $targetTableName = 'wp_term_relationships';
        $oldIdColumnName = 'object_id';
        $oldIdColumnName2 = 'term_taxonomy_id';

        foreach ($items as $item) {

            $new_relations = DB::table($this->targetDB . '.refasta_wp_batch_relations')
                ->whereRaw(
                    '((`table` = ? and old_id = ?) OR (`table` = ? and old_id = ?))'
                    , [
                        'wp_posts', $item->object_id, 'wp_term_taxonomy', $item->term_taxonomy_id
                    ]
                    )
                ->where('convert_type', $convert_type)
                ->get();

            if(count($new_relations) !== 2){
                continue;
            }
            
            $new_object_id = '';
            $new_term_taxonomy_id = '';
            foreach($new_relations as $new_relation) {
                if ($new_relation->table == 'wp_posts') {
                    $new_object_id = $new_relation->new_id;
                } else {
                    $new_term_taxonomy_id = $new_relation->new_id;
                }
            }

            $upsertArray = array(
                'object_id' => $new_object_id,
                'term_taxonomy_id' => $new_term_taxonomy_id,
                'term_order' => $item->term_order,
            );

            $this->upsertTable($item, $targetTableName, $oldIdColumnName, $upsertArray, $convert_type, $oldIdColumnName2);
        }
        return true;
    }

}


