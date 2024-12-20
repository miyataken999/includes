<?php

namespace App\Http\Controllers;
use App\Models\Internet\Mst_brand;
use App\Models\Internet\Mst_colorstone;
use App\rs_purchased_result;
class PankuzuController extends Controller
{
    public function index($rs_purchased_result_id) {
        $rs_purchased_result = rs_purchased_result::where('id', $rs_purchased_result_id)->first();

        $pankuzu_array = $this->create_pankuzu_array($rs_purchased_result);

        $array = [
            'pankuzu_array' => $pankuzu_array,
        ];
        return view('pankuzu.index', $array);
    }
    public function array_rule()
    {
        $gold_property_array = [
            'Pt900' => ['url' => '/platinum/pt900/','name' => 'Pt900',],
            'Pt900-Pt850' => ['url' => '/platinum/pt850/','name' => 'Pt850',],
            'Pm900' => ['url' => '/platinum/pt900/','name' => 'Pt900',],
            'K18YG/750' => ['url' => '/gold/k18/','name' => '18金,K18',],
            'K18WG/750' => ['url' => '/gold/k18/','name' => '18金,K18',],
            'K18PG/750' => ['url' => '/gold/k18/','name' => '18金,K18',],
            'K18(WG無)' => ['url' => '/gold/k18/','name' => '18金,K18',],
            'After K18' => ['url' => '/gold/k10/','name' => '10金,K10',],
            'Pt950' => ['url' => '/platinum/pt950/','name' => 'Pt950',],
            'K14WG/585' => ['url' => '/gold/k14/','name' => '14金,K14',],
            'K14PG/585' => ['url' => '/gold/k14/','name' => '14金,K14',],
            'K14YG/585' => ['url' => '/gold/k14/','name' => '14金,K14',],
            'K14(WG無)' => ['url' => '/gold/k14/','name' => '14金,K14',],
            'K10YG/416' => ['url' => '/gold/k10/','name' => '10金,K10',],
            'K10WG/416' => ['url' => '/gold/k10/','name' => '10金,K10',],
            'K10PG/416' => ['url' => '/gold/k10/','name' => '10金,K10',],
            'K10(WG無)' => ['url' => '/gold/k10/','name' => '10金,K10',],
            'Sv925' => ['url' => '/gold/silver/','name' => 'Sv925,スターリングシルバー',],
            'K18/750×Pt900コンビ-Kメイン' => ['url' => '','name' => 'コンビ：K18/Pt900-K',],
            'K18/750×Pt900コンビ-Pメイン' => ['url' => '','name' => 'コンビ：K18/Pt900-P',],
            'K18/750×Pt900コンビ-5:5' => ['url' => '','name' => '',],
            'K18/750×Pt850コンビ-Kメイン' => ['url' => '','name' => 'コンビ：K18/Pt850-K',],
            'K18/750×Pt850コンビ-Pメイン' => ['url' => '','name' => 'コンビ：K18/Pt850-P',],
            'K18/750×Pt850コンビ-5:5' => ['url' => '','name' => '',],
            'K18/750×Pt950コンビ-Kメイン' => ['url' => '','name' => 'コンビ：K18/Pt950-K',],
            'K18/750×Pt950コンビ-Pメイン' => ['url' => '','name' => 'コンビ：K18/Pt950-P',],
            'K18/750×Pt950コンビ-5:5' => ['url' => '','name' => '',],
            'K18/750-Pt/Pmコンビ' => ['url' => '','name' => 'プラチナ',],
            'K18RG/750-Rose Gold' => ['url' => '/gold/k18/','name' => '18金,K18',],
            'K18BG/750-Brown Gold' => ['url' => '/gold/k18/','name' => '18金,K18',],
            'K18GG/750-Green Gold' => ['url' => '/gold/k18/','name' => '18金,K18',],
            'K18SG/750 サクラゴールド' => ['url' => '/gold/k18/','name' => '18金,K18',],
            'Pt850' => ['url' => '/platinum/pt850/','name' => 'Pt850',],
            'Pm850' => ['url' => '/platinum/pt850/','name' => 'Pt850',],
            'Pt/Pm' => ['url' => '/platinum/pt850/','name' => 'プラチナ',],
            'Pt950-Pt850' => ['url' => '/platinum/pt850/','name' => 'Pt850',],
            'K18Y+P/750,2カラー' => ['url' => '/gold/k18/','name' => '18金,K18',],
            'K18Y+W/750,2カラー' => ['url' => '/gold/k18/','name' => '18金,K18',],
            'K18P+W/750,2カラー' => ['url' => '/gold/k18/','name' => '18金,K18',],
            'K14/585,3カラー' => ['url' => '/gold/k14/','name' => '14金,K14',],
            'K14/585,2カラー' => ['url' => '/gold/k14/','name' => '14金,K14',],
            'K10/416,3カラー' => ['url' => '/gold/k10/','name' => '10金,K10',],
            'K10/416,2カラー' => ['url' => '/gold/k10/','name' => '10金,K10',],
            'K18Y+W+P/750,3カラー' => ['url' => '/gold/k18/','name' => '10金,K10',],
            'K18W+P+B/750,3カラー' => ['url' => '/gold/k18/','name' => '10金,K10',],
            'K18W+P+Y+C/750,4カラー' => ['url' => '/gold/k18/','name' => '10金,K10',],
            'Silver/銀製' => ['url' => '/gold/silver/','name' => '銀(シルバー)',],
            'Pt999/995,1000' => ['url' => '/platinum/pt1000/','name' => 'Pt1000,純白金',],
            'Pt999/995,1000板材' => ['url' => '/platinum/pt1000/','name' => 'Pt1000,純白金',],
            'Pt999コイン' => ['url' => '/platinum/pt1000/','name' => 'Pt1000,純白金',],
            'Pt999吹き固め' => ['url' => '/platinum/pt1000/','name' => 'Pt1000,純白金',],
            'K24/999' => ['url' => '/gold/k24/','name' => '24金,K24,純金',],
            'K24/999板材' => ['url' => '/gold/k24/','name' => '24金,K24,純金',],
            'K24コイン' => ['url' => '/gold/k24/','name' => '24金,K24,純金',],
            'K24/吹き固め' => ['url' => '/gold/k24/','name' => '24金,K24,純金',],
            'K18/吹き固め' => ['url' => '/gold/k18/','name' => '18金,K18',],
            'K14/吹き固め' => ['url' => '/gold/k14/','name' => '14金,K14',],
            'K10/吹き固め' => ['url' => '/gold/k10/','name' => '10金,K10',],
            'K23/958' => ['url' => '/gold/k22/','name' => '23金,K23',],
            'K22/916' => ['url' => '/gold/k22/','name' => '22金,K22',],
            'K21.6/900' => ['url' => '/gold/k22/','name' => '21.6金,K21.6',],
            'K20/835' => ['url' => '/gold/k22/','name' => '20金,K20',],
            'K17/700' => ['url' => '','name' => '17金,K17',],
            'K18/750×SSコンビ' => ['url' => '','name' => '',],
            'K18/750×K14/585コンビ' => ['url' => '/gold/k14/','name' => '14金,K14',],
            'K18/750×K10/416コンビ' => ['url' => '/gold/k10/','name' => '10金,K10',],
            'K22/916×Pt950' => ['url' => '/gold/k22/','name' => '',],
            'K18/750×Pt999(1000)' => ['url' => '/platinum/pt1000/','name' => 'Pt1000,純白金',],
            'K14/585×Pt900' => ['url' => '/platinum/pt900/','name' => 'Pt900',],
            'K14/585×Pt950' => ['url' => '/platinum/pt950/','name' => 'Pt950',],
            'K14/585×Pt999(1000)' => ['url' => '/platinum/pt1000/','name' => 'Pt1000,純白金',],
            'Pt999×Pt850コンビ' => ['url' => '/platinum/pt850/','name' => 'Pt850',],
            'Sv925×K18/750コンビ' => ['url' => '/gold/silver/','name' => 'Sv925,スターリングシルバー',],
            'Sv925×K14/585コンビ' => ['url' => '/gold/silver/','name' => 'Sv925,スターリングシルバー',],
            'Sv925×K10/416コンビ' => ['url' => '/gold/silver/','name' => 'Sv925,スターリングシルバー',],
            'Sv925×ルベドメタル' => ['url' => '/gold/silver/','name' => 'Sv925,スターリングシルバー',],
            'ルベドメタル' => ['url' => '','name' => '',],
            'K12/500' => ['url' => '','name' => '12金,K12',],
            'K9/375' => ['url' => '','name' => '9金,K9',],
            'K8/333' => ['url' => '','name' => '8金,K8',],
            'K5/210' => ['url' => '','name' => '5金,K5',],
            'K18+K14' => ['url' => '/gold/k14/','name' => '14金,K14',],
            'Sv1000/純銀' => ['url' => '/gold/silver/','name' => 'Sv1000,純銀',],
            'K14コンビ×Pt900' => ['url' => '/platinum/pt900/','name' => 'Pt900',],
            'K14コンビ×Pt850' => ['url' => '/platinum/pt850/','name' => 'Pt850',],
            'Sv900(Coin)' => ['url' => '/gold/silver/','name' => 'Sv900,コインシルバー',],
            'Pd999板' => ['url' => '/palladium/','name' => 'Pd1000,純パラジウム',],
            'K14コンビ×Pt950' => ['url' => '/platinum/pt950/','name' => 'Pt950',],
            'Pd999スクラップ' => ['url' => '/palladium/','name' => 'Pd1000,純パラジウム',],
            'Pd999コイン' => ['url' => '/palladium/','name' => 'Pd1000,純パラジウム',],
            'Pd999/吹き固め' => ['url' => '/palladium/','name' => 'Pd1000,純パラジウム',],
            'Pd950' => ['url' => '/palladium/','name' => 'Pd950',],
            'Pd900' => ['url' => '/palladium/','name' => 'Pd900',],
            'Pd500' => ['url' => '/palladium/','name' => 'Pd500',],
            'Au12/Pd20歯科材' => ['url' => '/palladium/','name' => 'Au12Pd20,歯科材',],
            'AuIG(500g)' => ['url' => '/ingot/','name' => '金のインゴット',],
            'AuIG(100g)' => ['url' => '/ingot/','name' => '金のインゴット',],
            'AuIG(100g未満)' => ['url' => '/gold/k24/','name' => '24金,K24,純金',],
            'AuIG(GDB(国外含む))' => ['url' => '/gold/k24/','name' => '24金,K24,純金',],
            'Pt IG(500g)' => ['url' => '/ingot/','name' => 'プラチナのインゴット',],
            'Pt IG(100g)' => ['url' => '/ingot/','name' => 'プラチナのインゴット',],
            'Pt IG(100g未満)' => ['url' => '/platinum/pt1000/','name' => 'Pt1000,純白金',],
            'PdIG(500g)' => ['url' => '/palladium/','name' => 'パラジウムのインゴット',],
            'Diamond' => ['url' => '/diamond/','name' => 'ダイヤモンド',],
            'PdIG(100g)' => ['url' => '/palladium/','name' => 'パラジウムのインゴット',],
            'PdIG(100g未満)' => ['url' => '/palladium/','name' => 'Pd1000,純パラジウム',],
            'Diamond Melee' => ['url' => '/diamond/','name' => 'ダイヤモンド',],
            '中石' => ['url' => '','name' => '',],
            '脇石' => ['url' => '','name' => '',],
            'Loose/ルース' => ['url' => '','name' => '',],
            'その他スチール' => ['url' => '','name' => '',],
            'SS/stainless steal' => ['url' => '','name' => '',],
            '合金/メッキ' => ['url' => '','name' => '',],
            'その他' => ['url' => '','name' => '',],
            'titanium' => ['url' => '','name' => '',],
        ];
        $colorstone_array = [
            '1' => [
                'url' => '/diamond',
                'name' => 'ダイヤモンド',
            ],
            '3' => [
                'url' => '/colorstone/iolite',
                'name' => 'アイオライト',
            ],
            '4' => [
                'url' => '/colorstone/awinite',
                'name' => 'アウイナイト',
            ],
            '5' => [
                'url' => '/colorstone/aquamarinemorganite',
                'name' => 'アクアマリン（アクワマリン）',
            ],
            '6' => [
                'url' => '',
                'name' => 'アパタイト',
            ],
            '7' => [
                'url' => '/colorstone/amethystcitrine',
                'name' => 'アメシスト（アメジスト）',
            ],
            '8' => [
                'url' => '',
                'name' => 'アメトリン',
            ],
            '9' => [
                'url' => '/colorstone/alexandrite',
                'name' => 'アレキサンドライト',
            ],
            '10' => [
                'url' => '/colorstone/andesin',
                'name' => 'アンデシン',
            ],
            '11' => [
                'url' => '/colorstone/ammolite',
                'name' => 'アンモライト',
            ],
            '12' => [
                'url' => '',
                'name' => 'インカローズ（ロードクロサイト）',
            ],
            '13' => [
                'url' => '/colorstone/emerald',
                'name' => 'エメラルド',
            ],
            '14' => [
                'url' => '/colorstone/opal',
                'name' => 'オパール',
            ],
            '15' => [
                'url' => '/colorstone/garnet',
                'name' => 'ガーネット',
            ],
            '16' => [
                'url' => '/colorstone/cameo',
                'name' => 'カメオ（シェル・ストーン）',
            ],
            '17' => [
                'url' => '/colorstone/chalcedony',
                'name' => 'カルセドニー',
            ],
            '18' => [
                'url' => '',
                'name' => 'キュービックジルコニア',
            ],
            '19' => [
                'url' => '',
                'name' => 'クォーツ(水晶)',
            ],
            '20' => [
                'url' => '/colorstone/chrysoberyl',
                'name' => 'クリソベリル',
            ],
            '21' => [
                'url' => '/colorstone/kunzite',
                'name' => 'クンツァイト',
            ],
            '22' => [
                'url' => '/colorstone/grandidierite',
                'name' => 'グランディディエライト',
            ],
            '23' => [
                'url' => '/colorstone/sapphire',
                'name' => 'サファイア',
            ],
            '24' => [
                'url' => '/colorstone/amethystcitrine',
                'name' => 'シトリン',
            ],
            '25' => [
                'url' => '/colorstone/sillimanite',
                'name' => 'シリマナイト',
            ],
            '26' => [
                'url' => '',
                'name' => 'ジルコン',
            ],
            '27' => [
                'url' => '/colorstone/spinel',
                'name' => 'スピネル',
            ],
            '28' => [
                'url' => '/colorstone/sphene',
                'name' => 'スフェーン',
            ],
            '29' => [
                'url' => '',
                'name' => 'スミソナイト',
            ],
            '30' => [
                'url' => '/colorstone/turquoise',
                'name' => 'ターコイズ（トルコ石）',
            ],
            '31' => [
                'url' => '',
                'name' => 'ダイオプサイト',
            ],
            '32' => [
                'url' => '/colorstone/tanzanite',
                'name' => 'タンザナイト',
            ],
            '33' => [
                'url' => '/colorstone/topaz',
                'name' => 'トパーズ',
            ],
            '34' => [
                'url' => '/colorstone/tourmaline',
                'name' => 'トルマリン',
            ],
            '35' => [
                'url' => '/colorstone/paraibatourmaline',
                'name' => 'パライバトルマリン',
            ],
            '36' => [
                'url' => '',
                'name' => 'フルオライト',
            ],
            '37' => [
                'url' => '/colorstone/bluetopaz',
                'name' => 'ブルートパーズ',
            ],
            '38' => [
                'url' => '',
                'name' => 'べっ甲',
            ],
            '39' => [
                'url' => '',
                'name' => 'ペツォッタイト',
            ],
            '40' => [
                'url' => '/colorstone/peridot',
                'name' => 'ペリドット',
            ],
            '41' => [
                'url' => '',
                'name' => 'ベリル',
            ],
            '42' => [
                'url' => '',
                'name' => 'マスグラバイト',
            ],
            '43' => [
                'url' => '',
                'name' => 'マラカイト',
            ],
            '44' => [
                'url' => '',
                'name' => 'マルチストーン',
            ],
            '45' => [
                'url' => '',
                'name' => 'ムーンストーン',
            ],
            '46' => [
                'url' => '',
                'name' => 'モルガナイト',
            ],
            '47' => [
                'url' => '',
                'name' => 'ラピスラズリ',
            ],
            '48' => [
                'url' => '/colorstone/ruby',
                'name' => 'ルビー',
            ],
            '49' => [
                'url' => '',
                'name' => 'ルベライト',
            ],
            '50' => [
                'url' => '/colorstone/coral',
                'name' => '珊瑚（コーラル）',
            ],
            '51' => [
                'url' => '/colorstone/pearl',
                'name' => '真珠（パール）',
            ],
            '52' => [
                'url' => '',
                'name' => '琥珀（アンバー）',
            ],
            '53' => [
                'url' => '/colorstone/jade',
                'name' => '翡翠（ジェイド）',
            ],
            '54' => [
                'url' => '',
                'name' => '象牙（アイボリー）',
            ],
            '55' => [
                'url' => '',
                'name' => 'その他宝石',
            ],
        ];

        $dai_category_array = [
            '' => [
                'url' => '',
                'name' => 'バッグ',
            ],
            '' => [
                'url' => '',
                'name' => '毛皮',
            ],
            '' => [
                'url' => '',
                'name' => '酒',
            ],
            '' => [
                'url' => '',
                'name' => '靴',
            ],
            '' => [
                'url' => '',
                'name' => 'ワンピース',
            ],
            '' => [
                'url' => '',
                'name' => 'ベルト',
            ],
            '' => [
                'url' => '',
                'name' => '帽子',
            ],
            '' => [
                'url' => '',
                'name' => '手帳',
            ],
            '' => [
                'url' => '',
                'name' => '楽器',
            ],
            '' => [
                'url' => '',
                'name' => '着物',
            ],          
        ];

    return [
      'gold_property_array' => $gold_property_array,
      'colorstone_array' => $colorstone_array,
      'dai_category_array' => $dai_category_array,
    ];
  }



