<?php

namespace App\Http\Controllers;

use App\Models\Api\Request_api;
use App\Notifications\PushChatwork;

use Illuminate\Http\Request;
use DB;

use App\Models\Internet\sagawa_api_results;
use App\Models\Internet\sagawa_api_result_codes;

use Illuminate\Support\Facades\Mail;
use App\Notifications\PushMessage;
use Exception;

/**
 * 佐川送り状発行API
 * 今回は、このうちの集荷依頼機能を利用
 * @see https://drive.google.com/file/d/1qYX_7lwf3bpuu-5OAl8H7t1rsj0oWtQX/view?usp=sharing
 * @see https://docs.google.com/spreadsheets/d/1ldCGetlucQpV9dLG_SA56gwva-0O9XQPr_sqwJdwAQ8/edit#gid=1856808430
 *
 * セットするxml内容の補足
 * <otodoke~>
 * 集荷したお荷物の配達先（ラウンジ）
 * <irai〇〇>
 * 集荷に伺うお客様の情報
 * <userManageNumbe>
 * 日時＋ランダム文字列
 * <shukaIraiShiteiDate>
 * 集荷に伺う日付
 * <shukaIraiShiteiTimeCode>
 * 集荷に伺う時間帯。指定しない場合「00」
 * <printOutFlg>
 * 実際に集荷に伺うデータを送信する場合「1」
 * エラーチェック等でテスト送信をする場合は「0」
 * 【ご注意事項】
 * <motoChakucode>
 * 元払・着払の指定項目。着払「1」
 * <kiji2>
 * 着払の際は、こちらの顧客コードを基に運賃の請求を行います。
 * その為、お手数ですが上記の項目へ顧客コードをセットして頂けますと幸いです。
 *
 */
class SagawaController extends Controller
{
    // apiリクエストのベースURL
    // public $base_url = "http://beccl-st.biz-blue.net";
    public $base_url = "https://beccl.biz-blue.net";

    /**
     * リクエスト用の送信パラメータに使うプロパティ設定
     * @link https://docs.google.com/spreadsheets/d/1hYTzfBV52CoJFnUvlhh1c2BEemMHtZEDN9ojXCpGaEc/edit#gid=597350204
     * @param object $request Request Class
     * @param boolean $is_test テストフラグ
     */
    public function setShuukaParam($request, $is_test)
    {
        // 認証情報
        // $this->customerId = '11872422';// test
        $this->customerId = '11891322';// 本番　
        
        // $this->loginPassword = '/05EOuE0HT9c/5e73UMamw==';// test
        $this->loginPassword = 'YpTu6LyGotQbzqvxl+211g==';// 本番　
        
        // ランダム管理番号
        $this->userManageNumber = $this->create_userManageNumber();
        // 固定項目
        $this->okuriCode = 'N01';// 送り状の帳票コード
        $this->outputLevel = '000';// エラー判定の精査範囲を設定
        $this->otodokeAdd1 = '東京都豊島区東池袋';
        $this->otodokeAdd2 = '1丁目25−14 アルファビルディング4F';
        $this->otodokeAdd3 = 'ラウンジデザイナーズ株式会社';
        $this->otodokeNm1 = '宅配買取事業部';
        $this->otodokeYubin = '1700013';
        $this->otodokeTel = '03-5985-0388';
        $this->iraiPrintFlg = '1';// 送り状の依頼主項目の印字 [0]顧客コード紐づく出荷場情報 [1]依頼主情報を参照
        $this->binsyuCode = '000';// 陸便
        $this->daibikiFlg = '0';// 代引きなし
        $this->motoChakuCode = "1";// 元払・着払の指定項目。着払「1」
        // 以下、違い不明
        $this->kokyakuCode = "130003750009";
        $this->shukaIraiCode = "130003750009";// 集荷依頼主コード
        $this->kiji2 = "130003750009";// 着払の際は、この顧客コードを基に運賃の請求
        $this->kiji3 = "内容品：貴重品";
        $this->kiji4 = "集荷先の荷送人さまが不在時には、弊社へ連絡を希望します。";

        $this->printOutFlg = "1";// 実際に集荷する場合は「1」

        // 申込情報の反映箇所
        if($is_test == "is_test"){
            // test実行
            $this->setShuukaParam_test($request);
        }else{
            // 本番実行
            $this->setShuukaParam_honban($request);
        }
    }

