<?php
# Includes the autoloader for libraries installed with composer
require '/home/apache190211/vendor/autoload.php';

# Imports the Google Cloud client library
use Google\Cloud\Language\LanguageClient;

# Your Google Cloud Platform project ID
$projectId = 'innate-empire-193705';


// include "/usr/local/src/google_api/natural_language/jogai.php";
require "jogai.php";

// ini_set("display_errors",1);

function replacePartOfSpeech($category , $str){
    $Tag = array(
		"UNKNOWN" => "no", //"不明",
		"ADJ" => "ok", //"形容詞",
		"ADP" => "ok", //"前置詞（前置詞と後置詞）",
		"ADV" => "ok", //"副詞",
		"CONJ" => "ok", //"接続詞",
		"DET" => "no", //"決定子",
		"NOUN" => "ok", //"名詞",
		"NUM" => "ok", //"基数",
		"PRON" => "ok", //"代名詞",
		"PRT" => "no", //"助詞またはその他の機能語",
		"PUNCT" => "no", //"句読点",
		"VERB" => "no", //"動詞（すべての時制とモード）",
		"X" => "no", //"その他",
		"AFFIX" => "ok", //"接辞",
    );
    $ret = $$category[$str];
    return $ret;
}



function analyze_syntax($text, $projectId = null, $jogai = array())
{
    // Create the Natural Language client
    $language = new LanguageClient([
        'projectId' => $projectId,
    ]);

	$options["language"] = 'ja';
    // Call the analyzeSyntax function
    $annotation = $language->analyzeSyntax($text,$options);

    // Print syntax information. See https://cloud.google.com/natural-language/docs/reference/rest/v1/Token
    // to learn about more information you can extract from Token objects.
    $tokens = $annotation->tokens();

    $return = '';
    $br_flag = 'off';
    foreach ($tokens as $token) {
    	// $token['text']['content'] = trim($token['text']['content']);
    	if($token['text']['content'] == ''){
    		continue;
    	}
    	$jogai_flag = '';
    	if(in_array($token['text']['content'],$jogai,true)){
    		continue;
    	}


        $category = 'Tag';
        $a = replacePartOfSpeech($category, $token['partOfSpeech']['tag']);

		if(($a == 'no') OR ($token['text']['content'] == "■■")){
			if($return != ''){
				if($br_flag == 'off'){
					$br_flag = 'on';
					$return .= "\n";
				}
			}
			// $return .= $token['text']['content'];
			// $return .= "【{$a}】";
		}else{
			$br_flag = 'off';
			$return .= $token['text']['content'];
			// $return .= "【".$token['partOfSpeech']['tag']."】";

			// $return .= "【{$a}】";
		}


    }// end foreach


    $return = explode("\n",$return);
    $return = array_unique($return);
    $return = array_values($return);
    $return = implode("\n",$return);

    $return = trim($return);
    return $return;

}


$text = $_POST["text"];
$res = analyze_syntax($text, $projectId, $jogai);

// $res = array("body"=>$res);
// $res = json_encode($res);

echo $res;
