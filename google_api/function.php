<?php

/**
 * Returns an authorized API client.
 * @return Google_Client the authorized client object
 */
function getClient() {
  $client = new Google_Client();
  $client->setApplicationName(APPLICATION_NAME);
  $client->setScopes(SCOPES);
  $client->setAuthConfig(CLIENT_SECRET_PATH);
  $client->setAccessType('offline');

  // Load previously authorized credentials from a file.
  $credentialsPath = expandHomeDirectory(CREDENTIALS_PATH);
  if (file_exists($credentialsPath)) {
    $accessToken = json_decode(file_get_contents($credentialsPath), true);
  } else {
    // Request authorization from the user.
    $authUrl = $client->createAuthUrl();
    printf("Open the following link in your browser:\n%s\n", $authUrl);
    print 'Enter verification code: ';
    $authCode = trim(fgets(STDIN));

    // Exchange authorization code for an access token.
    $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);

    // Store the credentials to disk.
    if(!file_exists(dirname($credentialsPath))) {
      mkdir(dirname($credentialsPath), 0700, true);
    }
    file_put_contents($credentialsPath, json_encode($accessToken));
    printf("Credentials saved to %s\n", $credentialsPath);
  }
  $client->setAccessToken($accessToken);

  // Refresh the token if it's expired.
  if ($client->isAccessTokenExpired()) {
    $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
    file_put_contents($credentialsPath, json_encode($client->getAccessToken()));
  }
  return $client;
}


/**
 * Expands the home directory alias '~' to the full path.
 * @param string $path the path to expand.
 * @return string the expanded path.
 */
function expandHomeDirectory($path) {
  $homeDirectory = getenv('HOME');
  if (empty($homeDirectory)) {
    $homeDirectory = getenv('HOMEDRIVE') . getenv('HOMEPATH');
  }
  return str_replace('~', realpath($homeDirectory), $path);
}


function cw_push($msg,$room='68102709',$token='nishitani')
    {


        //本番ルーム
        // $room = "";
        //テスト用個人ルーム
        // $room = "67058874";
      $mukouyama = "6f1db69a7e4395287486998ed4d6be06";
        //誰から発行するか
        $sugisan = "4207431dc4eb42bb1e13befa2f6657fe";
        $bot = "ed472827b8dda0ba81ba586c8b0e4bd9";
        $nishitani = $bot;

        $sekkyaku = "d567b3dc41da98c77a37e53b9a52fbb0";



        $token = $$token;


        $c = system('curl -X POST -H "X-ChatWorkToken:'.$token.'" -d "body='.$msg.'" "https://api.chatwork.com/v2/rooms/'.$room.'/messages" -O',$c);

        // if(!$c){
        //     return '送信失敗しました。';
        // }else{
        //     return '送信成功！';
        // }
    }


function cw_task($msg,$to_ids='2107601',$room='68102709',$token='nishitani',$limit='')
    {
        //タスク宛先
        // $to_ids = 2107601;//西谷
        // $to_ids = 1147485;//杉さん
        // $to_ids = 2362369;//脇坂さん

        //本番ルーム
        // $room = "";
        //テスト用個人ルーム
        // $room = "67058874";
        //誰から発行するか
        $mukouyama = "6f1db69a7e4395287486998ed4d6be06";
        $sugisan = "4207431dc4eb42bb1e13befa2f6657fe";
        $bot = "ed472827b8dda0ba81ba586c8b0e4bd9";
        $nishitani = $bot;// tamiya

        $token = $$token;

        if($limit == ''){
          $limit = time();
        }

        $msg = str_replace("&","＆",$msg);
        $msg = str_replace("'","’",$msg);
        $msg = str_replace("\"","”",$msg);
        // $system = 'curl -X POST -H "X-ChatWorkToken:'.$token.'" -d "body='.$msg.'&limit='.$limit.'&to_ids='.$to_ids.'" "https://api.chatwork.com/v2/rooms/'.$room.'/tasks"';
        // echo $system;
        $c = system('curl -X POST -H "X-ChatWorkToken:'.$token.'" -d "body='.$msg.'&limit='.$limit.'&to_ids='.$to_ids.'" "https://api.chatwork.com/v2/rooms/'.$room.'/tasks"',$c);
    }



function cw_file_up($msg,$room='68102709',$token='nishitani',$file='')
    {


        //本番ルーム
        // $room = "";
        //テスト用個人ルーム
        // $room = "67058874";
      $mukouyama = "6f1db69a7e4395287486998ed4d6be06";
        //誰から発行するか
        $sugisan = "4207431dc4eb42bb1e13befa2f6657fe";
        $bot = "ed472827b8dda0ba81ba586c8b0e4bd9";
        $nishitani = $bot;

        $sekkyaku = "d567b3dc41da98c77a37e53b9a52fbb0";



        $token = $$token;


        $c = system('curl -X POST -H "X-ChatWorkToken: '.$token.'" -F"file=@'.$file.'" -F"message='.$msg.'" "https://api.chatwork.com/v2/rooms/'.$room.'/files"',$c);

    }