    public function setShuukaParam_test($request)
    {
        // テストケース
        $TestCase = \App\Models\Dummy\SagawaAPI\TestCase::setTest();
        $TestCase->set();
        $this->iraiAdd1 = $TestCase->iraiAdd1;// 25文字まで
        $this->iraiAdd2 = "aaa";// 25文字まで
        $this->iraiAdd3 = "bbb";// 25文字まで
        $this->iraiNm1 = $TestCase->iraiNm1;
        $this->iraiYubin = $TestCase->iraiYubin;
        $this->iraiTel = $TestCase->iraiTel;
        $this->iraiMailAddress = $TestCase->iraiMailAddress;
        $this->haisoKosu = $TestCase->haisoKosu;
        $this->shukaIraiShiteiDate = $TestCase->shukaIraiShiteiDate;// 集荷に伺う日付 yyyymmdd
        $this->shukaIraiShiteiTimeCode = $TestCase->shukaIraiShiteiTimeCode;// 集荷に伺う時間帯。指定しない場合「00」
    }
    public function setShuukaParam_honban($request)
    {
        /**
         * shukaIraiShiteiTimeCode
         * [00] 時間帯指定なし
         * [09] 9:00～12:00
         * [12] 12:00～15:00
         * [15] 15:00～18:00
         * [18] 18:00～21:00
         */
        $this->iraiAdd1 = $request->iraiAdd1;// 25文字まで
        $this->iraiAdd2 = $request->iraiAdd2;// 25文字まで
        $this->iraiAdd3 = $request->iraiAdd3;// 25文字まで
        $this->iraiNm1 = $request->iraiNm1;
        $this->iraiYubin = $request->iraiYubin;
        $this->iraiTel = $request->iraiTel;
        $this->iraiMailAddress = $request->iraiMailAddress;
        $this->haisoKosu = mb_convert_kana($request->haisoKosu, "n");
        $this->haisoKosu = preg_replace('/[^0-9]/', '', $this->haisoKosu);
        $this->shukaIraiShiteiDate = str_replace("-", "", $request->shukaIraiShiteiDate);// 集荷に伺う日付 yyyymmdd
        $this->shukaIraiShiteiTimeCode = $request->shukaIraiShiteiTimeCode; // 集荷に伺う時間帯。指定しない場合「00」
    }


    /**
     * 管理番号の生成
     */
    public function create_userManageNumber()
    {
        $array = [
            "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z"
            , "A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z"
            , "0", "1", "2", "3", "4", "5", "6", "7", "8", "9"
        ];
        $manageNumber = date("ymdHis");
        for ($i = 0; $i < 4; $i++)
        {
            $manageNumber .= $array[random_int(0,(count($array)-1))];
        }
        return $manageNumber;
    }

    /**
     * 集荷依頼のAPIリクエスト
     */
    public function shuukairai(Request $request, $is_test="")
    {
        $api = new Request_api($this->base_url);
        // パラメータのセット
        if($is_test == "is_test"){
            $this->setShuukaParam($request, "is_test");
        }else{
            $this->setShuukaParam($request, "");
        }
        $method = 'POST';
        $endpoint = "/api/shipping";
        $request_headers = [
            'Content-type' => 'application/xml',
        ];
        $request_xml = $this->create_xml();
        // 3個以上あったら止める
        if ($this->haisoKosu >= 3) {
            $sagawa_api_result = new sagawa_api_results();
            $sagawa_api_result->request_xml = $request_xml;
            $sagawa_api_result->send_at = date("Y-m-d H:i:s");
            $sagawa_api_result->resultCode = "customError0001";
            $sagawa_api_result->save();

            $err_msg = "[info][title]佐川集荷APIエラー発生[/title]";
            $err_msg .= "[toall]\n";
            // $err_msg .= "[To:3335415]平　ゆりえさん\n";
            // $err_msg .= "[To:6233305]伊藤　希さん\n";
            // $err_msg .= "[To:5983309]愛川　ゆりえさん\n";
            // $err_msg .= "[To:6096553]春野　雅さん\n";
            // $err_msg .= "[To:5232919]小野田　こずゑさん\n";
            // $err_msg .= "[To:5232918]吉本　ミクさん\n";
            $err_msg .= "個数が３個以上選択されているエラーです。\n";
            $err_msg .= "詳細URL: https://rifa.life/shop5/sagawa_api_results_list.php?q=(sagawa_api_result_id~equals~{$sagawa_api_result->sagawa_api_result_id})[/info]";
            // PushChatwork::push($err_msg, $room = "84134721", $token = "nishitani");
            // PushChatwork::push($err_msg, $room = "68102709", $token = "nishitani");
            $sendPostData = array(
                "subject" => "佐川集荷APIエラー発生",
                "text" => "個数が３個以上選択されているエラーです。\n詳細URL: https://rifa.life/shop5/sagawa_api_results_list.php?q=(sagawa_api_result_id~equals~{$sagawa_api_result->sagawa_api_result_id})",
            );
            $sendPostData = http_build_query($sendPostData, "", "&");
            $sendPostDataOptions = array('http' => array('method'=> 'POST','header'=> 'Content-Type: application/x-www-form-urlencoded','content' => $sendPostData));
            $sendPostContext = stream_context_create($sendPostDataOptions);
            try{
                file_get_contents('https://rifa.life/evaProject/api/notification/mail', false, $sendPostContext);
            }catch(\Exception $e){}


            if (isset($request->is_office)) {
                return redirect("https://rifa.life/shop5/sagawa_api_results_list.php");
            }else{
                return "";
            }
        }
        $res = $api->send_request($method, $endpoint, $request_headers, $request_xml);
        $response_xml = $res["Body"];
        $this->db_save($request_xml, $response_xml, $request);
        if(isset($request->is_office)){
            return redirect("https://rifa.life/shop5/sagawa_api_results_list.php");
        }
        // return response($res["Body"])->header("Content-Type", "application/xml");
        return "";
    }

