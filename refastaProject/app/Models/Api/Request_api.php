<?php

namespace App\Models\Api;

use Illuminate\Http\Request;
use App\Notifications\PushChatwork;

/**
* Guzzleを利用したリクエストまとめ
 * @param method GET or POST or ....
 * @param endpoint 
 */
class Request_api 
{
    public $base_url = "";

    public function __construct($base_url)
    {
        $this->base_url = $base_url;
    }


    public function send_request($method, $endpoint, $request_headers, $request_body_array)
    {
        if(is_array($request_body_array)){
            $request_body = json_encode($request_body_array);
        }else{
        $request_body = $request_body_array;
        }
        
        $client = new \GuzzleHttp\Client(['base_uri' => $this->base_url]);
        $request = new \GuzzleHttp\Psr7\Request($method, $endpoint, $request_headers, $request_body);
        try {
            $response = $client->send($request);
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            // 404などの場合はここでレスポンスを受け取る。
            // 200以外・・202などはどうなるかわからん。。
            $response = $e->getResponse();
        }


        $result["Headers"] = '';
        foreach ($response->getHeaders() as $name => $values) {
            $result["Headers"] .= $name . ': ' . implode(', ', $values) . "\n";
        }
        $body = $response->getBody();
        $body = (string)$body;
        $result["Body"] = $body;

        //★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★
        // ここにちゃんと202などの期待したステータスコードが返ってくるか確かめる。
        //★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★
        $result["StatusCode"] = $response->getStatusCode();
        $result["ReasonPhrase"] = $response->getReasonPhrase();

        if (
            ($result["StatusCode"] != "200")
            and ($result["StatusCode"] != "201")
            and ($result["StatusCode"] != "202")
            and ($result["StatusCode"] != "203")
            and ($result["StatusCode"] != "204")
        ) {
            $msg = "Request APIstatusエラー。200~204以外です。";
            $option = array("dir" => __DIR__, "file" => __FILE__, "line" => __LINE__, "class" => __CLASS__, "function" => __FUNCTION__);
            PushChatwork::error_push($msg, $option);
        }

        return $result;
    }

}
