<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Kinkai\tb_rapaport;
use DB;


class TestController extends Controller
{
    public function dummmyData($date)
    {
        $ret = new \stdClass;
        $ret->date = date("Y-m", strtotime($date));
        $ret->high = random_int(5000, 7000);
        $ret->low = random_int(5000, 7000);
        $ret->open = random_int(5000, 7000);
        $ret->close = random_int(5000, 7000);
        return $ret;
    }


    public function index()
    {
        // $tb_price = DB::table("kinkai.tb_price")

        $golddata = array(
            $this->dummmyData("2018-01-01"),
            $this->dummmyData("2018-02-01"),
            $this->dummmyData("2018-03-01"),
            $this->dummmyData("2018-04-01"),
            $this->dummmyData("2018-05-01"),
            $this->dummmyData("2018-06-01"),
            $this->dummmyData("2018-07-01"),
            $this->dummmyData("2018-08-01"),
            $this->dummmyData("2018-09-01"),
            $this->dummmyData("2018-10-01"),
            $this->dummmyData("2018-11-01"),
            $this->dummmyData("2018-12-01"),

            $this->dummmyData("2019-01-01"),
            $this->dummmyData("2019-02-01"),
            $this->dummmyData("2019-03-01"),
            $this->dummmyData("2019-04-01"),
            $this->dummmyData("2019-05-01"),
            $this->dummmyData("2019-06-01"),
            $this->dummmyData("2019-07-01"),
            $this->dummmyData("2019-08-01"),
            $this->dummmyData("2019-09-01"),
            $this->dummmyData("2019-10-01"),
            $this->dummmyData("2019-11-01"),
            $this->dummmyData("2019-12-01"),

            $this->dummmyData("2020-01-01"),
            $this->dummmyData("2020-02-01"),
            $this->dummmyData("2020-03-01"),
            $this->dummmyData("2020-04-01"),
            $this->dummmyData("2020-05-01"),
            $this->dummmyData("2020-06-01"),
            $this->dummmyData("2020-07-01"),
            $this->dummmyData("2020-08-01"),
            $this->dummmyData("2020-09-01"),
            $this->dummmyData("2020-10-01"),
            $this->dummmyData("2020-11-01"),
            $this->dummmyData("2020-12-01"),
        );



        $gold_ticks = array();
        $loop = 0;
        foreach($golddata as $item){
            if($loop % 4 == 0){
                $gold_ticks[] = '"' . $item->date . '"';
            }
            $loop++;
        }
        // var_dump($gold_ticks);
        // unset($gold_ticks[count($gold_ticks) - 1]);
        // var_dump($gold_ticks);
        $gold_ticks_text = implode(",", $gold_ticks);

        $array = array(
            "golddata" => $golddata,
            "gold_ticks_text" => $gold_ticks_text,
        );

        return view("make_candle_graph.index", $array);
    }

    public function test_func(){
       $tb_rapaport = tb_rapaport::first();
       phpinfo();
       var_dump($tb_rapaport);
       return 'aaaaa';
    }