    /**
     * api結果のDB保存処理
     * @param string $request_xml
     * @param string $response_xml
     */
    public function db_save($request_xml, $response_xml, $request)
    {
        $response_body = simplexml_load_string($response_xml);
        if($response_body === false)
        {
            return false;
        }

        $sagawa_api_result = new sagawa_api_results();
        $sagawa_api_result->request_xml = $request_xml;
        $sagawa_api_result->response_xml = $response_xml;
        $sagawa_api_result->send_at = date("Y-m-d H:i:s");
        if(isset($response_body->resultCode))
        {
            $sagawa_api_result->resultCode = $response_body->resultCode;
        }


        if(isset($response_body->printDataList) && isset($response_body->printDataList->printDataDetail))
        {
            $is_error = "";
            $printDataDetail = $response_body->printDataList->printDataDetail;
            $sagawa_api_result->userManageNumber = $printDataDetail->userManageNumber;
            if(isset($response_body->resultCode))
            {
                if($response_body->resultCode == "S0-0001")
                {
                    //正常終了 or 「W0-0002」（注意）があるが、今回の集荷APIではない
                    $sagawa_api_result = $this->depotInfo_save($sagawa_api_result, $printDataDetail);
                }else{
                    //エラー時は「E8-0001」
                    $is_error = "on";
                }
            }
            $sagawa_api_result->save();
            $resultcode_text = $this->resultCode_save($sagawa_api_result, $printDataDetail);
            if($is_error !== "")
            {
                $err_msg = "[info][title]佐川集荷APIエラー発生[/title]";
                $err_msg .= "[toall]\n";
                // $err_msg .= "[To:3335415]平　ゆりえさん\n";
                // $err_msg .= "[To:6233305]伊藤　希さん\n";
                // $err_msg .= "[To:5983309]愛川　ゆりえさん\n";
                // $err_msg .= "[To:6096553]春野　雅さん\n";
                // $err_msg .= "[To:5232919]小野田　こずゑさん\n";
                // $err_msg .= "[To:5232918]吉本　ミクさん\n";
                $err_msg .= "原因[" . trim($resultcode_text) . "]\n";
                $err_msg .= "詳細URL: https://rifa.life/shop5/sagawa_api_results_list.php?q=(sagawa_api_result_id~equals~{$sagawa_api_result->sagawa_api_result_id})[/info]";
                PushChatwork::push($err_msg, $room = "84134721", $token = "nishitani");
                // PushChatwork::push($err_msg, $room = "68102709", $token = "nishitani");
                $sendPostData = array(
                    "subject" => "佐川集荷APIエラー発生",
                    "text" => "原因[" . trim($resultcode_text) . "]\n詳細URL: https://rifa.life/shop5/sagawa_api_results_list.php?q=(sagawa_api_result_id~equals~{$sagawa_api_result->sagawa_api_result_id})",
                );
                $sendPostData = http_build_query($sendPostData, "", "&");
                $sendPostDataOptions = array('http' => array('method'=> 'POST','header'=> 'Content-Type: application/x-www-form-urlencoded','content' => $sendPostData));
                $sendPostContext = stream_context_create($sendPostDataOptions);
                try{
                    file_get_contents('https://rifa.life/evaProject/api/notification/mail', false, $sendPostContext);
                }catch(\Exception $e){}

            }else{
                // エラーがない場合のみ、手配完了メール送信
                $this->send_shuuka_api_mail($request);
            }
        }
    }

