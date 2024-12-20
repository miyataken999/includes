<?php
namespace App\Http\Controllers;
use App\Models\Api\Request_api;
use App\Notifications\PushChatwork;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Mail;

use App\Models\Internet\sagawa_api_results;
use App\Models\Internet\sagawa_api_result_codes;

/*
 * 佐川APIからマスタデータの取得をします
 * */
class GetSagawaController extends Controller{

    // apiリクエストのベースURL
    public $base_url = "https://beccl.biz-blue.net";

    //認証
    public function setGetSagawaParam()
    {
        // 認証情報
        $this->customerId = '11891322';// 本番　カスタマーID

        $this->loginPassword = 'YpTu6LyGotQbzqvxl+211g==';// 本番　ログインパスワード　

        //郵便番号
        $this->requestYubin = htmlspecialchars($_GET["yubin"], ENT_QUOTES);

    }

    //APIリクエスト
    public function getApiRequest()
    {
        $api = new Request_api($this->base_url);
        $method = 'POST';
        $endpoint = "/api/checkaddress";
        $request_headers = [
            'Content-type' => 'application/xml',
        ];
        $request_xml = $this->create_xml();
        $res = $api->send_request($method, $endpoint, $request_headers, $request_xml);
        $response_xml = $res["Body"];

        return $response_xml;
    }

    //佐川APIからマスタデータを取得
    public function get_address(Request $request)
    {
        if(!isset($_GET["yubin"])){
            return "リクエストが正しくありません。";
        }
        $this->setGetSagawaParam();
        $api_res = $this->getApiRequest();
        $xmldata = simplexml_load_string($api_res);
        if($xmldata->resultCode != 'S0-0001'){
            return "郵便番号に対応する住所がありません。エラーコード：" . $xmldata->resultCode;
        }
        $addressArray = array();
        foreach ($xmldata->addressList->addressInfo as $addressXml){
            $todofukenName = $addressXml->todofukenName;
            $shikuchosonName = $addressXml->shikuchosonName;
            $choikiName = $addressXml->choikiName;

            $address = $todofukenName . $shikuchosonName . $choikiName;

            $addressArray[] = $address;
        }
        return  implode("<br>", $addressArray);
    }

    //XMLデータ生成
    public function create_xml()
    {
        $xw = new \XMLWriter();
        $xw->openMemory();
            $xw->startDocument("1.0");

                $xw->startElement("checkAddressRequest");
                    $xw->startElement("customerAuth");
                        $xw->startElement("customerId");
                            $xw->text($this->customerId);
                        $xw->endElement();
                        $xw->startElement("loginPassword");
                            $xw->text($this->loginPassword);
                        $xw->endElement();
                    $xw->endElement(); // end customerAuth

                    $xw->startElement("requestYubin");
                        $xw->text($this->requestYubin);
                    $xw->endElement(); // end 郵便番号をセットするelement

                    $xw->startElement("requestAddress");
                    $xw->endElement(); // end 郵便番号をセットするelement


                $xw->endElement(); // end shippingRequest

            $xw->endDocument();
        $xml = $xw->outputMemory();
        return $xml;
    }
}