  public function create_pankuzu_jewelry($pankuzu, $rs_purchased_result)
  {
    $colorstone_array = $this->array_rule()['colorstone_array'];
    if (isset($colorstone_array[$rs_purchased_result->colorstone_id])) {
      $pankuzu[] = [
        'url' => '/colorstone/',
        'name' => '宝石・ジュエリー買取',
      ];
      $pankuzu[] = [
        'url' => $colorstone_array[$rs_purchased_result->colorstone_id]['url'] ?: '/colorstone/othercolorstone/',
        'name' => ($colorstone_array[$rs_purchased_result->colorstone_id]['name'] ?: 'その他宝石') . '買取',
      ];
    }
    return $pankuzu;
  }

    public function create_pankuzu_brand($pankuzu, $rs_purchased_result)
    {
        if ((!empty($rs_purchased_result->product_num)) && ($rs_purchased_result->product_num != 631)) {
        $mst_brand = Mst_brand::find($rs_purchased_result->product_num);
        if ($mst_brand === null) {
            return $pankuzu;
        }
        $mst_brand_name_array = explode('/', $mst_brand->name);
        $mst_brand_name = trim($mst_brand_name_array[0]);
        $pankuzu[] = [
            'url' => '/brand/',
            'name' => 'ブランド買取',
        ];
        $pankuzu[] = [
            'url' => 'https://brandkaimasu.com/brandlist_name/?brand_id=' . $rs_purchased_result->product_num,
            'name' => $mst_brand_name . '買取',
        ];
        }
            return $pankuzu;
        }
    public function create_pankuzu_gold($pankuzu, $rs_purchased_result)
        {
            $gold_property_array = $this->array_rule()['gold_property_array'];
            if (isset($gold_property_array[$rs_purchased_result->gold_property])) {
                $pankuzu[] = [
                    'url' => '/bullion/',
                    'name' => '地金・貴金属買取',
                ];
                // 両方入ってる時のみ格納
                if (
                    !empty($gold_property_array[$rs_purchased_result->gold_property]['url'])
                    &&
                    !empty($gold_property_array[$rs_purchased_result->gold_property]['name'])
                ) {
                    $pankuzu[] = [
                        'url' =>  $gold_property_array[$rs_purchased_result->gold_property]['url'] ?: '/gold/',
                        'name' => $gold_property_array[$rs_purchased_result->gold_property]['name'] ?: '金買取'
                    ];
                } else {
                    $pankuzu[] = [
                        'url' => '/gold/',
                        'name' => '金買取'
                    ];
                }
            }
            return $pankuzu;
        }

