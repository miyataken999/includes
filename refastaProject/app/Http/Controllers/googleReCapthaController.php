<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;

/**
 * 
 * google reCAPTHAテスト
 * @link クライアント側DOC https://developers.google.com/recaptcha/docs/display
 * @link サーバー側DOC https://developers.google.com/recaptcha/docs/verify
 */
class googleReCapthaController extends Controller
{
    public function index(Request $request)
    {
        return view("googleReCaptha.index");
    }

    public function submit(Request $request)
    {

        $recaptcha = htmlspecialchars($_POST["g-recaptcha-response"], ENT_QUOTES, 'UTF-8');

        if (isset($recaptcha)) {
            $captcha = $recaptcha;
        } else {
            $captcha = "";
            echo "captchaエラー";
            exit;
        }
        $secretKey = "6LftmrEbAAAAAOjYHXrrWmGHHCmYM1rfEFJYnvJp";

        $resp = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$secretKey}&response={$captcha}");
        $resp_result = json_decode($resp, true);

        var_dump($resp_result);

        if (intval($resp_result["success"]) !== 1) {
            //認証失敗時の処理をここに書く
        } else {
            //認証成功時の処理をここに書く
            //ここにmailsend等の記述をする。
        }

    }

}
