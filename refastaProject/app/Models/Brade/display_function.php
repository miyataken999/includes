<?php
namespace App\Models\Brade;


use DB;
/**
 *
 */
class display_function
{
    /**
     * @param table 参照テーブル名
     * @param column [参照キー]カラム名
     * @param val [参照キー]参照元のvalue
     * @param return_col 表示させたいカラム名
     */
    function print_mst_name($table = "", $column = "", $val = "", $return_col = "")
    {
        if ($table == "") {
            return "";
        }
        if ($column == "") {
            return "";
        }
        if ($val == "") {
            return "";
        }
        if ($return_col == "") {
            return "";
        }
        $data = DB::table($table)->where($column, $val)->first();
        if ($data == false) {
            return "";
        }
        return $data->$return_col;
    }

    function print_interview_users_img($user_id = "", $img_name = "デフォルト")
    {
        if ($user_id == "") {
            return "";
        }
        
        $data = DB::table("mst_interview_users_img")
            ->where("mst_interview_user_id", $user_id)
            ->where("img_name", $img_name)
            ->first();
        if ($data == false) {
            $data = DB::table("mst_interview_users_img")
                ->where("mst_interview_user_id", $user_id)
                ->orderBy("sort", "asc")
                ->first();
        }
        if ($data == false) {
            return "https://rifa.life/refasta_wordpress/wp-content/uploads/2021/07/%E3%83%AD%E3%82%B3%E3%82%99.jpg";
        }
        return $data->img_url;
    }


    function change_html_format($text="")
    {
        $text = str_replace("\n", "<br>", $text);
        //URL抽出
        if (preg_match_all('(https?://[-_.!~*\'()a-zA-Z0-9;/?:@&=+$,%#]+)', $text, $result) !== false) {
            foreach ($result[0] as $link_text) {
                //URL表示
                if (strstr($text, $link_text)) {
                    $info = new \SplFileInfo($link_text);
                    $ext = $info->getExtension();
                    if(
                        ($ext == "jpg")
                        OR ($ext == "png")
                        or ($ext == "gif")
                        or ($ext == "webp")
                    ){
                        $text = str_replace($link_text, '<img src="' . $link_text . '" >', $text);
                    }else{
                        $text = str_replace($link_text, '<a href="' . $link_text . '" target="_blank">' . $link_text . '</a>', $text);
                    }

                    
                }
            }
        }
        return $text;

    }
}

