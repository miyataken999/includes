<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use DB;

class InterviewController extends Controller
{
    /**
     * 一覧取得
     */
    public function make_list_contents()
    {
        $interview = DB::table("internet.interview")
            ->where("is_open", true)
            ->whereRaw("open_at <= NOW() ")
            ->orderBy("open_at", "desc")
            ->paginate(20);


        $links = $interview->links();
        // $links = str_replace("https://rifa.life/refastaProject", "/refasta_wordpress", $links);
        // $links = str_replace("?page=", "?interview_test&page=", $links);
        $links = str_replace("https://rifa.life/refastaProject", "", $links);
        $links = str_replace("interview_list", "interview", $links);
        

        $array = array();
        $array["interview"] = $interview;
        $array["links"] = $links;
        return view("interview.interview_list.index", $array);

    }

    /**
     * interview_testつけることでプレビューできる
     */
    public function make_detail_contents($interview_url)
    {
        if(isset($_GET["interview_test"])){
            $interview = DB::table("internet.interview")
                ->where("url", $interview_url)
                ->first();
        }else{
            $interview = DB::table("internet.interview")
                ->where("url", $interview_url)
                ->where("is_open", true)
                ->first();
        }
        if($interview == false){
            return "";
        }

        $interview_sections = DB::table("internet.interview_sections")
            ->where("interview_id", (int) $interview->interview_id)
            ->orderBy("sort", "asc")
            ->orderBy("interview_section_id", "asc")
            ->get();


        $loop = 0;
        foreach ($interview_sections as $section) {
            $interview_contents = DB::table("internet.interview_contents")
                ->where("interview_section_id", (int) $section->interview_section_id)
                ->orderBy("sort", "asc")
                ->orderBy("interview_content_id", "asc")
                ->get();
            $interview_sections[$loop]->contents = $interview_contents;
            $loop++;
        }

        $array = array();
        $array["interview"] = $interview;
        $array["interview_sections"] = $interview_sections;
        return view("interview.interview_detail.index", $array);
    }

    /**
     * ヒーローエリアの作成
     */
    public function get_hero_area($interview_url)
    {
        $interview = DB::table("internet.interview")
            ->where("url", $interview_url)
            ->first();
        if($interview == false){
            return "";
        }
        $array = array();
        $array["interview"] = $interview;
        return view("interview.interview_detail.hero_area", $array);

    }

    /**
     * タイトル、ディスクリプションの生成
     */
    public function get_title_and_description($interview_url)
    {
        $interview = DB::table("internet.interview")
        ->where("url", $interview_url)
            ->first();
        if ($interview == false) {
            return "";
        }
        $array = array();
        $array["title"] = $interview->title;
        $array["description"] = $interview->description;
        $json = json_encode($array);
        return $json;


    }

    /**
     * 再利用ブロック用コンテンツ
     */
    public function make_block_contents()
    {
        $interviews = DB::table("internet.interview")
            ->where("is_open", true)
            ->orderBy("open_at", "desc")
            ->orderBy("interview_id", "desc")
            ->get();
        $array = array();
        $array["interviews"] = $interviews;
        return view("interview.reusable_block.index", $array);
    }

}
