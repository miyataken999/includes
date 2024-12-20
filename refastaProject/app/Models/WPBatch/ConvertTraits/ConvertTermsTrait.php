<?php
namespace App\Models\WPBatch\ConvertTraits;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

trait ConvertTermsTrait
{
    public function convert_terms()
    {
        // kinkai convert
        $this->upsertNewWPTerms($this->get_terms('kinkai_lounge'), 'kinkai_lounge');
        // brakai convert
        $this->upsertNewWPTerms($this->get_terms('brakai_lounge'), 'brakai_lounge');
    }

    /**
     * lounge WPからpostsデータを取得
     * @param string $convert_type
     * @return Collection
     */
    public function get_terms(string $convert_type): Collection
    {
        if ($convert_type === 'kinkai_lounge') {
            $table = 'kinkaiwordpress.wp_terms';
        } elseif ($convert_type === 'brakai_lounge') {
            $table = 'brakaiwordpress.wp_terms';
        }
        return DB::table($table)->get();
    }

    /**
     * @param Collection $items
     * @param string $convert_type
     * @return bool
     */
    public function upsertNewWPTerms(Collection $items  , string $convert_type): bool
    {
        $targetTableName = 'wp_terms';
        $oldIdColumnName = 'term_id';

        foreach ($items as $item) {
            $upsertArray = array(
                'name' => $item->name,
                'slug' => $item->slug,
                'term_group' => $item->term_group,
            );
            $this->upsertTable($item, $targetTableName, $oldIdColumnName, $upsertArray, $convert_type);
        }
        return true;
    }

}