function convert_ingo($price){
  $ingo = array();

  $ingo["1"] = "R";
  $ingo["2"] = "U";
  $ingo["3"] = "N";
  $ingo["4"] = "J";
  $ingo["5"] = "D";
  $ingo["6"] = "Z";
  $ingo["7"] = "I";
  $ingo["8"] = "X";
  $ingo["9"] = "A";
  $ingo["0"] = "S";
  $ingo[","] = "K";



  $price = number_format($price);



  if(($price != '') && ($price != 0)){
    //一文字ずつ配列に格納
    $price = str_split($price);
    $length = count($price);
    $ingo_result = "";
    if($length >= 3){
      //末３文字が全部ゼロだったら、空にする
      if(($price[$length-1] == "0") && ($price[$length-2] == "0") && ($price[$length-3] == "0")){
        $price[$length-1] = "";
        $price[$length-2] = "";
        $price[$length-3] = "";
        if($price[$length-4] == ","){
          $price[$length-4] = "";
        }
      }
    }
    if($length == 3){
      $length = 4;
      $price[3] = $price[2];
      $price[2] = $price[1];
      $price[1] = $price[0];
      $price[0] = ",";
    }
    if($length == 2){
      $length = 4;
      $price[3] = $price[1];
      $price[2] = $price[0];
      $price[1] = "0";
      $price[0] = ",";
    }
    if($length == 1){
      $length = 4;
      $price[3] = $price[0];
      $price[2] = "0";
      $price[1] = "0";
      $price[0] = ",";
    }

    //隠語配列に変換、結合
    for ($i=0; $i < $length; $i++) {
      // var_dump($price[$i]);
      if($price[$i] != ''){
        $price[$i] = $ingo[$price[$i]];
      }
      if($price[$i]){
        $ingo_result = $ingo_result.$price[$i];
      }
    }

    return $ingo_result;
  }else{
    return '';
  }
}




function create_ttl($res){
  $title = "";

  $fieldName = 'product_num'; $kugiri = " ";
  $mst_name = "mst_brand";  $mst_key = 'id';  $mst_sel_name = 'name';
  if(($res->$fieldName != 631) AND ($res->$fieldName != "")){
    $mst_data='';
    $mstdata = mst_brand::select($mst_sel_name)->where($mst_key,$res->$fieldName)->limit(1)->find_one();
    if($title != ""){$title .= $kugiri;}
    $title .= $mstdata->$mst_sel_name;
  }

  $gold_property = $res->gold_property;
  if($gold_property != ""){
    if($title != ""){ $title .= " ";}
    $title .= "{$res->gold_property}";
  }

  $fieldName = 'sub_category_id1';  $kugiri = " ";
  $mst_name = "store_sub_categories"; $mst_key = 'id';  $mst_sel_name = 'name';
  $mst_data='';
  $mstdata = store_sub_categories::select($mst_sel_name)->where($mst_key,$res->$fieldName)->limit(1)->find_one();
  if($title != ""){$title .= $kugiri;}
  $title .= $mstdata->$mst_sel_name;


  $comment = $res->comment;
  if($comment != ""){
    if($title != ""){ $title .= " ";}
    $title .= "{$res->comment}";
  }


  $fieldName = 'category_id'; $kugiri = " ";
  $mst_name = "store_categories"; $mst_key = 'category_id'; $mst_sel_name = 'category_name';
  $mst_data='';
  $mstdata = store_categories::select($mst_sel_name)->where($mst_key,$res->$fieldName)->limit(1)->find_one();
  if($title != ""){$title .= $kugiri;}
  $title .= $mstdata->$mst_sel_name;


  $Gram = $res->Gram;
  if($Gram != ""){
    if($title != ""){ $title .= " ";}
    $title .= "{$res->Gram}{$res->unit}";
  }

  $Parent_stone = $res->Parent_stone;
  if($Parent_stone != ""){
    if($title != ""){ $title .= " ";}
    $title .= "{$res->Parent_stone}ct";
  }

  $Aside_stone = $res->Aside_stone;
  if($Aside_stone != ""){
    if($title != ""){ $title .= " ";}
    $title .= "{$res->Aside_stone}ct";
  }

  $DA_SELFGRES = $res->DA_SELFGRES;
  if($DA_SELFGRES != ""){
    if($title != ""){ $title .= " ";}
    $title .= "{$res->DA_SELFGRES}";
  }



  $line = $res->line;
  if($line != ""){
    if($title != ""){ $title .= " ";}
    $title .= "{$res->line}";
  }

  $item_name = $res->item_name;
  if($item_name != ""){
    if($title != ""){ $title .= " ";}
    $title .= "{$res->item_name}";
  }

  $serial_number = $res->serial_number;
  if($serial_number != ""){
    if($title != ""){ $title .= " ";}
    $title .= "{$res->serial_number}";
  }

  $Remarks = $res->Remarks;
  if($Remarks != ""){
    if($title != ""){ $title .= "/";}
    $title .= "{$res->Remarks}";
  }

  return $title;
}
