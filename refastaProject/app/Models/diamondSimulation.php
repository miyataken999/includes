<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class diamondSimulation extends Model
{
    // クラリティとCoefficientの対応表
    const GROUPS_CLARITY_COEFFICIENTS = [
        'VVS1' => [
            'FL' => 1.15,
            'IF' => 1.1,
            'VVS1' => 1
        ],
        'VVS2' => [
            'VVS2' => 1
        ],
        'VS1' => [
            'VS1' => 1
        ],
        'VS2' => [
            'VS2' => 1
        ],
        'SI1' => [
            'SI1' => 1
        ],
        'SI2' => [
            'SI2' => 1,
            'SI3' => 0.9,
            'I1' => 0.5,
            'I2' => 0.4,
            'I3' => 0.2
        ]
    ];
    const GROUPS_CUT_COEFFICIENTS = [
        'Excellent' => [
            '3EX' => 1.1,
            '3EXHC' => 1.2,
            '3EXC' => 1.1,
            '3EXH' => 1.1,
            'Excellent' => 1,
            'EXCELLENT' => 1,
            'EXHC' => 1.1,
            'EXH' => 1.05
        ],
        'Good' => [
            'Good' => 1,
            'GOOD' => 1,
            'ROUND' => 1,
            'FAIR' => 0.6,
            'POOR' => 0.3,
            'ASSCHER' => 0.65,
            'BAQUETTE' => 0.65,
            'BRIOLETTE' => 0.65,
            'BULLETS' => 0.65,
            'CUSHION MODIFIED' => 0.65,
            'CUSHION' => 0.65,
            'EMERALD' => 0.65,
            'EUROPEAN CUT' => 0.65,
            'FLANDERS' => 0.65,
            'HEXAGONAL' => 0.65,
            'HALF MOON' => 0.65,
            'HEART' => 0.65,
            'KITE' => 0.65,
            'LOZENGE' => 0.65,
            'MARQUISE' => 0.65,
            'OCTAGONAL' => 0.65,
            'OLD MINER' => 0.65,
            'OVAL' => 0.65,
            'PENTAGONAL' => 0.65,
            'PRINCESS' => 0.65,
            'PEAR' => 0.65,
            'RADIANT' => 0.65,
            'RECTANGULAR' => 0.65,
            'ROSE' => 0.65,
            'SHIELD' => 0.65,
            'SQ EMERALD' => 0.65,
            'SQUARE' => 0.65,
            'STAR' => 0.65,
            'TRAPEZOID' => 0.65,
            'TRIANGULAR' => 0.65,
            'TAPER' => 0.65,
            'TRILLIANT' => 0.65,
            'X' => 0.65,
        ],
        'Verygood' => [
            'VERYGOOD' => 1,
            'VERY GOOD' => 1,
            'VGHC' => 1.05,
            'VGH' => 1,
        ],
    ];

    const FLUORESCENCE_COEFFICIENTS = [
        'NONE' => 1,
        'FAINT' => 0.95,
        'MEDIUM' => 0.85,
        'VERY STRONG' => 0.6,
        'STRONG' => 0.75,
        'VERY SLIGHT' => 0.95,
        'WHITISH' => 0.95
    ];

    const APPRAISER_COEFFICIENTS = [
        'AGTジェムラボラトリー' => 1,
        'AIGS' => 1,
        'B鑑' => 0.7,
        'DGL' => 1,
        'GGS' => 0.8,
        'GIA' => 1,
        'GRS' => 1,
        'IGI' => 1,
        'NGL' => 0.8,
        'ジェム・リサーチ･ラボラトリー' => 0.9,
        'ジェムクリニックセンター' => 0.8,
        'ジェムリサーチジャパン' => 0.9,
        'ジャパン ジェム アプレイザル' => 0.9,
        'ジャパン テクニカル ジェム ラボラトリー東京' => 0.9,
        'ジャパンジェムグレーディングセンター' => 0.8,
        'ジュエルトレーディングラボラトリー' => 0.8,
        'ティファニー' => 1,
        'メトロ宝石鑑別センター' => 0.9,
        'ユニバーサルジェムラボラトリー' => 0.9,
        '鑑別書(保証書)付' => 0.9,
        '全国宝石学協会' => 0.9,
        '中央宝石研究所' => 1,
        '東京宝石科学アカデミー' => 0.8,
        '日独宝石研究所' => 1,
        '日米宝石鑑別センター' => 1,
        '日本ジェムテイスティングセンター' => 1,
        '日本彩珠宝石研究所' => 1,
        '日本宝石学協会' => 0.8,
        '宝石鑑別団体協議会' => 1,
    ];
    const ISSUE_YEAR_COEFFICIENTS = [
        '３ヶ月以内' => 1,
        '今年' => 1,
        '１年以上' => 0.9,
    ];
}
