<?php
include_once '/usr/local/src/google_api/idiorm_paris/app/config.php';

include_once '/usr/local/src/google_api/idiorm_paris/app/evav62122_audit.php';
include_once '/usr/local/src/google_api/idiorm_paris/app/evav62122_detailed_audit.php';

class Eoc_market_decline_alert extends Model{

  public static $_table = 'Eoc_market_decline_alert';    // table name (異なる場合オーバライドできる)
  public static $_id_column = 'id';     // primary key (同様)

  public function log_save($old_dataArray='', $user_login='', $log_table='', $log_action='', $log_save_table='')
  {
    $logText = '';
    $logText .= "---Keys\n";
    $logText .= "id : {$this->id}\n";

    $logText .= "---Fields\n";

    //■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
    //ログコード 商品
    //■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
    $new_dataArray = $this->as_array();
    $logText = "";
    if($log_action=="edit"){
      foreach ($new_dataArray as $data_key => $data_value) {
        if($data_value != $old_dataArray[$data_key]){
          if($old_dataArray[$data_key] == ""){
            $logText .= "{$data_key} [new]:{$data_value}\n";
          }else{
            $logText .= "{$data_key} [old]:{$old_dataArray[$data_key]}\n";
            $logText .= "{$data_key} [new]:{$data_value}\n";
          }
        }
      }
    }else{//add
      foreach ($new_dataArray as $data_key => $data_value) {
        $logText .= "{$data_key} [new]:{$data_value}\n";
      }
    }
    //■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
    //ログコード 商品 END
    //■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■


    //■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
    //ログコード 最終
    //■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
    if($logText != ""){
        $logFirst = "---Keys\n";
        $logFirst .= "id:{$this->id}\n";
        $logFirst .= "---Fields\n";
        $logText = $logFirst.$logText;

        if($log_save_table === ''){
          $log_save_table = 'evav62122_audit';
        }else{
          $log_save_table = $log_save_table;
        }

        $auditLog = $log_save_table::create();
        if(isset($_SERVER["REMOTE_ADDR"])){
          $auditLog->ip = $_SERVER["REMOTE_ADDR"];
        }else{
          $auditLog->ip = '127.0.0.1';
        }
        $auditLog->user = $user_login;
        $auditLog->table = $log_table;
        $auditLog->action = $log_action;
        $auditLog->description = $logText;
        $auditLog->datetime = date("Y-m-d H:i:s");

        $auditLog->save();
    }
    //■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
    //ログコード 最終 END
    //■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■

    try {
      $this->save();
      return true;
    } catch (Exception $e) {
      return $e->getMessage();
    }

  }// end log_save


}
