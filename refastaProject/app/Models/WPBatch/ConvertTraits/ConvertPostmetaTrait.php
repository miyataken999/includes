<?php
namespace App\Models\WPBatch\ConvertTraits;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

trait ConvertPostmetaTrait
{
    public function convert_post_meta()
    {
        // kinkai convert
        $this->upsertNewWPPostMeta($this->get_post_meta('kinkai_lounge'), 'kinkai_lounge');
        // brakai convert
        $this->upsertNewWPPostMeta($this->get_post_meta('brakai_lounge'), 'brakai_lounge');
    }

    /**
     * lounge WPからpostsデータを取得
     * @param string $convert_type
     * @return Collection
     */
    public function get_post_meta(string $convert_type): Collection
    {
        if ($convert_type === 'kinkai_lounge') {
            $table = 'kinkaiwordpress.wp_postmeta';
        } elseif ($convert_type === 'brakai_lounge') {
            $table = 'brakaiwordpress.wp_postmeta';
        }
        return DB::table($table)->get();
    }

    /**
     * @param Collection $items
     * @param string $convert_type
     * @return bool
     */
    public function upsertNewWPPostMeta(Collection $items  , string $convert_type): bool
    {
        $targetTableName = 'wp_postmeta';
        $oldIdColumnName = 'meta_id';

        foreach ($items as $item) {
            $new_wppostmeta = DB::table($this->targetDB . '.refasta_wp_batch_relations')
                ->where('table', 'wp_posts')
                ->where('old_id', $item->post_id)
                ->where('convert_type', $convert_type)
                ->first();

            if($new_wppostmeta === null){
                continue;
            }

            $new_meta_id = $new_wppostmeta->new_id;
            
            $upsertArray = array(
                'post_id' => $new_meta_id,
                'meta_key' => $item->meta_key,
                'meta_value' => $item->meta_value,
            );
            
            $this->upsertTable($item, $targetTableName, $oldIdColumnName, $upsertArray, $convert_type);
        }
        return true;
    }

}