    /**
     * depotInfoの詳細の処理
     * @param object $sagawa_api_result API結果テーブルモデルのインスタンス
     * @param object $printDataDetail
     */
    public function depotInfo_save($sagawa_api_result, $printDataDetail)
    {
        if(isset($printDataDetail->depotInfo))
        {
            if(isset($printDataDetail->depotInfo->depotCode))
            {
                $sagawa_api_result->depotCode = $printDataDetail->depotInfo->depotCode;
            }
            if(isset($printDataDetail->depotInfo->depotName))
            {
                $sagawa_api_result->depotName = $printDataDetail->depotInfo->depotName;
            }
            if(isset($printDataDetail->depotInfo->depotTel))
            {
                $sagawa_api_result->depotTel = $printDataDetail->depotInfo->depotTel;
            }
        }
        return $sagawa_api_result;
    }


    /**
     * resultCodeの詳細の処理（sagawa_api_result_codesの生成）
     * @param object $sagawa_api_result API結果テーブルモデルのインスタンス
     * @param object $printDataDetail
     */
    public function resultCode_save($sagawa_api_result, $printDataDetail)
    {
        $resultcode_text = "";
        $sagawa_api_result_id = $sagawa_api_result->sagawa_api_result_id;
        if(isset($printDataDetail->resultCodeList) && isset($printDataDetail->resultCodeList->resultCode))
        {
            $resultCode = $printDataDetail->resultCodeList->resultCode;
            foreach ($resultCode as $code)
            {
                $sagawa_api_result_code = new sagawa_api_result_codes();
                $sagawa_api_result_code->sagawa_api_result_id = $sagawa_api_result_id;
                $sagawa_api_result_code->result_code = $code;
                $sagawa_api_result_code->save();
                $resultcode_text .= $code . "\n";
            }

        }
        return $resultcode_text;
    }



    /**
     * xmlデータの生成
     */
    public function create_xml()
    {
        $xw = new \XMLWriter();
        $xw->openMemory();
        $xw->startDocument("1.0");
        $xw->startElement("shippingRequest");
        $xw->startElement("customerAuth");
        $xw->startElement("customerId");
        $xw->text($this->customerId);
        $xw->endElement();
        $xw->startElement("loginPassword");
        $xw->text($this->loginPassword);
        $xw->endElement();
        $xw->endElement(); // end customerAuth
        $xw->startElement("printOutFlg");
        $xw->text($this->printOutFlg);
        $xw->endElement(); // end printOutFlg

        $xw->startElement("okuriCode");
        $xw->text($this->okuriCode);
        $xw->endElement(); // end okuriCode

        $xw->startElement("outputLevel");
        $xw->text($this->outputLevel);
        $xw->endElement(); // end outputLevel

        $xw->startElement("printDataList");
        $xw->startElement("printDataDetail");
        $printDataDetail_array = array(
            "haisoKosu",
            "userManageNumber",
            "kokyakuCode",
            "otodokeAdd1",
            "otodokeAdd2",
            "otodokeAdd3",
            "otodokeNm1",
            "otodokeYubin",
            "otodokeTel",
            "iraiPrintFlg",
            "iraiAdd1",
            "iraiAdd2",
            "iraiAdd3",
            "iraiNm1",
            "iraiYubin",
            "iraiTel",
            "iraiMailAddress",
            "binsyuCode",
            "daibikiFlg",
            "kiji2",
            "kiji3",
            "kiji4",
        );
        foreach ($printDataDetail_array as $value) {
            $xw->startElement($value);
            $xw->text($this->$value);
            $xw->endElement();
        }

        $xw->startElement("motoChakuCode");
        $xw->text($this->motoChakuCode);
        $xw->endElement(); // motoChakuCode


        $xw->startElement("shukaIraiData");
        $shukaIraiData_array = array(
            "shukaIraiCode",
            "shukaIraiShiteiDate",
            "shukaIraiShiteiTimeCode",
        );
        foreach ($shukaIraiData_array as $value) {
            $xw->startElement($value);
            $xw->text($this->$value);
            $xw->endElement();
        }
        $xw->endElement(); // shukaIraiData

        $xw->endElement(); // end printDataDetail
        $xw->endElement(); // end printDataList
        $xw->endElement(); // end shippingRequest
        $xw->endDocument();

        $xml = $xw->outputMemory();
        return $xml;
    }


    /**
     * 集荷手配完了メール送信
     */
    public function send_shuuka_api_mail($request)
    {
        $input_values = $request;
        $to = $this->iraiMailAddress;
        $title = '【スピード宅配買取申込完了のご案内】リファスタ';
        $type = 'sagawa';
        $send_type = 'visitor';
        Mail::to($to)->bcc("rifa@urlounge.co.jp")->send(new PushMessage($input_values,$title,$type,$send_type));

    }
}