    public function urltest()
    {
        $urls = [];
        $urls[] = 'https://kinkaimasu.jp/kiyaku_old';
        $urls[] = 'https://kinkaimasu.jp/system_bk';
        $urls[] = 'https://kinkaimasu.jp/gold-k18';
        $urls[] = 'https://kinkaimasu.jp/kit_bk';
        $urls[] = 'https://kinkaimasu.jp/wp-wiki';
        $urls[] = 'https://kinkaimasu.jp/flea_market_apps_comparison_bk';
        $urls[] = 'https://kinkaimasu.jp/buyer';
        $urls[] = 'https://kinkaimasu.jp/reservation_shop_purchase';
        $urls[] = 'https://kinkaimasu.jp/questionnaire';
        $urls[] = 'https://kinkaimasu.jp/interview/vol_01';
        $urls[] = 'https://kinkaimasu.jp/campaign';
        $urls[] = 'https://kinkaimasu.jp/interview/vol_02';
        $urls[] = 'https://kinkaimasu.jp/faq';
        $urls[] = 'https://kinkaimasu.jp/%e3%83%81%e3%83%a3%e3%83%83%e3%83%88%e3%81%a7%e8%a7%a3%e6%b1%ba%ef%bc%81 %e6%9f%bb%e5%ae%9a%e3%82%82%e8%b3%aa%e5%95%8f%e3%82%82%e3%81%99%e3%81%90%e3%81%ab%e5%af%be%e5%bf%9c';
        $urls[] = 'https://kinkaimasu.jp/campaign/month';
        $urls[] = 'https://kinkaimasu.jp/re-blocks';
        $urls[] = 'https://kinkaimasu.jp/sunday';
        $urls[] = 'https://kinkaimasu.jp/%e3%82%a2%e3%83%ac%e3%82%ad%e3%82%b5%e3%83%b3%e3%83%89%e3%83%a9%e3%82%a4%e3%83%88%e3%81%ae%e8%b2%b7%e5%8f%96,%e7%9b%b8%e5%a0%b4 | %e3%83%aa%e3%83%95%e3%82%a1%e3%82%b9%e3%82%bf(%e6%97%a7:%e3%83%aa';
        $urls[] = 'https://kinkaimasu.jp/media/2018/0904';
        $urls[] = 'https://kinkaimasu.jp/%e5%85%b1%e6%9c%89%e3%82%bd%e3%83%bc%e3%82%b9%e3%82%b3%e3%83%bc%e3%83%89';
        $urls[] = 'https://kinkaimasu.jp/media/2016/0728';
        $urls[] = 'https://kinkaimasu.jp/media/2016/0919';
        $urls[] = 'https://kinkaimasu.jp/media/2016/0717';
        $urls[] = 'https://kinkaimasu.jp/media/2016/1013';
        $urls[] = 'https://kinkaimasu.jp/media/2016/1218';
        $urls[] = 'https://kinkaimasu.jp/media/2017/0425';
        $urls[] = 'https://kinkaimasu.jp/media/2017/0602';
        $urls[] = 'https://kinkaimasu.jp/media/2017/0929';
        $urls[] = 'https://kinkaimasu.jp/media/2017/1226';
        $urls[] = 'https://kinkaimasu.jp/media/2018/0115';
        $urls[] = 'https://kinkaimasu.jp/media/2018/0308';
        $urls[] = 'https://kinkaimasu.jp/media/2018/0410';
        $urls[] = 'https://kinkaimasu.jp/media/2018/0712';
        $urls[] = 'https://kinkaimasu.jp/media';
        $urls[] = 'https://kinkaimasu.jp/sns_list';
        $urls[] = 'https://kinkaimasu.jp/media/2018/0302';
        $urls[] = 'https://kinkaimasu.jp/platinum';
        $urls[] = 'https://kinkaimasu.jp/test';
        $urls[] = 'https://kinkaimasu.jp/reuse_gold';
        $urls[] = 'https://kinkaimasu.jp/reuse_buyer';
        $urls[] = 'https://kinkaimasu.jp/';
        $urls[] = 'https://kinkaimasu.jp/reuse_colorstoneList';
        $urls[] = 'https://kinkaimasu.jp/reuse_brandList';
        $urls[] = 'https://kinkaimasu.jp/media/2020/0819';
        $urls[] = 'https://kinkaimasu.jp/reuse_accessories';
        $urls[] = 'https://kinkaimasu.jp/reuse_brand_evaluation';
        $urls[] = 'https://kinkaimasu.jp/reuse_deal_brand_list';
        $urls[] = 'https://kinkaimasu.jp/reuse_gold_coin';
        $urls[] = 'https://kinkaimasu.jp/reuse_inqury_device';
        $urls[] = 'https://kinkaimasu.jp/reuse_discrimination_note';
        $urls[] = 'https://kinkaimasu.jp/reuse_kaitori_pr';
        $urls[] = 'https://kinkaimasu.jp/reuse_conditions';
        $urls[] = 'https://kinkaimasu.jp/my_home_satei_back';
        $urls[] = 'https://kinkaimasu.jp/reuse_six_reason';
        $urls[] = 'https://kinkaimasu.jp/platinum/pt900';
        $urls[] = 'https://kinkaimasu.jp/platinum/pt950';
        $urls[] = 'https://kinkaimasu.jp/platinum/pm';
        $urls[] = 'https://kinkaimasu.jp/platinum/coin';
        $urls[] = 'https://kinkaimasu.jp/platinum/ingot';
        $urls[] = 'https://kinkaimasu.jp/platinum/ring';
        $urls[] = 'https://kinkaimasu.jp/platinum/earring';
        $urls[] = 'https://kinkaimasu.jp/platinum/necklace';
        $urls[] = 'https://kinkaimasu.jp/platinum/bracelet';
        $urls[] = 'https://kinkaimasu.jp/platinum/brooch';
        $urls[] = 'https://kinkaimasu.jp/platinum/jewelry';
        $urls[] = 'https://kinkaimasu.jp/platinum/list';
        $urls[] = 'https://kinkaimasu.jp/platinum/pt850';
        $urls[] = 'https://kinkaimasu.jp/platinum/pt1000';
        $urls[] = 'https://kinkaimasu.jp/scrap_banner';
        $urls[] = 'https://kinkaimasu.jp/colorstone/emerald/cabochon';
        $urls[] = 'https://kinkaimasu.jp/colorstone/emerald/ring';
        $urls[] = 'https://kinkaimasu.jp/gold/k24';
        $urls[] = 'https://kinkaimasu.jp/colorstone/jade';
        $urls[] = 'https://kinkaimasu.jp/colorstone/tourmaline';
        $urls[] = 'https://kinkaimasu.jp/colorstone/chrysoberyl';
        $urls[] = 'https://kinkaimasu.jp/colorstone/garnet';
        $urls[] = 'https://kinkaimasu.jp/colorstone/topaz';
        $urls[] = 'https://kinkaimasu.jp/colorstone/pearl';
        $urls[] = 'https://kinkaimasu.jp/colorstone/pearl/trivia';
        $urls[] = 'https://kinkaimasu.jp/colorstone/aquamarinemorganite';
        $urls[] = 'https://kinkaimasu.jp/colorstone/peridot';
        $urls[] = 'https://kinkaimasu.jp/gold/k22';
        $urls[] = 'https://kinkaimasu.jp/gold/k14';
        $urls[] = 'https://kinkaimasu.jp/gold/k10';
        $urls[] = 'https://kinkaimasu.jp/gold/ring';
        $urls[] = 'https://kinkaimasu.jp/gold/earring';
        $urls[] = 'https://kinkaimasu.jp/gold/necklace';
        $urls[] = 'https://kinkaimasu.jp/gold/bracelet';
        $urls[] = 'https://kinkaimasu.jp/gold/brooch';
        $urls[] = 'https://kinkaimasu.jp/gold/jewelry';
        $urls[] = 'https://kinkaimasu.jp/gold/kikinzoku';
        $urls[] = 'https://kinkaimasu.jp/colorstone/ammolite';
        $urls[] = 'https://kinkaimasu.jp/gold/pink-gold';
        $urls[] = 'https://kinkaimasu.jp/gold/white-gold';
        $urls[] = 'https://kinkaimasu.jp/colorstone/spinel';
        $urls[] = 'https://kinkaimasu.jp/gold/yellow-gold';
        $urls[] = 'https://kinkaimasu.jp/colorstone/tanzanite';
        $urls[] = 'https://kinkaimasu.jp/colorstone/kunzite';
        $urls[] = 'https://kinkaimasu.jp/colorstone/cameo';
        $urls[] = 'https://kinkaimasu.jp/colorstone/chalcedony';
        $urls[] = 'https://kinkaimasu.jp/gold/silver';
        $urls[] = 'https://kinkaimasu.jp/colorstone/sillimanite';
        $urls[] = 'https://kinkaimasu.jp/colorstone/turquoise';
        $urls[] = 'https://kinkaimasu.jp/colorstone/othercolorstone';
        $urls[] = 'https://kinkaimasu.jp/colorstone/amethystcitrine';
        $urls[] = 'https://kinkaimasu.jp/colorstone/iolite';
        $urls[] = 'https://kinkaimasu.jp/colorstone/rhodochrosite';
        $urls[] = 'https://kinkaimasu.jp/colorstone/awinite';
        $urls[] = 'https://kinkaimasu.jp/colorstone/papalachiasapphire';
        $urls[] = 'https://kinkaimasu.jp/colorstone/grandidierite';
        $urls[] = 'https://kinkaimasu.jp/colorstone/sphene';
        $urls[] = 'https://kinkaimasu.jp/colorstone/starruby';
        $urls[] = 'https://kinkaimasu.jp/colorstone/starsapphire';
        $urls[] = 'https://kinkaimasu.jp/colorstone/concpearl';
        $urls[] = 'https://kinkaimasu.jp/colorstone/imperialtopaz';
        $urls[] = 'https://kinkaimasu.jp/colorstone/blackopal';
        $urls[] = 'https://kinkaimasu.jp/colorstone/grossergarnet';
        $urls[] = 'https://kinkaimasu.jp/colorstone/andesin';
        $urls[] = 'https://kinkaimasu.jp/colorstone/lightopal';
        $urls[] = 'https://kinkaimasu.jp/colorstone/fireopal';
        $urls[] = 'https://kinkaimasu.jp/colorstone/bluetopaz';
        $urls[] = 'https://kinkaimasu.jp/colorstone/whiteopal';
        $urls[] = 'https://kinkaimasu.jp/colorstone/opal';
        $urls[] = 'https://kinkaimasu.jp/colorstone/opal/sup1';
        $urls[] = 'https://kinkaimasu.jp/colorstone/opal/sup2';
        $urls[] = 'https://kinkaimasu.jp/colorstone/coral';
        $urls[] = 'https://kinkaimasu.jp/colorstone/coral/sup1';
        $urls[] = 'https://kinkaimasu.jp/colorstone/coral/sup2';
        $urls[] = 'https://kinkaimasu.jp/old_result';
        $urls[] = 'https://kinkaimasu.jp/happydiamond';
        $urls[] = 'https://kinkaimasu.jp/brand/tiffany';
        $urls[] = 'https://kinkaimasu.jp/old_result_detail';
        $urls[] = 'https://kinkaimasu.jp/line/method';
        $urls[] = 'https://kinkaimasu.jp/brand/cartier';
        $urls[] = 'https://kinkaimasu.jp/brand/agate';
        $urls[] = 'https://kinkaimasu.jp/brand/bvlgari';
        $urls[] = 'https://kinkaimasu.jp/brand/ahkah';
        $urls[] = 'https://kinkaimasu.jp/brand/harrywinston';
        $urls[] = 'https://kinkaimasu.jp/brand/vancleefarpels';
        $urls[] = 'https://kinkaimasu.jp/brand/boucheron';
        $urls[] = 'https://kinkaimasu.jp/brand/chaumet';
        $urls[] = 'https://kinkaimasu.jp/brand/damiani';
        $urls[] = 'https://kinkaimasu.jp/brand/mellerio_dits_meller';
        $urls[] = 'https://kinkaimasu.jp/brand/vendome_aoyama';
        $urls[] = 'https://kinkaimasu.jp/brand/royal_asscher';
        $urls[] = 'https://kinkaimasu.jp/brand/kashikey';
        $urls[] = 'https://kinkaimasu.jp/brand/star_jewelry';
        $urls[] = 'https://kinkaimasu.jp/brand/4c';
        $urls[] = 'https://kinkaimasu.jp/brand/mauboussin';
        $urls[] = 'https://kinkaimasu.jp/brand/carreraycarrera';
        $urls[] = 'https://kinkaimasu.jp/booking_thanks';
        $urls[] = 'https://kinkaimasu.jp/gold/k18';
        $urls[] = 'https://kinkaimasu.jp/colorstone/_alexandrite';
        $urls[] = 'https://kinkaimasu.jp/colorstone/ruby';
        $urls[] = 'https://kinkaimasu.jp/louisvuitton/bag_bk';
        $urls[] = 'https://kinkaimasu.jp/colorstone/emerald/sup1';
        $urls[] = 'https://kinkaimasu.jp/colorstone/emerald/sup2';
        $urls[] = 'https://kinkaimasu.jp/colorstone/sapphire';
        $urls[] = 'https://kinkaimasu.jp/colorstone/paraibatourmaline/_bk';
        $urls[] = 'https://kinkaimasu.jp/brand_page_settings_chanel';
        $urls[] = 'https://kinkaimasu.jp/gold/';
        $urls[] = 'https://kinkaimasu.jp/colorstone/_marriageengagering';
        $urls[] = 'https://kinkaimasu.jp/colorstone/sapphire/sup1';
        $urls[] = 'https://kinkaimasu.jp/colorstone/ruby/sup1';
        $urls[] = 'https://kinkaimasu.jp/colorstone/sapphire/sup2';
        $urls[] = 'https://kinkaimasu.jp/colorstone/ruby/sup2';
        $urls[] = 'https://kinkaimasu.jp/colorstone/marriageengagering/sup1';
        $urls[] = 'https://kinkaimasu.jp/colorstone/marriageengagering/sup2';
        $urls[] = 'https://kinkaimasu.jp/louisvuitton/wallet';
        $urls[] = 'https://kinkaimasu.jp/louisvuitton/goods';
        $urls[] = 'https://kinkaimasu.jp/campaign/month/_test';
        $urls[] = 'https://kinkaimasu.jp/louisvuitton/watch';
        $urls[] = 'https://kinkaimasu.jp/result';
        $urls[] = 'https://kinkaimasu.jp/result_detail';
        $urls[] = 'https://kinkaimasu.jp/24kaitori';
        $urls[] = 'https://kinkaimasu.jp/media/2020/1229';
        $urls[] = 'https://kinkaimasu.jp/%e5%a4%96%e9%83%a8%e5%a7%94%e8%a8%97%e8%80%85%e3%82%a2%e3%83%83%e3%83%97%e3%82%b5%e3%83%b3%e3%83%97%e3%83%ab';
        $urls[] = 'https://kinkaimasu.jp/interview';
        $urls[] = 'https://kinkaimasu.jp/happybrand';
        $urls[] = 'https://kinkaimasu.jp/interview/vol_03';
        $urls[] = 'https://kinkaimasu.jp/interview/vol_04';
        $urls[] = 'https://kinkaimasu.jp/happy_harrywinston';
        $urls[] = 'https://kinkaimasu.jp/louisvuitton/shoes';
        $urls[] = 'https://kinkaimasu.jp/colorstone/paraibatourmaline';
        $urls[] = 'https://kinkaimasu.jp/louisvuitton/clothing';
        $urls[] = 'https://kinkaimasu.jp/louisvuitton/monogram';
        $urls[] = 'https://kinkaimasu.jp/interview/vol_05';
        $urls[] = 'https://kinkaimasu.jp/brandlist_name/test';
        $urls[] = 'https://kinkaimasu.jp/colorstone/alexandrite';
        $urls[] = 'https://kinkaimasu.jp/brandlist_name';
        $urls[] = 'https://kinkaimasu.jp/happy-tiffany';
        $urls[] = 'https://kinkaimasu.jp/louisvuitton/damier';
        $urls[] = 'https://kinkaimasu.jp/chanel/boychanel';
        $urls[] = 'https://kinkaimasu.jp/happy-cartier';
        $urls[] = 'https://kinkaimasu.jp/brandlist_name %e3%83%b4%e3%82%a3%e3%83%88%e3%83%b3%e3%82%b3%e3%83%b3%e3%83%86%e3%83%b3%e3%83%84%ef%bc%91';
        $urls[] = 'https://kinkaimasu.jp/brandlist_name %e3%83%b4%e3%82%a3%e3%83%88%e3%83%b3%e3%82%b3%e3%83%b3%e3%83%86%e3%83%b3%e3%83%84%ef%bc%92';
        $urls[] = 'https://kinkaimasu.jp/brandlist_name %e3%83%b4%e3%82%a3%e3%83%88%e3%83%b3hero_area';
        $urls[] = 'https://kinkaimasu.jp/louisvuitton/taiga';
        $urls[] = 'https://kinkaimasu.jp/brandlist_name %e3%83%80%e3%82%a4%e3%83%a4%e3%82%b3%e3%83%b3%e3%83%86%e3%83%b3%e3%83%84%ef%bc%91';
        $urls[] = 'https://kinkaimasu.jp/brandlist_name %e6%9c%8d%e9%a3%be%e3%82%b3%e3%83%b3%e3%83%86%e3%83%b3%e3%83%84%ef%bc%91';
        $urls[] = 'https://kinkaimasu.jp/brandlist_name %e5%ae%9d%e9%a3%be%e3%82%b3%e3%83%b3%e3%83%86%e3%83%b3%e3%83%84%ef%bc%91';
        $urls[] = 'https://kinkaimasu.jp/brandlist_name %e6%99%82%e8%a8%88%e3%82%b3%e3%83%b3%e3%83%86%e3%83%b3%e3%83%84%ef%bc%91';
        $urls[] = 'https://kinkaimasu.jp/brandlist_name %e8%b2%b4%e9%87%91%e5%b1%9e%e3%82%b3%e3%83%b3%e3%83%86%e3%83%b3%e3%83%84%ef%bc%91';
        $urls[] = 'https://kinkaimasu.jp/chanel/accessories';
        $urls[] = 'https://kinkaimasu.jp/interview/vol_08';
        $urls[] = 'https://kinkaimasu.jp/louisvuitton/vernis';
        $urls[] = 'https://kinkaimasu.jp/colorstone/emerald';
        $urls[] = 'https://kinkaimasu.jp/chanel/bag/';
        $urls[] = 'https://kinkaimasu.jp/chanel/clothing';
        $urls[] = 'https://kinkaimasu.jp/louisvuitton/exotic_leather';
        $urls[] = 'https://kinkaimasu.jp/brandlist_name %e3%82%b7%e3%83%a3%e3%83%8d%e3%83%abhero_area';
        $urls[] = 'https://kinkaimasu.jp/brandlist_name %e3%83%96%e3%83%ab%e3%82%ac%e3%83%aahero_area';
        $urls[] = 'https://kinkaimasu.jp/brandlist_name %e3%82%a8%e3%83%ab%e3%83%a1%e3%82%b9hero_area';
        $urls[] = 'https://kinkaimasu.jp/brandlist_name %e3%83%86%e3%82%a3%e3%83%95%e3%82%a1%e3%83%8b%e3%83%bchero_area';
        $urls[] = 'https://kinkaimasu.jp/brandlist_name %e3%83%96%e3%83%ab%e3%82%ac%e3%83%aa%e3%83%95%e3%83%aa%e3%83%bc%e3%82%b3%e3%83%b3%e3%83%86%e3%83%b3%e3%83%84';
        $urls[] = 'https://kinkaimasu.jp/brandlist_name %e3%82%ab%e3%83%ab%e3%83%86%e3%82%a3%e3%82%a8hero_area';
        $urls[] = 'https://kinkaimasu.jp/brandlist_name %e3%83%ad%e3%83%ac%e3%83%83%e3%82%af%e3%82%b9hero_area';
        $urls[] = 'https://kinkaimasu.jp/brandlist_name %e3%82%b0%e3%83%83%e3%83%81hero_area';
        $urls[] = 'https://kinkaimasu.jp/brandlist_name %e3%83%95%e3%82%a7%e3%83%a9%e3%82%ac%e3%83%a2hero_area';
        $urls[] = 'https://kinkaimasu.jp/brandlist_name %e3%83%90%e3%83%ac%e3%83%b3%e3%82%b7%e3%82%a2%e3%82%achero_area';
        $urls[] = 'https://kinkaimasu.jp/brandlist_name %e3%82%ab%e3%82%b7%e3%82%b1%e3%82%a4hero_area';
        $urls[] = 'https://kinkaimasu.jp/brandlist_name %e3%83%9f%e3%83%a5%e3%82%a6%e3%83%9f%e3%83%a5%e3%82%a6hero_area';
        $urls[] = 'https://kinkaimasu.jp/brandlist_name %e3%83%95%e3%82%a7%e3%83%b3%e3%83%87%e3%82%a3hero_area';
        $urls[] = 'https://kinkaimasu.jp/brandlist_name %e3%82%b8%e3%83%a5%e3%82%a8%e3%83%aa%e3%83%bc%e3%83%9e%e3%82%adhero_area';
        $urls[] = 'https://kinkaimasu.jp/brandlist_name %e3%83%a8%e3%83%b3%e3%83%89%e3%82%b7%e3%83%bchero_area';
        $urls[] = 'https://kinkaimasu.jp/brandlist_name %e3%83%b4%e3%82%a1%e3%83%b3%e3%82%af%e3%83%aa%e3%83%bc%e3%83%95%e3%82%a2%e3%83%bc%e3%83%9a%e3%83%abhero_area';
        $urls[] = 'https://kinkaimasu.jp/brandlist_name %e3%83%97%e3%83%a9%e3%83%80hero_area';
        $urls[] = 'https://kinkaimasu.jp/brandlist_name %e3%82%bb%e3%83%aa%e3%83%bc%e3%83%8chero_area';
        $urls[] = 'https://kinkaimasu.jp/brandlist_name %e3%82%b3%e3%83%bc%e3%83%81hero_area';
        $urls[] = 'https://kinkaimasu.jp/brandlist_name %e3%83%96%e3%83%ab%e3%82%ac%e3%83%aa%e3%83%95%e3%83%aa%e3%83%bc%e3%82%b3%e3%83%b3%e3%83%86%e3%83%b3%e3%83%842';
        $urls[] = 'https://kinkaimasu.jp/brandlist_name %e3%83%87%e3%82%a3%e3%82%aa%e3%83%bc%e3%83%abhero_area';
        $urls[] = 'https://kinkaimasu.jp/brandlist_name %e3%82%aa%e3%83%a1%e3%82%achero_area';
        $urls[] = 'https://kinkaimasu.jp/brandlist_name %e3%83%9f%e3%82%ad%e3%83%a2%e3%83%88hero_area';
        $urls[] = 'https://kinkaimasu.jp/brandlist_name %e3%82%b7%e3%83%a7%e3%83%bc%e3%83%a1hero_area';
        $urls[] = 'https://kinkaimasu.jp/brandlist_name %e3%83%9d%e3%83%b3%e3%83%86%e3%83%b4%e3%82%a7%e3%82%ad%e3%82%aahero_area';
        $urls[] = 'https://kinkaimasu.jp/brandlist_name %e3%82%b5%e3%83%b3%e3%83%ad%e3%83%bc%e3%83%a9%e3%83%b3%e3%83%91%e3%83%aahero_area';
        $urls[] = 'https://kinkaimasu.jp/brandlist_name %e3%82%b7%e3%83%a5%e3%83%97%e3%83%aa%e3%83%bc%e3%83%a0hero_area';
        $urls[] = 'https://kinkaimasu.jp/brandlist_name %e3%82%b7%e3%83%a5%e3%83%97%e3%83%aa%e3%83%bc%e3%83%a0%e3%83%95%e3%83%aa%e3%83%bc%e3%82%b3%e3%83%b3%e3%83%86%e3%83%b3%e3%83%84';
        $urls[] = 'https://kinkaimasu.jp/brandlist_name %e3%83%ad%e3%83%ac%e3%83%83%e3%82%af%e3%82%b9%e3%83%95%e3%83%aa%e3%83%bc%e3%82%b3%e3%83%b3%e3%83%86%e3%83%b3%e3%83%84';
        $urls[] = 'https://kinkaimasu.jp/brandlist_name %e3%82%bb%e3%83%aa%e3%83%bc%e3%83%8c%e3%83%95%e3%83%aa%e3%83%bc%e3%82%b3%e3%83%b3%e3%83%86%e3%83%b3%e3%83%84';
        $urls[] = 'https://kinkaimasu.jp/brandlist_name %e3%83%ad%e3%83%ac%e3%83%83%e3%82%af%e3%82%b9%e3%82%b3%e3%83%b3%e3%83%86%e3%83%b3%e3%83%84%ef%bc%92';
        $urls[] = 'https://kinkaimasu.jp/brandlist_name %e3%82%bb%e3%83%aa%e3%83%bc%e3%83%8c%e3%83%95%e3%83%aa%e3%83%bc%e3%82%b3%e3%83%b3%e3%83%86%e3%83%b3%e3%83%842';
        $urls[] = 'https://kinkaimasu.jp/brandlist_name %e3%83%87%e3%82%a3%e3%82%aa%e3%83%bc%e3%83%ab%e3%83%95%e3%83%aa%e3%83%bc%e3%82%b3%e3%83%b3%e3%83%86%e3%83%b3%e3%83%84';
        $urls[] = 'https://kinkaimasu.jp/brandlist_name %e3%82%aa%e3%83%a1%e3%82%ac%e3%83%95%e3%83%aa%e3%83%bc%e3%82%b3%e3%83%b3%e3%83%86%e3%83%b3%e3%83%84';
        $urls[] = 'https://kinkaimasu.jp/brandlist_name %e3%83%87%e3%82%a3%e3%82%aa%e3%83%bc%e3%83%ab%e3%83%95%e3%83%aa%e3%83%bc%e3%82%b3%e3%83%b3%e3%83%86%e3%83%b3%e3%83%84-2';
        $urls[] = 'https://kinkaimasu.jp/brandlist_name %e3%83%90%e3%83%bc%e3%83%90%e3%83%aa%e3%83%bchero_area';
        $urls[] = 'https://kinkaimasu.jp/brandlist_name %e3%83%91%e3%83%86%e3%83%83%e3%82%af%e3%83%95%e3%82%a3%e3%83%aa%e3%83%83%e3%83%97hero_area';
        $urls[] = 'https://kinkaimasu.jp/brandlist_name %e3%83%90%e3%83%bc%e3%83%90%e3%83%aa%e3%83%bc%e3%83%95%e3%83%aa%e3%83%bc%e3%82%b3%e3%83%b3%e3%83%86%e3%83%b3%e3%83%84';
        $urls[] = 'https://kinkaimasu.jp/brandlist_name %e3%83%91%e3%83%86%e3%83%83%e3%82%af%e3%83%95%e3%82%a3%e3%83%aa%e3%83%83%e3%83%97%e3%82%b3%e3%83%b3%e3%83%86%e3%83%b3%e3%83%84';
        $urls[] = 'https://kinkaimasu.jp/brandlist_name %e3%83%90%e3%83%bc%e3%83%90%e3%83%aa%e3%83%bc%e3%83%95%e3%83%aa%e3%83%bc%e3%82%b3%e3%83%b3%e3%83%86%e3%83%b3%e3%83%842';
        $urls[] = 'https://kinkaimasu.jp/brandlist_name %e3%83%9f%e3%83%a5%e3%82%a6%e3%83%9f%e3%83%a5%e3%82%a6 %e3%83%95%e3%83%aa%e3%83%bc%e3%82%b3%e3%83%b3%e3%83%86%e3%83%b3%e3%83%84';
        $urls[] = 'https://kinkaimasu.jp/brandlist_name %e3%83%9f%e3%83%a5%e3%82%a6%e3%83%9f%e3%83%a5%e3%82%a6 %e3%83%95%e3%83%aa%e3%83%bc%e3%82%b3%e3%83%b3%e3%83%86%e3%83%b3%e3%83%842';
        $urls[] = 'https://kinkaimasu.jp/brandlist_name TASAKI%ef%bc%88%e3%82%bf%e3%82%b5%e3%82%ad%ef%bd%a5%e7%94%b0%e5%b4%8e%e7%9c%9f%e7%8f%a0%ef%bc%89 %e3%83%95%e3%83%aa%e3%83%bc%e3%82%b3%e3%83%b3%e3%83%86%e3%83%b3%e3%83%84';
        $urls[] = 'https://kinkaimasu.jp/brandlist_name TASAKI%ef%bc%88%e3%82%bf%e3%82%b5%e3%82%ad%ef%bd%a5%e7%94%b0%e5%b4%8e%e7%9c%9f%e7%8f%a0%ef%bc%89 %e3%83%95%e3%83%aa%e3%83%bc%e3%82%b3%e3%83%b3%e3%83%86%e3%83%b3%e3%83%842';
        $urls[] = 'https://kinkaimasu.jp/brandlist_name %e3%82%a2%e3%83%bc%e3%82%ab%e3%83%bchero_area';
        $urls[] = 'https://kinkaimasu.jp/colorstone/ruby-2';
        $urls[] = 'https://kinkaimasu.jp/palladium';
        $urls[] = 'https://kinkaimasu.jp/brandlist_name %e3%82%a2%e3%83%bc%e3%82%ab%e3%83%bc/AHKAH %e3%83%95%e3%83%aa%e3%83%bc%e3%82%b3%e3%83%b3%e3%83%86%e3%83%b3%e3%83%84';
        $urls[] = 'https://kinkaimasu.jp/brandlist_name %e3%82%a2%e3%83%bc%e3%82%ab%e3%83%bc/AHKAH %e3%83%95%e3%83%aa%e3%83%bc%e3%82%b3%e3%83%b3%e3%83%86%e3%83%b3%e3%83%842';
        $urls[] = 'https://kinkaimasu.jp/colorstone/sapphire_';
        $urls[] = 'https://kinkaimasu.jp/interview/vol_10';
        $urls[] = 'https://kinkaimasu.jp/interview/vol_09';
        $urls[] = 'https://kinkaimasu.jp/brandlist_name %e3%83%80%e3%82%a4%e3%82%a2%e3%82%b0%e3%83%a9%e3%83%a0%e3%82%b0%e3%83%ac%e3%83%bc%e3%82%b9%e3%82%b3%e3%83%b3%e3%83%81%e3%83%8d%e3%83%b3%e3%82%bfhero_area';
        $urls[] = 'https://kinkaimasu.jp/brandlist_name %e3%82%b9%e3%82%bf%e3%83%bc%e3%82%b8%e3%83%a5%e3%82%a8%e3%83%aa%e3%83%bc/STAR JEWELRY %e3%83%95%e3%83%aa%e3%83%bc%e3%82%b3%e3%83%b3%e3%83%86%e3%83%b3%e3%83%84';
        $urls[] = 'https://kinkaimasu.jp/information';
        $urls[] = 'https://kinkaimasu.jp/brandlist_name %e3%82%bb%e3%82%a4%e3%82%b3%e3%83%bchero_area';
        $urls[] = 'https://kinkaimasu.jp/gold-pt-rate-increase';
        $urls[] = 'https://kinkaimasu.jp/interview/vol_11';
        $urls[] = 'https://kinkaimasu.jp/graph_test';
        $urls[] = 'https://kinkaimasu.jp/wp_candle_graph';
        $urls[] = 'https://kinkaimasu.jp/interview/vol_12';
        $urls[] = 'https://kinkaimasu.jp/gold';
        $urls[] = 'https://kinkaimasu.jp/interview_list';
        $urls[] = 'https://kinkaimasu.jp/interview/vol_14';
        $urls[] = 'https://kinkaimasu.jp/interview_detail';
        $urls[] = 'https://kinkaimasu.jp/interview/vol_15';
        $urls[] = 'https://kinkaimasu.jp/interview/vol_16';
        $urls[] = 'https://kinkaimasu.jp/colorstone/marriageengagering';
        $urls[] = 'https://kinkaimasu.jp/%e3%81%8a%e5%ae%a2%e6%a7%98%e3%81%ae%e5%a3%b0';
        $urls[] = 'https://kinkaimasu.jp/reusable_block';
        $urls[] = 'https://kinkaimasu.jp/month_campaign';
        $urls[] = 'https://kinkaimasu.jp/letter';
        $urls[] = 'https://kinkaimasu.jp/perpiece-price_bk';
        $urls[] = 'https://kinkaimasu.jp/colorstone';
        $urls[] = 'https://kinkaimasu.jp/testtest';
        $urls[] = 'https://kinkaimasu.jp/campaign_test';
        $urls[] = 'https://kinkaimasu.jp/buyer/amami';
        $urls[] = 'https://kinkaimasu.jp/happydiamond-04';
        $urls[] = 'https://kinkaimasu.jp/ingot';
        $urls[] = 'https://kinkaimasu.jp/diamond';
        $urls[] = 'https://kinkaimasu.jp/buyer/kamizi';
        $urls[] = 'https://kinkaimasu.jp/buyer/hasegawa';
        $urls[] = 'https://kinkaimasu.jp/buyer/nushiro';
        $urls[] = 'https://kinkaimasu.jp/buyer/sugi';
        $urls[] = 'https://kinkaimasu.jp/gold/list';
        $urls[] = 'https://kinkaimasu.jp/brand/list';
        $urls[] = 'https://kinkaimasu.jp/colorstone/list';
        $urls[] = 'https://kinkaimasu.jp/diamond/list/';
        $urls[] = 'https://kinkaimasu.jp/buyer-2';
        $urls[] = 'https://kinkaimasu.jp/column/list';
        $urls[] = 'https://kinkaimasu.jp/trivia/gold';
        $urls[] = 'https://kinkaimasu.jp/brand/pontevecchio';
        $urls[] = 'https://kinkaimasu.jp/brand';
        $urls[] = 'https://kinkaimasu.jp/trivia/platinum';
        $urls[] = 'https://kinkaimasu.jp/trivia/colorstone';
        $urls[] = 'https://kinkaimasu.jp/trivia/cleaning';
        $urls[] = 'https://kinkaimasu.jp/instrument';
        $urls[] = 'https://kinkaimasu.jp/chanel/wallet';
        $urls[] = 'https://kinkaimasu.jp/kiyaku_20221125';
        $urls[] = 'https://kinkaimasu.jp/diamond/ring';
        $urls[] = 'https://kinkaimasu.jp/media/2022/0616/';
        $urls[] = 'https://kinkaimasu.jp/chanel/boroboro_chanel';
        $urls[] = 'https://kinkaimasu.jp/diamond/necklace';
        $urls[] = 'https://kinkaimasu.jp/chanel/camelia';
        $urls[] = 'https://kinkaimasu.jp/diamond/pierced_earrings';
        $urls[] = 'https://kinkaimasu.jp/chanel';
        $urls[] = 'https://kinkaimasu.jp/diamond/bracelet_bangle';
        $urls[] = 'https://kinkaimasu.jp/diamond/broach_pintuck_cuffs';
        $urls[] = 'https://kinkaimasu.jp/diamond/yamaoka';
        $urls[] = 'https://kinkaimasu.jp/4c';
        $urls[] = 'https://kinkaimasu.jp/chanel/watch';
        $urls[] = 'https://kinkaimasu.jp//chanel/vintage_chanel/';
        $urls[] = 'https://kinkaimasu.jp//chanel/matelasse/';
        $urls[] = 'https://kinkaimasu.jp/choice/navi/id';
        $urls[] = 'https://kinkaimasu.jp/chanel/airline';
        $urls[] = 'https://kinkaimasu.jp/choice/navi/payment';
        $urls[] = 'https://kinkaimasu.jp/choice/navi/delivery';
        $urls[] = 'https://kinkaimasu.jp/choice/navi/kaitori_card';
        $urls[] = 'https://kinkaimasu.jp/choice/navi/possible';
        $urls[] = 'https://kinkaimasu.jp/choice/navi/impossible';
        $urls[] = 'https://kinkaimasu.jp/louisvuitton/bag';
        $urls[] = 'https://kinkaimasu.jp/diamond/fancycolor';
        $urls[] = 'https://kinkaimasu.jp/louisvuitton/accessories';
        $urls[] = 'https://kinkaimasu.jp/4c/carat';
        $urls[] = 'https://kinkaimasu.jp/4c/color';
        $urls[] = 'https://kinkaimasu.jp/4c/clarity';
        $urls[] = 'https://kinkaimasu.jp/4c/cut';
        $urls[] = 'https://kinkaimasu.jp/louisvuitton/ladies_shoes';
        $urls[] = 'https://kinkaimasu.jp/louisvuitton/mens_shoes';
        $urls[] = 'https://kinkaimasu.jp/louisvuitton/cosmetic_pouch';
        $urls[] = 'https://kinkaimasu.jp/louisvuitton/list';
        $urls[] = 'https://kinkaimasu.jp/louisvuitton/agenda';
        $urls[] = 'https://kinkaimasu.jp/louisvuitton/alma';
        $urls[] = 'https://kinkaimasu.jp/about_diamond_sorting_memo';
        $urls[] = 'https://kinkaimasu.jp/louisvuitton/amazon';
        $urls[] = 'https://kinkaimasu.jp/louisvuitton/cite';
        $urls[] = 'https://kinkaimasu.jp/louisvuitton/danube';
        $urls[] = 'https://kinkaimasu.jp/louisvuitton/damier_jean';
        $urls[] = 'https://kinkaimasu.jp/louisvuitton/epi';
        $urls[] = 'https://kinkaimasu.jp/faq/takuhai/cancel_shippingcost';
        $urls[] = 'https://kinkaimasu.jp/refining';
        $urls[] = 'https://kinkaimasu.jp/louisvuitton/etui_cigarette';
        $urls[] = 'https://kinkaimasu.jp/buyer/sugi_02';
        $urls[] = 'https://kinkaimasu.jp/faq/takuhai/cardboard_othersize_order';
        $urls[] = 'https://kinkaimasu.jp/faq/takuhai/how_to_send';
        $urls[] = 'https://kinkaimasu.jp/faq/takuhai/payment_timing';
        $urls[] = 'https://kinkaimasu.jp/faq/refa/business_hours';
        $urls[] = 'https://kinkaimasu.jp/buyer/nushiro_02';
        $urls[] = 'https://kinkaimasu.jp/louisvuitton/graffiti';
        $urls[] = 'https://kinkaimasu.jp/louisvuitton/keepall';
        $urls[] = 'https://kinkaimasu.jp/louisvuitton/monogram_denim';
        $urls[] = 'https://kinkaimasu.jp/louisvuitton/monogram_empreinte';
        $urls[] = 'https://kinkaimasu.jp/brand_page_settings_louisvuitton';
        $urls[] = 'https://kinkaimasu.jp/diamond/carat_list';
        $urls[] = 'https://kinkaimasu.jp/louisvuitton/mahina';
        $urls[] = 'https://kinkaimasu.jp/louisvuitton/montsouris';
        $urls[] = 'https://kinkaimasu.jp/4c/sizetable';
        $urls[] = 'https://kinkaimasu.jp/louisvuitton/multicles';
        $urls[] = 'https://kinkaimasu.jp/metal';
        $urls[] = 'https://kinkaimasu.jp/louisvuitton/multicolor';
        $urls[] = 'https://kinkaimasu.jp/louisvuitton/musette';
        $urls[] = 'https://kinkaimasu.jp/louisvuitton/neverfull';
        $urls[] = 'https://kinkaimasu.jp/diamond/list-of-diamond-size-and-market';
        $urls[] = 'https://kinkaimasu.jp/louisvuitton/papillon';
        $urls[] = 'https://kinkaimasu.jp/louisvuitton/pegase';
        $urls[] = 'https://kinkaimasu.jp/louisvuitton/pochette-accessoires';
        $urls[] = 'https://kinkaimasu.jp/louisvuitton/pochette_cles';
        $urls[] = 'https://kinkaimasu.jp/louisvuitton/porte_monnaie_coeur';
        $urls[] = 'https://kinkaimasu.jp/louisvuitton/portefeuille_sarah';
        $urls[] = 'https://kinkaimasu.jp/diamond/melee-dia_purchase';
        $urls[] = 'https://kinkaimasu.jp/louisvuitton/secret';
        $urls[] = 'https://kinkaimasu.jp/louisvuitton/tambour';
        $urls[] = 'https://kinkaimasu.jp/louisvuitton/zippy_wallet';
        $urls[] = 'https://kinkaimasu.jp/removestone';
        $urls[] = 'https://kinkaimasu.jp/faq/refa/purchase_insecurity';
        $urls[] = 'https://kinkaimasu.jp/faq/refa/purchase_of_no_brand';
        $urls[] = 'https://kinkaimasu.jp/faq/refa/what_refoundation';
        $urls[] = 'https://kinkaimasu.jp/louisvuitton/speedy';
        $urls[] = 'https://kinkaimasu.jp/hermes/bag';
        $urls[] = 'https://kinkaimasu.jp/media/2022/0915/';
        $urls[] = 'https://kinkaimasu.jp/removediamonds';
        $urls[] = 'https://kinkaimasu.jp/hermes/birkin';
        $urls[] = 'https://kinkaimasu.jp/no_certificate_diamond_purchase_method';
        $urls[] = 'https://kinkaimasu.jp/hermes/kelly';
        $urls[] = 'https://kinkaimasu.jp/hermes/scarf';
        $urls[] = 'https://kinkaimasu.jp/colorstone/sapphire/sup3';
        $urls[] = 'https://kinkaimasu.jp/colorstone/sapphire/sup4';
        $urls[] = 'https://kinkaimasu.jp/goldcoin_list';
        $urls[] = 'https://kinkaimasu.jp/shape/ring';
        $urls[] = 'https://kinkaimasu.jp/hermes/wallet';
        $urls[] = 'https://kinkaimasu.jp/hermes/watch';
        $urls[] = 'https://kinkaimasu.jp/hermes/picotin';
        $urls[] = 'https://kinkaimasu.jp/hermes/accessories';
        $urls[] = 'https://kinkaimasu.jp/hermes/bolide';
        $urls[] = 'https://kinkaimasu.jp/refoundation';
        $urls[] = 'https://kinkaimasu.jp/shape/necklace_pendant';
        $urls[] = 'https://kinkaimasu.jp/sitemaps';
        $urls[] = 'https://kinkaimasu.jp/size_chart';
        $urls[] = 'https://kinkaimasu.jp/selling_situation';
        $urls[] = 'https://kinkaimasu.jp/hermes/evelyne';
        $urls[] = 'https://kinkaimasu.jp/ingot/good_delivery_bar';
        $urls[] = 'https://kinkaimasu.jp/hermes/lindy';
        $urls[] = 'https://kinkaimasu.jp/gucci';
        $urls[] = 'https://kinkaimasu.jp/coach';
        $urls[] = 'https://kinkaimasu.jp/season/aw';
        $urls[] = 'https://kinkaimasu.jp/refining/available_brands';
        $urls[] = 'https://kinkaimasu.jp/diatable';
        $urls[] = 'https://kinkaimasu.jp/shopchoice';
        $urls[] = 'https://kinkaimasu.jp/yahoo/exchange';
        $urls[] = 'https://kinkaimasu.jp/prada';
        $urls[] = 'https://kinkaimasu.jp/report/201510';
        $urls[] = 'https://kinkaimasu.jp/ecology';
        $urls[] = 'https://kinkaimasu.jp/affiliate';
        $urls[] = 'https://kinkaimasu.jp/sitemaps/category';
        $urls[] = 'https://kinkaimasu.jp/colorstone/alexandrite/sup1';
        $urls[] = 'https://kinkaimasu.jp/colorstone/emerald/sup3';
        $urls[] = 'https://kinkaimasu.jp/colorstone/opal/sup3';
        $urls[] = 'https://kinkaimasu.jp/colorstone/jade/sup1';
        $urls[] = 'https://kinkaimasu.jp/selling_situation02';
        $urls[] = 'https://kinkaimasu.jp/kiyaku';
        $urls[] = 'https://kinkaimasu.jp/diamond/perpiece-price';
        $urls[] = 'https://kinkaimasu.jp/difference_of_jewelry_price';
        $urls[] = 'https://kinkaimasu.jp/colorstone/coral/sup3';
        $urls[] = 'https://kinkaimasu.jp/item_list/watch';
        $urls[] = 'https://kinkaimasu.jp/refining/simulation';
        $urls[] = 'https://kinkaimasu.jp/item_list/accessories';
        $urls[] = 'https://kinkaimasu.jp/item_list/goods';
        $urls[] = 'https://kinkaimasu.jp/chat';
        $urls[] = 'https://kinkaimasu.jp/item_list/wallet';
        $urls[] = 'https://kinkaimasu.jp/item_list/bag';
        $urls[] = 'https://kinkaimasu.jp/search_result';
        $urls[] = 'https://kinkaimasu.jp/flow_for_secondhand_dealer';
        $urls[] = 'https://kinkaimasu.jp/item_list/shoes';
        $urls[] = 'https://kinkaimasu.jp/item_list/cosme';
        $urls[] = 'https://kinkaimasu.jp/line';
        $urls[] = 'https://kinkaimasu.jp/item_list/carry-bag';
        $urls[] = 'https://kinkaimasu.jp/mens';
        $urls[] = 'https://kinkaimasu.jp/choice/navi';
        $urls[] = 'https://kinkaimasu.jp/ladies';
        $urls[] = 'https://kinkaimasu.jp/promise';
        $urls[] = 'https://kinkaimasu.jp/choice';
        $urls[] = 'https://kinkaimasu.jp/top';
        $urls[] = 'https://kinkaimasu.jp/louisvuitton/pricelist';
        $urls[] = 'https://kinkaimasu.jp/brandlist';
        $urls[] = 'https://kinkaimasu.jp/kit';
        $urls[] = 'https://kinkaimasu.jp/businesstrip';
        $urls[] = 'https://kinkaimasu.jp/view_all_brand';
        $urls[] = 'https://kinkaimasu.jp/choice/repeat';
        $urls[] = 'https://kinkaimasu.jp/system';
        $urls[] = 'https://kinkaimasu.jp/flea_market_apps_comparison';
        $urls[] = 'https://kinkaimasu.jp/accessmap';
        $urls[] = 'https://kinkaimasu.jp/daily';
        $urls[] = 'https://kinkaimasu.jp/monthly';
        $urls[] = 'https://kinkaimasu.jp/metaltable';
        $urls[] = 'https://kinkaimasu.jp/happycolorstone';
        $urls[] = 'https://kinkaimasu.jp/happydiamond-05';
        $urls[] = 'https://kinkaimasu.jp/metalplatinumtable';
        $urls[] = 'https://kinkaimasu.jp/shop_purchase';

        $i = 0;
        foreach($urls as $url) {
            $url = $url . '/';
            $ch = curl_init($url);
            $header = $this->cls($ch);
            if ($header != 200) {
                $url = str_replace('kinkaimasu.jp', 'brandkaimasu.com', $url);
                $ch = curl_init($url);
                $header = $this->cls($ch);
            }
            echo "{$url}	{$header}<br>";
            $i++;
        }

    }

    
    function cls($ch){
        curl_setopt($ch,CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_exec($ch);
        $response_header = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return $response_header;
    }
}