    public function create_pankuzu_dai_category($pankuzu, $rs_purchased_result)
    {
        $dai_category_array = $this->array_rule()['dai_category_array'];
        if (isset($dai_category_array[$rs_purchased_result->category_id])) {
        $pankuzu[0] = [
            'url' => $dai_category_array[$rs_purchased_result->category_id]['url'],
            'name' => $dai_category_array[$rs_purchased_result->category_id]['name'],
        ];
        }
        return $pankuzu;
    }



    public function create_pankuzu_array(rs_purchased_result $rs_purchased_result): array
    {
        $pankuzu = [];

        // 第１・２階層
        // 宝石条件
        if (empty($pankuzu[0])) {
            $pankuzu = $this->create_pankuzu_jewelry($pankuzu, $rs_purchased_result);
        }
        // ブランド条件
        if (empty($pankuzu[0])) {
            $pankuzu = $this->create_pankuzu_brand($pankuzu, $rs_purchased_result);
        }
        // 貴金属条件
        if (empty($pankuzu[0])) {
            $pankuzu = $this->create_pankuzu_gold($pankuzu, $rs_purchased_result);
        }
        // 時計だったら１階層目を時計にする
        if ($rs_purchased_result->purchased_category === 'W') {
        $pankuzu[0] = [
            'url' => '/watch/', // todo ???
            'name' => '時計買取',
        ];
        }
        // 大カテが該当したら１階層目だけ変更する
        $pankuzu = $this->create_pankuzu_dai_category($pankuzu, $rs_purchased_result);
        if (empty($pankuzu[0])) {
            $pankuzu[0] = [
                'url' => '/result/',
                'name' => '地金･宝飾品･ブランド品の買取実績',
            ];
            return $pankuzu;
        }

        $pankuzu[2] = [
        'url' => '/result/',
        'name' => '地金･宝飾品･ブランド品の買取実績',
        ];

        $pankuzu[3] = [
            'url' => '',
            'name' => $this->create_current_result_detail_title($rs_purchased_result) ,
        ];

        return $pankuzu;
    }

