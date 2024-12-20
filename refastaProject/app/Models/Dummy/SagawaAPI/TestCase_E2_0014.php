<?php

namespace App\Models\Dummy\SagawaAPI;




//レスポンスコード
//20210821

// <shippingResponse>
// <printOutFlg>0</printOutFlg>
// <okuriCode>N01</okuriCode>
// <outputLevel>000</outputLevel>
// <resultCode>E8-0001</resultCode>
// <printDataList>
// <printDataDetail>
// <userManageNumber>20150626092404</userManageNumber>
// <resultCodeList>
// <resultCode>E2-0005</resultCode>
// </resultCodeList>
// </printDataDetail>
// </printDataList>
// </shippingResponse>

/**
 * Class TestCase
 * @package App\Models\Dummy\SagawaAPI
 */
class TestCase_E2_0014
{

    /**
     * @param string customerId 固定
     * @param string loginPassword 固定
     * @param string printOutFlg 固定
     * @param string okuriCode 固定
     * @param string outputLevel 固定
     * @param string kokyakuCode 固定
     * @param string otodokeAdd1 住所１（ラウンジ）
     * @param string otodokeAdd2 住所２（ラウンジ）
     * @param string otodokeAdd3 住所３（ラウンジ）
     * @param string otodokeNm1 固定 届け先名（ラウンジ）
     * @param string otodokeYubin 固定 届け先郵便番号（ラウンジ）
     * @param string otodokeTel 固定 届け先TEL（ラウンジ）
     * @param string iraiPrintFlg 固定
     * @param string binsyuCode 固定（陸便）
     * @param string daibikiFlg 固定（代引きしない）
     * @param string motoChakuCode 固定（配送料金の支払い方法を設定）
     * @param string shukaIraiCode 固定（契約コードなので固定）

     * @param string haisoKosu 集荷依頼の個数（申込内容を反映）
     * @param string userManageNumber  こちら側でバラバラな内容で指定する管理番号
     * @param string iraiAdd1 集荷依頼（お客様）住所（申込内容を反映）
     * @param string iraiNm1 集荷依頼（お客様）名前（申込内容を反映）
     * @param string iraiYubin 集荷依頼（お客様）郵便番号（申込内容を反映）
     * @param string iraiTel 集荷依頼（お客様）TEL（申込内容を反映）
     * @param string iraiMailAddress 集荷依頼（お客様）メールアドレス（申込内容を反映）
     * @param string shukaIraiShiteiDate 集荷依頼日 yyyyMMdd（申込内容を反映）
     * @param string shukaIraiShiteiTimeCode 集荷依頼時間帯 （コード参照）（申込内容を反映）
     */
    public function set()
    {
        $this->customerId = '11872422'; // 必須:◎項番:1-1-1階層:3属性:半角文字桁数:50桁数:50項目名:カスタマーID備考:
        $this->loginPassword = '/05EOuE0HT9c/5e73UMamw=='; // 必須:◎項番:1-1-2階層:3属性:半角文字桁数:100桁数:100項目名:ログインパスワード備考:
        $this->printOutFlg = '0'; // 必須:◎項番:1-3階層:2属性:半角数字桁数:1桁数:1項目名:送り状発行依頼フラグ備考:0 - (確認機能) エラー精査のみ行います ---1 - (発行依頼機能) エラー精査後、出荷情報が全て正常であ れば送り状発行処理を行います
        $this->okuriCode = 'N01'; // 必須:○項番:1-4階層:2属性:半角文字桁数:20桁数:20項目名:送り状コード備考:送り状の帳票コードを設定 ---5.1.パラメータコード定義書参照 ---送り状発行依頼フラグが1の場合、指定必須
        $this->outputLevel = '000'; // 必須:◎項番:1-5階層:2属性:半角数字桁数:3桁数:3項目名:出力レベル備考:エラー判定の精査範囲を設定 ---5.1.パラメータコード定義書参照
        $this->kokyakuCode = '130003750009'; // 必須:◎項番:1-7-1-3階層:4属性:半角数字桁数:12桁数:12項目名:顧客コード備考:配送会社との契約コード ---チェックデジット含みます
        $this->otodokeAdd1 = '東京都豊島区東池袋'; // 必須:◎項番:1-7-1-4階層:4属性:指定なし桁数:25桁数:25項目名:届先住所1備考:半角/全角問わず、25文字まで印字可能
        $this->otodokeAdd2 = '1丁目25−14 アルファビルディング4F'; // 必須:×項番:1-7-1-5階層:4属性:指定なし桁数:25桁数:25項目名:届先住所2備考:半角/全角問わず、25文字まで印字可能
        $this->otodokeAdd3 = 'ラウンジデザイナーズ株式会社'; // 必須:×項番:1-7-1-6階層:4属性:指定なし桁数:25桁数:25項目名:届先住所3備考:半角/全角問わず、25文字まで印字可能
        $this->otodokeNm1 = '宅配買取事業部'; // 必須:◎項番:1-7-1-7階層:4属性:指定なし桁数:25桁数:25項目名:届先氏名1備考:半角/全角問わず、25文字まで印字可能
        $this->otodokeYubin = '1700013'; // 必須:◎項番:1-7-1-9階層:4属性:半角数字桁数:7桁数:7項目名:届先郵便番号備考:ハイフン無し
        $this->otodokeTel = '03-5985-0388'; // 必須:◎項番:1-7-1-10階層:4属性:半角文字桁数:20桁数:20項目名:届先電話番号備考:数字またはハイフン
        $this->iraiPrintFlg = '1'; // 必須:◎項番:1-7-1-12階層:4属性:半角数字桁数:1桁数:1項目名:依頼主指定フラグ備考:送り状の依頼主項目の印字を顧客コードに紐づく出荷場にす るか、下記の依頼主情報を記載するか指定します 0 - 顧客コード紐づく出荷場情報を印字 ---1 - 下記の依頼主情報を参照
        $this->binsyuCode = '000'; // 000=陸便  // 必須:◎項番:1-7-1-28階層:4属性:半角数字桁数:3桁数:3項目名:便種コード備考:5.1.パラメータコード定義参照
        $this->daibikiFlg = '0'; // 必須:◎項番:1-7-1-29階層:4属性:半角数字桁数:1桁数:1項目名:代金引換フラグ備考:0 - 通常出荷の送り状を発行 ---1 - 代金引換の送り状を発行
        $this->motoChakuCode = '1'; // 必須:○項番:1-7-1-46階層:4属性:半角数字桁数:1桁数:1項目名:元着コード備考:配送料金の支払い方法を設定 ---5.1.パラメータコード定義参照
        $this->shukaIraiCode = '130003750009'; // 必須:×項番:1-7-1-47-1階層:5属性:半角数字桁数:12桁数:12項目名:集荷依頼主コード備考:集荷依頼の場合必須 ---集荷依頼を利用する配送会社との契約コード ---チェックデジット含みます

        $this->haisoKosu = '1'; // 必須:◎項番:1-7-1-1階層:4属性:半角数字桁数:2桁数:2項目名:配送個口数備考:発送個口数 ---指定分送り状発行
        $this->userManageNumber = '20150626092404'; // 必須:◎項番:1-7-1-2階層:4属性:半角文字桁数:16桁数:16項目名:管理番号備考:利用者側で管理する一意なコード
        $this->iraiAdd1 = '東京都豊島区東池袋4-30-13-501'; // 必須:○項番:1-7-1-13階層:4属性:指定なし桁数:25桁数:25項目名:依頼主住所1備考:依頼主指定フラグが1の場合必須 ---半角/全角問わず、25文字まで印字可能
        $this->iraiNm1 = '西谷真一'; // 必須:○項番:1-7-1-16階層:4属性:指定なし桁数:25桁数:25項目名:依頼主氏名1備考:依頼主指定フラグが1の場合必須 ---半角/全角問わず、25文字まで印字可能
        $this->iraiYubin = '1700013'; // 必須:○項番:1-7-1-18階層:4属性:半角数字桁数:7桁数:7項目名:依頼主郵便番号備考:依頼主指定フラグが1の場合必須 ---ハイフンなし
        $this->iraiTel = '08065294996'; // 必須:○項番:1-7-1-19階層:4属性:半角文字桁数:20桁数:20項目名:依頼主電話番号備考:依頼主指定フラグが1の場合必須 ---数字またはハイフン
        $this->iraiMailAddress = 'nishitani@urlounge.co.jp'; // 必須:×項番:1-7-1-20階層:4属性:半角文字桁数:320桁数:320項目名:依頼主メールアドレス備考:
        $this->shukaIraiShiteiDate = '20210822'; // 必須:×項番:1-7-1-47-2階層:5属性:半角文字桁数:8桁数:8項目名:集荷依頼指定日備考:集荷依頼の場合必須 ---yyyyMMdd
        $this->shukaIraiShiteiTimeCode = '00'; // 必須:×項番:1-7-1-47-3階層:5属性:半角文字桁数:2桁数:2項目名:集荷依頼時間指定コード備考:集荷依頼の場合必須 ---5.1.パラメータコード定義参照

    }
}
