<?php
namespace App\Models\WPBatch\ConvertTraits;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

trait ConvertTermmetaTrait
{
    public function convert_termmeta()
    {
        // kinkai convert
        $this->upsertNewWPTermmeta($this->get_termmeta('kinkai_lounge'), 'kinkai_lounge');
        // brakai convert
        $this->upsertNewWPTermmeta($this->get_termmeta('brakai_lounge'), 'brakai_lounge');
    }

    /**
     * lounge WPからpostsデータを取得
     * @param string $convert_type
     * @return Collection
     */
    public function get_termmeta(string $convert_type): Collection
    {
        if ($convert_type === 'kinkai_lounge') {
            $table = 'kinkaiwordpress.wp_termmeta';
        } elseif ($convert_type === 'brakai_lounge') {
            $table = 'brakaiwordpress.wp_termmeta';
        }
        return DB::table($table)->get();
    }

    /**
     * @param Collection $items
     * @param string $convert_type
     * @return bool
     */
    public function upsertNewWPTermmeta(Collection $items  , string $convert_type): bool
    {
        $targetTableName = 'wp_termmeta';
        $oldIdColumnName = 'meta_id';

        foreach ($items as $item) {
            $upsertArray = array(
                'term_id' => $item->term_id,
                'meta_key' => $item->meta_key,
                'meta_value' => $item->meta_value,
            );
            $this->upsertTable($item, $targetTableName, $oldIdColumnName, $upsertArray, $convert_type);
        }
        return true;
    }

}