    public function create_current_result_detail_title($rs_purchased_result)
    {
        $mst_brand_name = '';
        if ($rs_purchased_result->product_num != 631) {
        $mst_brand = Mst_brand::find($rs_purchased_result->product_num);
        if ($mst_brand !== null) {
            $mst_brand_name_array = explode('/', $mst_brand->name);
            $mst_brand_name = trim($mst_brand_name_array[0]);
        }
        }

        $title = '';
        if (!empty($rs_purchased_result->colorstone_id)) {
        // 宝石
        // ブランド＋宝石＋品位＋形状の買取実積
        $title .= $mst_brand_name;
        $mst_colorstone = Mst_colorstone::find($rs_purchased_result->colorstone_id);
        if ($mst_colorstone !== null) {
            if (!empty($title)) {
            $title .= ' ';
            }
            $title .= $mst_colorstone->name;
        }
        if ($rs_purchased_result->gold_property !== null) {
            if (!empty($title)) {
            $title .= ' ';
            }
            $title .= $rs_purchased_result->gold_property;
        }
        if ($rs_purchased_result->product_style !== null) {
            if (!empty($title)) {
            $title .= ' ';
            }
            $title .= $rs_purchased_result->product_style;
        }
        } elseif ($rs_purchased_result->purchase_category === 'K') {
        // 地金
        // 品位＋形状の買取実積
        if ($rs_purchased_result->gold_property !== null) {
            if (!empty($title)) {
            $title .= ' ';
            }
            $title .= $rs_purchased_result->gold_property;
        }
        if ($rs_purchased_result->product_style !== null) {
            if (!empty($title)) {
            $title .= ' ';
            }
            $title .= $rs_purchased_result->product_style;
        }
        } else {
        // 形状
        // ブランド＋形状の買取実積
        $title .= $mst_brand_name;
        if ($rs_purchased_result->product_style !== null) {
            if (!empty($title)) {
            $title .= ' ';
            }
            $title .= $rs_purchased_result->product_style;
        }
        }
        if (!empty($title)) {
            $title .= 'の買取実績';
        }
        return $title;
    }
}







