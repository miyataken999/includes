<?php 
//スプレッドシートAPIクライアント読み込み
include_once "/home/apache/vendor/autoload.php";
include_once '/usr/local/src/google_api/idiorm_paris/app/lounge_report__buy.php';

use Carbon\Carbon;
use lounge_report__buy;

class write_gss
{
    public $target_month = "";
    public $last_date = "";
    public $select_year = "";
    public $select_month = "";
    public $sheet_name = "";
    public $range = "";
    public $write_array = [];
    public $write_array_2 = [];
    public $service;
    public $spreadsheetId = '1-6jwthSHoQ4U86OMQMO6oY2GSkQLARDwqsQwMaTvRKk'; // 書き込みシート
    public $spreadsheet_service;
    public $start_row;
    public $end_row;


    /**
     */
    public function sheets_batch($write_array, $start_write_column, $end_write_column, $sheet_name)
    {
        // yearとmonthから開始行を求める
        $daysFromJanuary1 = $this->daysFromJanuary1();
        $this->start_row = $daysFromJanuary1 + 4;
        // 与えられた年月が何日あるか計算して終了行を求める
        $day = Carbon::create($this->select_year, $this->select_month, 1);
        $daysInMonth = $day->daysInMonth;
        $this->end_row = $this->start_row + $daysInMonth;

        // 日付を保存する配列
        $month_array = [];

        $start_date = $day->copy();
        $end_date = Carbon::parse($day)->endOfMonth();


        while ($start_date->lte($end_date)) {
          $month_array[] = $start_date->format('Y-m-d');
          $start_date->addDay();
        }
        $this->write_to_gss($write_array, $start_write_column, $end_write_column, $sheet_name);
        echo('全ての書き込みが完了しました。<br>');
    }

    /**
     * 1月１日から何日かを取得
     * @return int $days
     */
    function daysFromJanuary1() {
        // 書き込みGSS上の最初（2023年）の1月1日の日付を作成
        $january1 = Carbon::createFromDate(2023, 1, 1);

        // 指定された年月の日付を作成
        $specifiedDate = Carbon::createFromDate($this->select_year, $this->select_month, 1);

        // 日数を計算
        $interval = $january1->diff($specifiedDate);
        $days = $interval->days;

        return $days;
    }


        /**
     * gssに書き込む
     * @param $array_to_write
     * @param $total_count_column
     * @param $total_price_column
     * @return void
     * @throws Exception
     */
    function write_to_gss ($array_to_write, $start_write_column, $end_write_column, $sheet_name = '') {
        echo($start_write_column.$this->start_row . 'からの書き込みが' . $this->write($this->spreadsheetId, $sheet_name, $start_write_column.$this->start_row, $end_write_column.$this->end_row, $array_to_write));
      }

      
      public function write(string $spread_sheet_id, string $sheet_name, string $start_cell, string $end_cell, array $array_to_write): string
      {
          try {
              $values = $array_to_write;
              $spreadsheet_id = $spread_sheet_id;
              $range = $sheet_name . '!' . $start_cell . ':' . $end_cell;
              $body = new \Google_Service_Sheets_ValueRange([
                  'values' => $values
              ]);
              $body->setValues($values);
              $params = ['valueInputOption' => 'USER_ENTERED'];   // データの入力方法 ※
              $result = $this->spreadsheet_service->spreadsheets_values->update(
                  $spreadsheet_id,
                  $range,
                  $body,
                  $params
              );
              // 制限があるため1秒まつ
              sleep(1);
              return '完了しました！<br>';
  
          } catch (\Exception $e) {
              // エラー処理
              return $e->getMessage();
          }
      }
  

}
