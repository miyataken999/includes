<?php
namespace App\Models\WPBatch\ConvertTraits;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

trait ConvertTermTaxonomyTrait
{

    public function convert_term_taxonomy()
    {
        // kinkai convert
        $this->upsertNewWPTermTaxonomy($this->get_term_taxonomy('kinkai_lounge'), 'kinkai_lounge');
        // brakai convert
        $this->upsertNewWPTermTaxonomy($this->get_term_taxonomy('brakai_lounge'), 'brakai_lounge');
    }

    /**
     * lounge WPからpostsデータを取得
     * @param string $convert_type
     * @return Collection
     */
    public function get_term_taxonomy(string $convert_type): Collection
    {
        if ($convert_type === 'kinkai_lounge') {
            $table = 'kinkaiwordpress.wp_term_taxonomy';
        } elseif ($convert_type === 'brakai_lounge') {
            $table = 'brakaiwordpress.wp_term_taxonomy';
        }
        return DB::table($table)->get();
    }

    /**
     * @param Collection $items
     * @param string $convert_type
     * @return bool
     */
    public function upsertNewWPTermTaxonomy(Collection $items  , string $convert_type): bool
    {
        $targetTableName = 'wp_term_taxonomy';
        $oldIdColumnName = 'term_taxonomy_id';

        foreach ($items as $item) {

            $upsertArray = array(
                'term_id' => $item->term_id,
                'taxonomy' => $item->taxonomy,
                'description' => $item->description,
                'parent' => $item->parent,
                'count' => $item->count,
            );

            $parent_id = $item->parent;



            $relationTableName = 'refasta_wp_batch_relations';

            $term_id_table = DB::table($this->targetDB . '.' . $relationTableName)
            ->where('table', 'wp_terms')
            ->where('old_id', $item->term_id)
            ->where('convert_type', $convert_type)
            ->first();

            if($term_id_table === null){
                continue;
            }

            $term_id = $term_id_table->new_id;

            $upsertArray = array(
                'term_id' => $term_id,
                'taxonomy' => $item->taxonomy,
                'description' => $item->description,
                'parent' => $parent_id,
                'count' => $item->count,
            );

            $this->upsertTable($item, $targetTableName, $oldIdColumnName, $upsertArray, $convert_type);

        }

        return true;
    }

}


