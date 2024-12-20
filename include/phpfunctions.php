<?php
class ErrorHandler
{
	var $errorstack = array();
	function handle_mail_error($errno, $errstr, $errfile, $errline)
	{
		if(strpos($errstr,"It is not safe to rely on the system's timezone settings."))
			return;
		$this->errorstack []= array('number' => $errno, 'description' => $errstr, 'file' => $errfile, 'line' => $errline);
	}
	function getErrorMessage()
	{
		$msg="";
		foreach($this->errorstack as $err)
		{
			if($msg)
				$msg.="\r\n";
			$msg.=$err['description'];
		}
		return $msg;
	}
}

class facebookWrapper
{
	var $fbObj = null;

	function __construct()
	{
		include_once('plugins/facebook/facebook.php');
		$this->fbObj = new Facebook(array(
			'appId'  => GetGlobalData("FBappId",""),
			'secret' => GetGlobalData("FBappSecret","")
		));

	}

	function getCookieName()
	{
		return "fbsr_" . GetGlobalData("FBappId","");
	}

	function FBgetUserInfo() {
		$fbme = array();
		try {
			$uid = $this->fbObj->getUser();
			$fbme = $this->fbObj->api('/'.$uid);
		} catch (FacebookApiException $e) {
			runner_print_r($e);
		}
		return $fbme;
	}

	function FBgetLoginUrl($params=array()){
		$url = "http://";
		if( $_SERVER["HTTPS"] && $_SERVER["HTTPS"] != "off")
			$url = "https://";
		$url .= $_SERVER["HTTP_HOST"] . substr($_SERVER['REQUEST_URI'], 0, strrpos($_SERVER['REQUEST_URI'], '/') + 1) .GetTableLink("login");
		return $this->fbObj->getLoginUrl(array('display'=>'popup', 'redirect_uri'=>$url));
	}

	function FBgetSignedRequest(){
		return $this->fbObj->getSignedRequest();
	}

	function FBgetAppId(){
		return $this->fbObj->getAppId();
	}
}

/**
	PHPRunner wrapper for mail() function.
	$params array Input paramaters.
	The following parameters are supported:
	'from' Sender email address. If none specified an email address from the wizard will be used.
	'to' Receiver email address.
	'body' Plain text message body.
	'htmlbody' Html message body (do not use 'body' parameter in this case).
	'charset' Html message charset. If none specified the default website charset will be used.

	Returns array with data:
	"mailed" - indicates wheter mail sent or not
	"errors" - array of errors
		Each error is an array with the following keys:
		"number" - error number
		"description" - error description
		"file" - name of the php file in which error happened
		"line" - line number on which error happened

 * @param Array params
 * @return Array
 * @intellisense
 */
function runner_mail( $params )
{
	if( !GetGlobalData("useBuiltInMailer", false) )
	{
		include_once(getabspath('libs/phpmailer/class.phpmailer.php'));
		include_once(getabspath('libs/phpmailer/class.smtp.php'));
		return runner_mail_smtp($params);
	}

	$from = isset($params['from']) ? $params['from'] : "";
	if( !$from )
		$from = GetGlobalData("strFromEmail", "");
	
	$to = isset($params['to']) ? $params['to'] : "";
	$body = isset($params['body']) ? $params['body'] : "";
	$cc = isset($params['cc']) ? $params['cc'] : "";
	$bcc = isset($params['bcc']) ? $params['bcc'] : "";
	$replyTo = isset($params['replyTo']) ? $params['replyTo'] : "";
	$priority = isset($params['priority']) ? $params['priority'] : "";
	
	$charset = "";
	$isHtml = false;
	if( !$body )
	{
		$body = isset($params['htmlbody']) ? $params['htmlbody'] : "";
		$charset = isset($params['charset']) ? $params['charset'] : "";
		if( !$charset )
			$charset = "utf-8";
		$isHtml = true;
	}
	$subject = $params['subject'];

	//
	$header = "";
	if( $isHtml )
	{
		$header .= "MIME-Version: 1.0\r\n";
		$header .= 'Content-Type: text/html;' . ( $charset ? ' charset=' . $charset . ';' : '' ) . "\r\n";
	}

	if( $from )
	{
		if( strpos($from, '<') !== false )
			$header .= 'From: ' . $from . "\r\n";
		else
			$header .= 'From: <' . $from . ">\r\n";

		@ini_set("sendmail_from", $from);
	}
	
	if($cc)
		$header .= 'Cc: ' . $cc . "\r\n";
	
	if($bcc)
		$header .= 'Bcc: ' . $bcc . "\r\n";

	if ($priority)
		$header .= 'X-Priority: '.$priority."\r\n";

	if($replyTo)
	{
		if( strpos($replyTo, '<') !== false )
			$header .= 'Reply-to: '.$replyTo."\r\n";
		else
			$header .= 'Reply-to: <'.$replyTo.">\r\n";
	}

	$eh = new ErrorHandler();
	set_error_handler(array($eh, "handle_mail_error"));

	$res = false;
	if( !$header )
	{
		$res = mail($to, $subject, $body);
	}
	else
	{
		$res = mail($to, $subject, $body, $header);
	}

	restore_error_handler();
	return array("mailed" => $res, "errors" => $eh->errorstack, "message"=> nl2br( $eh->getErrorMessage() ));
}

/**
 * Gets absolute path
 *
 * @param string $path
 * @return string
 * @intellisense
 */
function getabspath($path)
{
	// get path to the root
	$pathToRoot = substr(dirname(__FILE__),0,strlen(dirname(__FILE__))-strlen("/include"));
	// cheks if there already we have absolute path
	if ($pathToRoot=="" || strpos($path, $pathToRoot) !== false)
		return $path;

	// add \ or / if needed
	if (substr($path, 0, 1) != "/" && substr($path, 0, 1) != "\\")
		$pathToRoot .= "/";

	$realPath = $pathToRoot.$path;
	return $realPath;
}

/**
 * Check if the path is absolute or not basing on data
 * obtained from the current file directory's path
 * @param String path
 * @return Boolean
 */
function isAbsolutePath($path)
{
	if( runner_substr($path, 0, 2) == "\\\\"  )
		return true;

	$pathToRoot = dirname(__FILE__);

	if( runner_substr($path, 0, 1) == "\/" && runner_substr($pathToRoot, 0, 1) == "\/")
		return true;

	$windowsPatter = '/^[a-z]{1}:\\\/i';
	if( preg_match($windowsPatter, $pathToRoot) &&  preg_match($windowsPatter, $path))
		return true;

	return false;
}

/**
 * Gets absolute url
 *
 * @param string $uri
 * @return string
 * @intellisense
 */
function getabsurl($uri)
{
	$here = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	$realUrl = preg_replace('~#.*$~s', '', $here);
	$realUrl = preg_replace('~\?.*$~s', '', $realUrl);
	$realUrl = preg_replace('~/[^/]*$~s', '/'.$uri, $realUrl);
	return $realUrl;
}

/**
 * @intellisense
 */
function myfile_exists($filename)
{
	$file = @fopen($filename,"rb");
	if($file)
	{
		fclose($file);
		return true;
	}
	else
		return false;
}

/**
 * @intellisense
 */
function try_create_new_file($filename)
{
	$file = @fopen($filename,"x");
	if($file)
	{
		fclose($file);
		return true;
	}
	else
		return false;
}

function append_to_file( $filename, $str )
{
	$h = @fopen( $filename, "a" );
	if( !$h )
		return;
	fputs( $h, $str );
	fclose( $h );
}

//	read the whole file and return contents
/**
 * @intellisense
 */
function myfile_get_contents($filename, $mode = "rb")
{
	if(!is_uploaded_file($filename) && !file_exists($filename))
		return false;
	$handle = fopen($filename, $mode);
	if(!$handle)
		return false;
	fseek($handle, 0 , SEEK_END);
	$fsize = ftell($handle);
	fseek($handle, 0 , SEEK_SET);

	if($fsize)
		$contents = fread($handle, $fsize);
	else
		$contents="";
	fclose($handle);
	return $contents;
}

/**
 * @intellisense
 */
function myurl_get_contents($url)
{
	return file_get_contents($url);
}

/**
 * @intellisense
 */
function printfile($filename)
{
	$file = fopen($filename, "rb");
	$bufsize = 8*1024;
	while(!feof($file))
		echo fread($file, $bufsize);
	fclose($file);
}

/**
 * @intellisense
 */
function printfileByRange($filename, $startPos, $endPos)
{
	$fileSize = filesize($filename);
	$length = $endPos - $startPos + 1;
	$file = fopen($filename, "rb");
	if(fseek($file, $startPos) == 0)
	{
		$bufsize = 8*1024;
		if($length < $bufsize)
			$bufsize = $length;
		$totalRead = 0;
		while(!feof($file) && $totalRead < $length)
		{
			//reset time limit for big files
        	set_time_limit(0);
			print(fread($file, $bufsize));
			flush();
			if(ob_get_level())
        		ob_flush();
			$totalRead += $bufsize;
			if($totalRead + $bufsize > $length)
				$bufsize = $length - $totalRead;
		}
	}
	fclose($file);
}

/**
 * @intellisense
 */
function CreateThumbnail($value, $size, $ext)
{
	if(!function_exists("imagecreatefromstring"))
		return $value;

	$error_handler=set_error_handler("empty_error_handler");
	$image = imagecreatefromstring($value);
	if($error_handler)
		set_error_handler($error_handler);

	if(!$image)
		return $value;

	$width_old = imagesx($image);
	$height_old = imagesy($image);

	if($width_old>$size || $height_old>$size){
		if($width_old>=$height_old)
		{
			$final_height=(integer)($height_old*$size/$width_old);
			$final_width=$size;
		}
		else
		{
			$final_width=(integer)($width_old*$size/$height_old);
			$final_height=$size;
		}


	    $image_resized = imagecreatetruecolor( $final_width, $final_height );

	    if ($ext==".GIF" || $ext=="GIF" || $ext==".PNG" || $ext=="PNG") {
	      $trnprt_indx = imagecolortransparent($image);

	      // If we have a specific transparent color
	      if ($trnprt_indx >= 0) {

	     	// when index more than imagecolorstotal may occurs problems with gif
		    $totalColors = imagecolorstotal($image);
		    if ($trnprt_indx>=$totalColors && $totalColors>0){
		    	$trnprt_indx = imagecolorstotal($image)-1;
		    }

	        // Get the original image's transparent color's RGB values
	        $trnprt_color    = imagecolorsforindex($image, $trnprt_indx);

	        // Allocate the same color in the new image resource
	        $trnprt_indx    = imagecolorallocatealpha($image_resized, $trnprt_color['red'], $trnprt_color['green'], $trnprt_color['blue'],127);
	        $trnprt_indx    = imagecolorallocate($image_resized, 255,255,255);
	        // Completely fill the background of the new image with allocated color.
	        imagefill($image_resized, 0, 0, $trnprt_indx);

	        // Set the background color for new image to transparent
	        imagecolortransparent($image_resized, $trnprt_indx);

	      }
	      // Always make a transparent background color for PNGs that don't have one allocated already
	      elseif ($ext==".PNG" || $ext=="PNG") {

	        // Turn off transparency blending (temporarily)
	        imagealphablending($image_resized, false);

	        // Create a new transparent color for image
	        $color = imagecolorallocatealpha($image_resized, 0, 0, 0, 127);

	        // Completely fill the background of the new image with allocated color.
	        imagefill($image_resized, 0, 0, $color);

	        // Restore transparency blending
	        imagesavealpha($image_resized, true);
	      }
	    }

	 	imagecopyresampled($image_resized, $image, 0, 0, 0, 0, $final_width, $final_height, $width_old, $height_old);

	    ob_start();
		if($ext==".JPG" || $ext=="JPEG")
			imagejpeg($image_resized);
		elseif($ext==".PNG")
			imagepng($image_resized);
		else
			imagegif($image_resized);
		$ret=ob_get_contents();
		ob_end_clean();
		imagedestroy($image);
		imagedestroy($image_resized);
		return $ret;
	}
	imagedestroy($image);
	return $value;

}
/**
 * @intellisense
 */
function mysprintf($format, $params)
{
	$params2 = $params;
	array_unshift($params2, $format);
	return call_user_func_array('sprintf', $params2);
}

/**
 * @intellisense
 */
function now()
{
	return strftime("%Y-%m-%d %H:%M:%S");
}

/**
 * refine value passed by POST or GET method
 * @intellisense
 */
function refine($str)
{
	if(get_magic_quotes_gpc())
		return stripslashes($str);
	return $str;
}

/**
 * suggest image type by extension
 * @intellisense
 */
function SupposeImageType($file)
{
	if(strlen($file)>1 && $file[0]=='B' && $file[1]=='M')
		return "image/bmp";
	if(strlen($file)>2 &&  $file[0]=='G' && $file[1]=='I' && $file[2]=='F')
		return "image/gif";
	if(strlen($file)>3 &&  ord($file[0])==0xff && ord($file[1])==0xd8 && ord($file[2])==0xff)
		return "image/jpeg";
	if(strlen($file)>8 &&  ord($file[0])==0x89 && ord($file[1])==0x50 && ord($file[2])==0x4e && ord($file[3])==0x47
					   &&  ord($file[4])==0x0d && ord($file[5])==0x0a && ord($file[6])==0x1a && ord($file[7])==0x0a)
		return "image/png";
}

/**
 * @intellisense
 */
function prepare_file($value,$field,$controltype,$postfilename, $id)
{
	$filename="";
	$file=&$_FILES["value_".GoodFieldName($field)."_".$id];
	if($file["error"] && $file["error"]!=4)
		return false;
	if(trim($postfilename))
		$filename=refine(trim($postfilename));
	else
		$filename=$file['name'];
	if(substr($controltype,4,1)=="1")
	{
		$filename="";
		return array("filename" => "", "value" => "");
	}
	if(substr($controltype,4,1)=="0")
		return false;
	$ret=myfile_get_contents($file['tmp_name']);
	if(!$ret)
		return false;
	return array("filename" => $filename, "value" => $ret);
}

/**
 * @intellisense
 */
function prepare_upload($field, $controltype, $postfilename, $value, $table, $id, &$pageObject)
{
	$abs = $pageObject->pSet->isAbsolute($field);
	$file=&$_FILES["value_".GoodFieldName($field)."_".$id];
	$sbstr1 = substr($controltype,6,1);
	if($file["error"] || $value == "")
	{
		if($file["error"] != 4  && $sbstr1 != "1")
		return false;
	}
	if($sbstr1 == "1")
	{
		if(strlen($postfilename))
		{
			$pageObject->filesToDelete[]=new DeleteFile($postfilename, $pageObject->pSet->getUploadFolder($field), $abs);
			if($pageObject->pSet->getCreateThumbnail($field,$table))
				$pageObject->filesToDelete[]=new DeleteFile($pageObject->pSet->getStrThumbnail($field).$postfilename, $pageObject->pSet->getUploadFolder($field), $abs);
		}
		return "";
	}
	if(substr($controltype,6,1)=="0")
		return false;
	if(strlen($file['tmp_name']))
	{
		if(!$pageObject->pSet->getResizeOnUpload($field))
		{
			$pageObject->filesToMove[] = new MoveFile($file['tmp_name'],$value, $pageObject->pSet->getUploadFolder($field),$abs);
		}
		else
		{
			$contents = myfile_get_contents($file['tmp_name']);
			$ext = CheckImageExtension($file["name"]);
			$thumb = CreateThumbnail($contents, $pageObject->pSet->getNewImageSize($field), $ext);
			$pageObject->filesToSave[] = new SaveFile($thumb,$value, $pageObject->pSet->getUploadFolder($field),$abs);
		}
	}
	return $value;
}

/**
 * @intellisense
 */
function FieldSubmitted($field)
{
	return in_assoc_array("type_".GoodFieldName($field),$_POST) || in_assoc_array("value_".GoodFieldName($field),$_POST)
		|| in_assoc_array("value_".GoodFieldName($field),$_FILES);
}

/**
 * @intellisense
 */
function GetUploadedFileContents($name)
{
	return myfile_get_contents($_FILES[$name]['tmp_name']);
}

/**
 * @intellisense
 */
function GetUploadedFileName($name)
{
	return $_FILES[$name]["name"];
}

/**
 * @param &Array values
 * @param &Array blobfields
 * @param RunnerPage pageobject
 * @return Array
 * @intellisense
 */
function PrepareBlobs(&$values, &$blobfields, $pageObject)
{
	$blobs = array();

	if( $pageObject->connection->dbType == nDATABASE_Oracle || $pageObject->connection->dbType == nDATABASE_DB2 || $pageObject->connection->dbType == nDATABASE_Informix )
	{
		//	replace blobs with EMPTY_BLOB()
		foreach($blobfields as $bfield)
		{
			$blobs[ $pageObject->getTableField( $bfield ) ] = $values[ $bfield ];

			if( $pageObject->connection->dbType == nDATABASE_Oracle )
				$values[ $bfield ] = "EMPTY_BLOB()";
			else
				$values[ $bfield ] = "?";
		}
	}
	else
	{
		//	no special processing required
		$blobfields = array();
	}

	return $blobs;
}

/**
 * @intellisense
 * @param RunnerPage pageObj
 * @param String strSQL
 * @param &Array blobs
 */
function ExecuteUpdate( &$pageObj, $strSQL, &$blobs )
{
	$blobTypes = array();
	if( $pageObj->connection->dbType == nDATABASE_Informix )
	{
		foreach($blobs as $fieldname => $fieldvalue)
		{
			$blobTypes[ $fieldname ] = $pageObj->pSet->getFieldType( $fieldname );
		}
	}
	LogInfo($strSQL);
	if( !$pageObj->connection->execWithBlobProcessing( $strSQL, $blobs, $blobTypes ) )
	{
		$pageObj->setDatabaseError( $pageObj->connection->lastError() );
		return false;
	}
	return true;
}

/**
 * @intellisense
 */
function runner_move_uploaded_file($source, $dest)
{
	move_uploaded_file($source, $dest);
}

/**
 * @intellisense
 */
function runner_save_file($filename, $contents)
{
	if(file_exists($filename))
		@unlink($filename);
	$th = fopen($filename, "w");
	fwrite($th, $contents);
	fclose($th);
}

/**
 * @intellisense
 */
function runner_delete_file($file)
{
	if(myfile_exists($file))
		@unlink($file);
}

/**
 * @intellisense
 */
function runner_copy_file($source, $dest)
{
	copy($source, $dest);
}

/**
 * @intellisense
 */
function GetCurrentYear()
{
	$tm=localtime(time(),true);
	return $tm["tm_year"]+1900;
}

/**
 * @intellisense
 */
function sortMembers(&$arr)
{
	usort($arr, "sortfunc_members");
}

/**
 * @intellisense
 */
function sortfunc_members(&$a,&$b)
{
	global $sortgroup,$sortorder;
	$gcount=count($a["usergroup_boxes"]["data"]);
	for($i=0;$i<$gcount;$i++)
		if($a["usergroup_boxes"]["data"][$i]["usergroup_box"]["data"][0]["group"]==$sortgroup)
			break;
	if($i==$gcount || $a["usergroup_boxes"]["data"][$i]["usergroup_box"]["data"][0]["checked"]==$b["usergroup_boxes"]["data"][$i]["usergroup_box"]["data"][0]["checked"])
	{
//	compare by username
		if($a["user"]==$b["user"])
			return 0;
		if($a["user"]>$b["user"])
			return 1;
		return -1;
	}
	if($sortorder=="a" && $a["usergroup_boxes"]["data"][$i]["usergroup_box"]["data"][0]["checked"]=="")
		return 1;
	if($sortorder=="d" && $b["usergroup_boxes"]["data"][$i]["usergroup_box"]["data"][0]["checked"]=="")
		return 1;
	return -1;
}

/**
 * return refined POST or GET value - single value or array
 * @intellisense
 */
function postvalue($name)
{
	if(isset($_POST[$name]))
		$value=$_POST[$name];
	else if(isset($_GET[$name]))
		$value=$_GET[$name];
	else
		return "";
	if(!is_array($value))
		return refine($value);
	$ret=array();
	foreach($value as $key=>$val)
		$ret[$key]=refine($val);
	return $ret;
}

function getCustomMapIcon($field, $table, $data)
{
	global $strTableName;
	$icon = "";
	if(!$table)
		$table = $strTableName;


	return $icon;
}

function getDashMapCustomIcon( $dashName, $dashElementName, $data )
{
	$icon = "";
}

/**
 * return custom expression
 * @intellisense
 */
function CustomExpression($value, $data, $field, $ptype, $table="")
{
	global $strTableName;
	if(!$table)
		$table = $strTableName;
				if($table=="Eoc" && $field=="kaitori_sougak")
	{
		$value =  number_format($data["kaitori_sougak"]);;
		return $value;
	}
				if($table=="Eoc" && $field=="sougaku")
	{
		$value =  number_format($value);;
		return $value;
	}
				if($table=="Eoc" && $field=="mypage_id")
	{
		if($value > 0){
	$value = "<a href='https://urlounge.co.jp/MyPage/users_list.php?q=(id~equals~{$value})'>Mypageリンク</a>";
}

;
		return $value;
	}
				if($table=="Eoc" && $field=="event")
	{
		

include $_SERVER["DOCUMENT_ROOT"].'/include/page_event/eoc/list/events/index.php';
;
		return $value;
	}
				if($table=="Eoc" && $field=="btns")
	{
		

include $_SERVER["DOCUMENT_ROOT"].'/include/page_event/eoc/list/btns/index.php';
;
		return $value;
	}
				if($table=="Eoc" && $field=="insert_btns")
	{
		

include $_SERVER["DOCUMENT_ROOT"].'/include/page_event/eoc/list/insert_btns/index.php';
;
		return $value;
	}
				if($table=="PMT_EV001" && $field=="DA_WEIGHT")
	{
		$value =  floor($data["DA_WEIGHT"] * 1000) / 1000  ;;
		return $value;
	}
				if($table=="PMT_EV001" && $field=="DA_THESPECIFICATIONSLOSSES")
	{
		$value = number_format($data["DA_THESPECIFICATIONSLOSSES"]);;
		return $value;
	}
				if($table=="PMT_EV001" && $field=="DA_PAYOUTOTHER")
	{
		$value = floor($value);;
		return $value;
	}
				if($table=="PMT_EV001" && $field=="DA_INTEREST_1")
	{
		//DA_INTEREST_1
if($data["DA_INTEREST_1"] < 0)
	$value = "<font color=red>".$data["DA_INTEREST_1"]."</font>";
$value = number_format($data["DA_INTEREST_1"]);
;
		return $value;
	}
				if($table=="PMT_EV001" && $field=="DA_UNIT_PRICE_UNPLUG")
	{
		//DA_UNIT_PRICE_UNPLUG
$value = floor($data["DA_UNIT_PRICE_UNPLUG"]);;
		return $value;
	}
				if($table=="Eoc_chigins2" && $field=="status")
	{
		if($value == '未売却'){
	$value = '成約処理';
};
		return $value;
	}
				if($table=="shouhin" && $field=="link_id")
	{
		$value = "<a href='/include/raku_cabinet_shop5/cabinet_sansyou.php?box_id={$value}' target='_blank'>出品ZIP</a>";
;
		return $value;
	}
				if($table=="shouhin" && $field=="price")
	{
		$value = number_format($value);;
		return $value;
	}
				if($table=="shouhin" && $field=="sales_price")
	{
		$value = number_format($value);;
		return $value;
	}
				if($table=="shouhin" && $field=="nyuukin_price")
	{
		$value = number_format($value);;
		return $value;
	}
				if($table=="shouhin" && $field=="price_without_tax")
	{
		include $_SERVER["DOCUMENT_ROOT"]."/include/page_event/shouhin/list/price_without_tax.php";;
		return $value;
	}
				if($table=="shouhin" && $field=="sales_date")
	{
		include $_SERVER["DOCUMENT_ROOT"]."/include/page_event/shouhin/list/sales_date.php";;
		return $value;
	}
				if($table=="saisun" && $field=="link")
	{
		$value = '<a href="http://rifa.life/evaProject/saisun/edit/'.$value.'" >新採寸</a>';;
		return $value;
	}
				if($table=="saisun" && $field=="chigin_id")
	{
		if($value > 0){
	$query = "SELECT chigin_id FROM Eoc_chigins2 WHERE product_id={$value} LIMIT 1";
	$rs = CustomQuery($query);
	$data = db_fetch_array($rs);
	$value = $data["chigin_id"];
};
		return $value;
	}
				if($table=="saisun" && $field=="product_num")
	{
		$value = $data["yahoo_junle"];;
		return $value;
	}
				if($table=="saisun" && $field=="ecc_id1")
	{
		$value = $value."<br>".$data["name2"]."<br>".$data["name1"];;
		return $value;
	}
				if($table=="vw_shuukei_st" && $field=="link")
	{
		$value = "<a href='https://rifa.life/shop5/shouhin_list.php?q=(status~equals~" . $value . ")'>商品一覧</a>";;
		return $value;
	}
				if($table=="vw_shuukei_st" && $field=="nyuukin_price")
	{
		$value = number_format($value);;
		return $value;
	}
				if($table=="vw_shuukei_st" && $field=="avg_nyuukin_price")
	{
		$value = number_format($value);;
		return $value;
	}
				if($table=="vw_shuukei_st" && $field=="price")
	{
		$value = number_format($value);;
		return $value;
	}
				if($table=="vw_shuukei_st" && $field=="avg_price")
	{
		$value = number_format($value);;
		return $value;
	}
				if($table=="auctions" && $field=="zip_link")
	{
		$value = "<a href='/include/raku_cabinet_shop5/cabinet_sansyou.php?box_id={$value}' target='_blank'>出品ZIP</a>";;
		return $value;
	}
				if($table=="admin_users" && $field=="user_password")
	{
		$value = "*********";;
		return $value;
	}
				if($table=="mst_item_name" && $field=="sum_nyuukin_price")
	{
		include $_SERVER["DOCUMENT_ROOT"].'/include/page_event/mst_item_name/list/sum_nyuukin_price/index.php';

;
		return $value;
	}
				if($table=="Eoc_takuhai" && $field=="link_area")
	{
		include $_SERVER["DOCUMENT_ROOT"].'/include/page_event/eoc_takuhai/list/link/index.php';
;
		return $value;
	}
				if($table=="mst_brand" && $field=="tag_img")
	{
		$img = $data["tag_img"];
if($img != ''){
	$value = '<a href="'.$img.'"><img src="'.$img.'" width="100px"></a>';
}
;
		return $value;
	}
				if($table=="mst_brand" && $field=="sum_nyuukin_price")
	{
		
include $_SERVER["DOCUMENT_ROOT"].'/include/page_event/mst_brand/list/sum_nyuukin_price/index.php';
;
		return $value;
	}
				if($table=="vw_box_csv" && $field=="yahoo_junle")
	{
		$tmp_array = explode('/',$value);
$value = $tmp_array[1];;
		return $value;
	}
				if($table=="Eoc_unfinished" && $field=="many_satei_btn")
	{
		include $_SERVER["DOCUMENT_ROOT"].'/include/page_event/Eoc_unfinished/list/many_satei/index.php';;
		return $value;
	}
				if($table=="Eoc_unfinished" && $field=="sheet_url")
	{
		$link = $value."#gid=".$data["sheet_id"];

$value = "<a href='{$link}' target='_blank' >{$link}</a>";


;
		return $value;
	}
				if($table=="dia" && $field=="price")
	{
		$value = number_format($value);;
		return $value;
	}
				if($table=="dia" && $field=="nyuukin_price")
	{
		$value = number_format($value);;
		return $value;
	}
				if($table=="dia" && $field=="DA_INTEREST_1")
	{
		//DA_INTEREST_1
if($data["DA_INTEREST_1"] < 0)
	$value = "<font color=red>".$data["DA_INTEREST_1"]."</font>";
$value = number_format($data["DA_INTEREST_1"]);
;
		return $value;
	}
				if($table=="kin_satei" && $field=="price_per_gram")
	{
		$value = number_format($value);;
		return $value;
	}
				if($table=="kin_satei" && $field=="price_per_parent_stone")
	{
		$value = number_format($value);;
		return $value;
	}
				if($table=="kin_satei" && $field=="price")
	{
		$value = number_format($value);;
		return $value;
	}
				if($table=="kin_satei" && $field=="sales_price")
	{
		$value = number_format($value);;
		return $value;
	}
				if($table=="kin_satei" && $field=="nyuukin_price")
	{
		$value = number_format($value);;
		return $value;
	}
				if($table=="Eoc_catch" && $field=="space_2")
	{
		include $_SERVER["DOCUMENT_ROOT"]."/include/page_event/eoc_catch/edit/space_2.php";
;
		return $value;
	}
				if($table=="Eoc_catch" && $field=="space_10")
	{
		$value = '<img src="http://rifa.life/lounge_API/qr_code/img.php?text='.$data["ecc_id"].'" />';
;
		return $value;
	}
				if($table=="Eoc_catch" && $field=="idfile_check_area")
	{
		include $_SERVER["DOCUMENT_ROOT"]."/include/page_event/eoc_catch/edit/idfile_check/index.php";;
		return $value;
	}
				if($table=="Eoc_takuhai_for_catch" && $field=="link_area")
	{
		include $_SERVER["DOCUMENT_ROOT"].'/include/page_event/eoc_takuhai/list/link/index.php';;
		return $value;
	}
				if($table=="Eoc_takuhai_for_catch_history" && $field=="link_area")
	{
		include $_SERVER["DOCUMENT_ROOT"].'/include/page_event/eoc_takuhai/list/link/index.php';
;
		return $value;
	}
				if($table=="Eoc_takuhai_speed" && $field=="new_link")
	{
		$id = $data['id'];
$user_id = $_SESSION['UserData']['user_id'];
$value = '<a href="http://urlounge.co.jp/include/speed/shuka.php?id='.$id.'&haisou=sagawa&u_id='.$user_id.'">佐川集荷申込</a><br>';
$value .= '<a href="http://urlounge.co.jp/include/speed/shuka.php?id='.$id.'&haisou=yamato&u_id='.$user_id.'">ヤマト集荷申込</a>';
$value .= '<a href="http://urlounge.co.jp/include/speed/shuka_other.php?id='.$id.'&u_id='.$user_id.'">集荷依頼済<br>(ﾊｲｼｭｰｶ不使用)</a>';;
		return $value;
	}
				if($table=="Eoc_takuhai_speed" && $field=="edit_link")
	{
		$num = $data['speed_num'];
$user_id = $_SESSION['UserData']['user_id'];
$value = '<a href="http://urlounge.co.jp/include/speed/shuka.php?type=update&no='.$num.'&u_id='.$user_id.'">変更</a><br>';
$value .= '<a href="http://urlounge.co.jp/include/speed/shuka.php?type=stop&no='.$num.'&u_id='.$user_id.'">停止</a><br>';
$value .= '<a href="http://urlounge.co.jp/include/speed/shuka.php?type=restart&no='.$num.'&u_id='.$user_id.'">再開</a>';;
		return $value;
	}
				if($table=="mail_data_tbl" && $field=="str_header")
	{
		$value = htmlspecialchars_decode($value);
$value = str_replace("\n","<br>",$value);;
		return $value;
	}
				if($table=="mail_data_tbl" && $field=="str_pc")
	{
		$value = htmlspecialchars_decode($value);
$value = str_replace("\n","<br>",$value);;
		return $value;
	}
				if($table=="mail_data_tbl" && $field=="str2_pc")
	{
		$value = htmlspecialchars_decode($value);
$value = str_replace("\n","<br>",$value);;
		return $value;
	}
				if($table=="chohyo_by_seihin" && $field=="price")
	{
		$value = number_format($value);;
		return $value;
	}
				if($table=="chohyo_by_dia" && $field=="price")
	{
		$value = number_format($value);;
		return $value;
	}
				if($table=="mst_color_code" && $field=="code")
	{
		$value = "<span style='height: 40px; width: 40px; background: {$value};'></span>";;
		return $value;
	}
				if($table=="mst_color_code" && $field=="font_color")
	{
		$value = "<span style='height: 40px; width: 40px; background: {$value};'></span>";;
		return $value;
	}
				if($table=="mst_color_code" && $field=="res")
	{
		$value = "<span style='height: 40px; width: 40px; line-height: 3!important; ";
$value .= " background: ".$data["code"]."; color: ".$data["font_color"]."; '>リファ</span>";;
		return $value;
	}
				if($table=="Eoc_timeline" && $field=="ecc_id")
	{
		$html = "";
$html .= "<a href='/shop5/Eoc_list.php?q=(ecc_id~equals~{$value})' >{$value}</a>";
$value = $html;;
		return $value;
	}
				if($table=="Eoc_speed" && $field=="eoc_speed_event")
	{
		include $_SERVER["DOCUMENT_ROOT"].'/include/page_event/Eoc_speed/list/events/index.php';;
		return $value;
	}
				if($table=="Eoc_speed" && $field=="haisou_bangou")
	{
			$haisoubangouArray = str_replace("-","",$value);
	$haisoubangouArray = str_split($haisoubangouArray);

	$value = '';
	$c_haisou = count($haisoubangouArray);
	for ($i=0; $i < $c_haisou; $i++) {
		$value = $value.$haisoubangouArray[$i];
		if(($i == 3) or ($i == 7)){
			$value = $value."-";
		}
	}
;
		return $value;
	}
				if($table=="vw_Eoc_returned_items" && $field=="meisai")
	{
		
$value = "<a class='btn btn-link' target='_blank' href='http://rifa.life/lounge_API/wkhtml/henkyakumeisai/?user_id={$_SESSION['uid']}&ecc_id={$value}'>返却明細</a>";;
		return $value;
	}
				if($table=="mst_sales_discount" && $field=="discount")
	{
		$value = $value.'%';;
		return $value;
	}
				if($table=="mst_ichiba_meeting" && $field=="original_id")
	{
		
$html = "<a href='";
$html .= "https://docs.google.com/spreadsheets/d/{$value}/";
$html .= "' target='_blank'>{$value}</a>";

$value = $html;
;
		return $value;
	}
				if($table=="market_product_performance" && $field=="nyuukin_price")
	{
		$value = number_format($value);;
		return $value;
	}
				if($table=="market_product_performance" && $field=="zeinuki")
	{
		if($data["buy_tax"] > 0){
  $buy_tax = 1+($data["buy_tax"] / 100);
}else{
  //default value
  $buy_tax = 1.08;
}
$value = $data["nyuukin_price"] / $buy_tax;
;
		return $value;
	}
				if($table=="market_product_performance" && $field=="avg")
	{
		if($data["ichiba_lot_pieces"] != ''){
  $value = $data["Parent_stone"] / $data["ichiba_lot_pieces"];
}else{
  $value = '';
}
;
		return $value;
	}
				if($table=="market_product_performance" && $field=="charagai")
	{
		if($data["buy_tax"] > 0){
  $buy_tax = 1+($data["buy_tax"] / 100);
}else{
  //default value
  $buy_tax = 1.08;
}
$zeinuki = $data["nyuukin_price"] / $buy_tax;
if($data["Gram"] > 0){ $Gram = $data["Gram"]; }else{ $Gram = 0;}
if($data["Parent_stone"] > 0){ $Parent_stone = $data["Parent_stone"]; }else{ $Parent_stone = 0;}
if($data["Aside_stone"] > 0){ $Aside_stone = $data["Aside_stone"]; }else{ $Aside_stone = 0;}
if($data["price_per_gram"] > 0){ $price_per_gram = $data["price_per_gram"]; }else{ $price_per_gram = 0;}
if($data["ichiba_melee_gai"] > 0){ $ichiba_melee_gai = $data["ichiba_melee_gai"]; }else{ $ichiba_melee_gai = 0;}

$value = ( $zeinuki - ( ( $Gram - ( ( $Parent_stone + $Aside_stone ) * 0.2 ) ) * $price_per_gram ) - ( ( $ichiba_melee_gai * 1000 ) * $Aside_stone ) ) / $Gram;
;
		return $value;
	}
				if($table=="market_product_performance" && $field=="sagaku")
	{
		$value = $data["nyuukin_price"] - $data["price"];
;
		return $value;
	}
				if($table=="market_product_performance" && $field=="arari")
	{
		if($data["nyuukin_price"] > 0){
  $value = ($data["nyuukin_price"] - $data["price"]) / $data["nyuukin_price"];
  $value = number_format($value,2).'%';
}else{
  $value = '';
}
;
		return $value;
	}
				if($table=="market_product_performance" && $field=="price")
	{
		$value = number_format($value);;
		return $value;
	}
				if($table=="market_product_performance" && $field=="ichiba_img")
	{
		if((!strstr('/',$value)) && ($value != '')){
	$value = "<img width='120px' src='http://rifa.life/lounge_API/google_drive/img/{$value}' />";
	
}
;
		return $value;
	}
				if($table=="gold_check" && $field=="link")
	{
		include $_SERVER["DOCUMENT_ROOT"]."/include/page_event/gold_check/link.php";;
		return $value;
	}
				if($table=="gold_check" && $field=="status_list")
	{
		include $_SERVER["DOCUMENT_ROOT"].'/include/page_event/gold_check/list/status_list.php';;
		return $value;
	}
				if($table=="gold_check" && $field=="sumgram")
	{
		include $_SERVER["DOCUMENT_ROOT"]."/include/page_event/gold_check/sumgram.php";;
		return $value;
	}
				if($table=="gold_check_item" && $field=="gram")
	{
		$value = number_format($value, 2);;
		return $value;
	}
				if($table=="Eoc_nittsuu" && $field=="tel2")
	{
		if($data["tel2"] != ''){
	$value = $data["tel2"];
}else{
	$value = $data["tel"];
};
		return $value;
	}
				if($table=="box" && $field=="link")
	{
		$value = "<a href='https://urlounge.co.jp/include/raku_cabinet_shop5/cabinet_sansyou.php?box_id={$value}' target='_blank' >新規画像確認</a>";;
		return $value;
	}
				if($table=="ichiba_exhibition" && $field=="link")
	{
		include $_SERVER["DOCUMENT_ROOT"]."/include/page_event/ichiba_exhibition/link.php";;
		return $value;
	}
				if($table=="ichiba_exhibition" && $field=="url_id")
	{
		$html = "";
$html .= "<a target='_blank' href='https://docs.google.com/spreadsheets/d/{$value}/edit'>{$value}</a>";

$value = $html;
;
		return $value;
	}
				if($table=="mst_jigane_sheet" && $field=="url_id")
	{
		$html = "";
$html .= "<a target='_blank' href='https://docs.google.com/spreadsheets/d/{$value}/edit'>{$value}</a>";

$value = $html;
;
		return $value;
	}
				if($table=="mst_ichiba_meeting1" && $field=="original_id")
	{
		
$html = "<a href='";
$html .= "https://docs.google.com/spreadsheets/d/{$value}/";
$html .= "' target='_blank'>{$value}</a>";

$value = $html;
;
		return $value;
	}
				if($table=="nyuko" && $field=="sales_price")
	{
		$value = number_format($value);;
		return $value;
	}
				if($table=="shouhin_for_labels" && $field=="price")
	{
		$value = number_format($value);;
		return $value;
	}
				if($table=="shouhin_for_labels" && $field=="sales_price")
	{
		$value = number_format($value);;
		return $value;
	}
				if($table=="mst_finish_terms" && $field=="cost")
	{
		$value = number_format($value); ;
		return $value;
	}
				if($table=="Eoc_trader" && $field=="link_field")
	{
		include $_SERVER["DOCUMENT_ROOT"].'/include/page_event/Eoc_trader/list/btns/index.php';;
		return $value;
	}
				if($table=="Eoc_trader" && $field=="many_satei_btn")
	{
		include $_SERVER["DOCUMENT_ROOT"].'/include/page_event/Eoc_trader/list/many_satei/index.php';;
		return $value;
	}
				if($table=="market_product_performance_original" && $field=="zeinuki")
	{
		$value = number_format($value);;
		return $value;
	}
				if($table=="market_product_performance_original" && $field=="nyuukin_price")
	{
		$value = number_format($value);;
		return $value;
	}
				if($table=="market_product_performance_original" && $field=="ichiba_img")
	{
		include $_SERVER["DOCUMENT_ROOT"].'/include/page_event/market_product_performance_original/list/img/index.php';;
		return $value;
	}
				if($table=="market_product_performance_original" && $field=="charagai")
	{
		$value = number_format($value);;
		return $value;
	}
				if($table=="market_product_performance_original" && $field=="price_without_tax")
	{
		$value = number_format($value);;
		return $value;
	}
				if($table=="market_product_performance_original" && $field=="price")
	{
		$value = number_format($value);;
		return $value;
	}
				if($table=="market_product_performance_original" && $field=="sagaku")
	{
		$value = number_format($value);;
		return $value;
	}
				if($table=="market_product_performance_original" && $field=="disp_ichiba_img")
	{
		include $_SERVER["DOCUMENT_ROOT"].'/include/page_event/market_product_performance_original/list/img/index.php';;
		return $value;
	}
				if($table=="market_product_performance_original" && $field=="price_per_gram")
	{
		$value = number_format($value);;
		return $value;
	}
				if($table=="Eoc_takuhai_return" && $field=="link")
	{
		include $_SERVER["DOCUMENT_ROOT"].'/include/page_event/Eoc_takuhai_return/list/link/index.php';;
		return $value;
	}
				if($table=="Eoc_takuhai_return" && $field=="created_by")
	{
		;
		return $value;
	}
				if($table=="Eoc_takuhai_return_items" && $field=="link")
	{
		include $_SERVER["DOCUMENT_ROOT"].'/include/page_event/Eoc_takuhai_return/list/link/index.php';;
		return $value;
	}
				if($table=="buy_campaign_data_detail" && $field=="buy_campaign_data_rules_id")
	{
		;
		return $value;
	}
				if($table=="mst_analysisMail_send_targets" && $field=="dm_check_type")
	{
		if($value == 2){
	$value = '許可';
}else{
	$value = '拒否';
};
		return $value;
	}
				if($table=="sagawa_api_results" && $field=="link")
	{
		include $_SERVER["DOCUMENT_ROOT"] . "/include/page_event/sagawa_api_results/link.php";
;
		return $value;
	}
				if($table=="sagawa_api_results" && $field=="shukaIraiShiteiTimeCode")
	{
		include $_SERVER["DOCUMENT_ROOT"] . "/include/page_event/sagawa_api_results/shukaIraiShiteiTimeCode.php";
;
		return $value;
	}
				if($table=="royal_customer" && $field=="link")
	{
		include $_SERVER["DOCUMENT_ROOT"] . "/include/page_event/royal_customer/link.php";
;
		return $value;
	}
				if($table=="Eoc_royal_customers" && $field=="link")
	{
		include $_SERVER["DOCUMENT_ROOT"] . "/include/page_event/Eoc_royal_customers/link.php";
;
		return $value;
	}
				if($table=="Eoc_royal_customers" && $field=="usage_period")
	{
		include $_SERVER["DOCUMENT_ROOT"] . "/include/page_event/Eoc_royal_customers/usage_period.php";
;
		return $value;
	}
				if($table=="Eoc_royal_customers" && $field=="mail_check_type")
	{
		if($value == "2"){
    $value = "許可";
}else{
    $value = "拒否";
};
		return $value;
	}
				if($table=="Eoc_inventory" && $field=="inventory_j_ec")
	{
		$value = number_format($value);;
		return $value;
	}
				if($table=="Eoc_inventory" && $field=="inventory_j_ichiba")
	{
		$value = number_format($value);;
		return $value;
	}
				if($table=="Eoc_inventory" && $field=="inventory_k")
	{
		$value = number_format($value);;
		return $value;
	}
				if($table=="Eoc_inventory" && $field=="inventory_d")
	{
		$value = number_format($value);;
		return $value;
	}
				if($table=="Eoc_inventory" && $field=="inventory_b_ec")
	{
		$value = number_format($value);;
		return $value;
	}
				if($table=="Eoc_inventory" && $field=="inventory_b_ichiba")
	{
		$value = number_format($value);;
		return $value;
	}
				if($table=="Eoc_inventory" && $field=="inventory_w_ec")
	{
		$value = number_format($value);;
		return $value;
	}
				if($table=="Eoc_inventory" && $field=="inventory_w_ichiba")
	{
		$value = number_format($value);;
		return $value;
	}
				if($table=="Eoc_inventory" && $field=="inventory_c")
	{
		$value = number_format($value);;
		return $value;
	}
				if($table=="Eoc_inventory_item" && $field=="price")
	{
		$value = number_format($value);;
		return $value;
	}
				if($table=="Eoc_inventory_item" && $field=="price_without_tax")
	{
		include $_SERVER["DOCUMENT_ROOT"]."/include/page_event/shouhin/list/price_without_tax.php";;
		return $value;
	}
				if($table=="Eoc_inventory_item" && $field=="sales_price")
	{
		$value = number_format($value);;
		return $value;
	}
				if($table=="Eoc_inventory_item" && $field=="nyuukin_price")
	{
		$value = number_format($value);;
		return $value;
	}
				if($table=="Eoc_inventory_weekly" && $field=="inventory_j_ec")
	{
		$value = number_format($value);;
		return $value;
	}
				if($table=="Eoc_inventory_weekly" && $field=="inventory_j_ichiba")
	{
		$value = number_format($value);;
		return $value;
	}
				if($table=="Eoc_inventory_weekly" && $field=="inventory_k")
	{
		$value = number_format($value);;
		return $value;
	}
				if($table=="Eoc_inventory_weekly" && $field=="inventory_d")
	{
		$value = number_format($value);;
		return $value;
	}
				if($table=="Eoc_inventory_weekly" && $field=="inventory_b_ec")
	{
		$value = number_format($value);;
		return $value;
	}
				if($table=="Eoc_inventory_weekly" && $field=="inventory_b_ichiba")
	{
		$value = number_format($value);;
		return $value;
	}
				if($table=="Eoc_inventory_weekly" && $field=="inventory_w_ec")
	{
		$value = number_format($value);;
		return $value;
	}
				if($table=="Eoc_inventory_weekly" && $field=="inventory_w_ichiba")
	{
		$value = number_format($value);;
		return $value;
	}
				if($table=="Eoc_inventory_weekly" && $field=="inventory_c")
	{
		$value = number_format($value);;
		return $value;
	}
				if($table=="Eoc_inventory_weekly" && $field=="sales_j_ec")
	{
		$value = number_format($value);;
		return $value;
	}
				if($table=="Eoc_inventory_weekly" && $field=="sales_j_ichiba")
	{
		$value = number_format($value);;
		return $value;
	}
				if($table=="Eoc_inventory_weekly" && $field=="sales_k")
	{
		$value = number_format($value);;
		return $value;
	}
				if($table=="Eoc_inventory_weekly" && $field=="sales_d")
	{
		$value = number_format($value);;
		return $value;
	}
				if($table=="Eoc_inventory_weekly" && $field=="sales_b_ec")
	{
		$value = number_format($value);;
		return $value;
	}
				if($table=="Eoc_inventory_weekly" && $field=="sales_b_ichiba")
	{
		$value = number_format($value);;
		return $value;
	}
				if($table=="Eoc_inventory_weekly" && $field=="sales_w_ec")
	{
		$value = number_format($value);;
		return $value;
	}
				if($table=="Eoc_inventory_weekly" && $field=="sales_w_ichiba")
	{
		$value = number_format($value);;
		return $value;
	}
				if($table=="Eoc_inventory_weekly" && $field=="sales_c")
	{
		$value = number_format($value);;
		return $value;
	}
				if($table=="Eoc_inventory_weekly" && $field=="buy_during_period_j_ec")
	{
		$value = number_format($value);;
		return $value;
	}
				if($table=="Eoc_inventory_weekly" && $field=="buy_during_period_j_ichiba")
	{
		$value = number_format($value);;
		return $value;
	}
				if($table=="Eoc_inventory_weekly" && $field=="buy_during_period_k")
	{
		$value = number_format($value);;
		return $value;
	}
				if($table=="Eoc_inventory_weekly" && $field=="buy_during_period_d")
	{
		$value = number_format($value);;
		return $value;
	}
				if($table=="Eoc_inventory_weekly" && $field=="buy_during_period_b_ec")
	{
		$value = number_format($value);;
		return $value;
	}
				if($table=="Eoc_inventory_weekly" && $field=="buy_during_period_b_ichiba")
	{
		$value = number_format($value);;
		return $value;
	}
				if($table=="Eoc_inventory_weekly" && $field=="buy_during_period_w_ec")
	{
		$value = number_format($value);;
		return $value;
	}
				if($table=="Eoc_inventory_weekly" && $field=="buy_during_period_w_ichiba")
	{
		$value = number_format($value);;
		return $value;
	}
				if($table=="Eoc_inventory_weekly" && $field=="buy_during_period_c")
	{
		$value = number_format($value);;
		return $value;
	}
				if($table=="freee_work_records_by_term" && $field=="normal_work_mins")
	{
		include $_SERVER["DOCUMENT_ROOT"].'/include/page_event/freee_work_records_by_term/list/mins_format.php';
;
		return $value;
	}
				if($table=="freee_work_records_by_term" && $field=="overtime_work_mins")
	{
		include $_SERVER["DOCUMENT_ROOT"].'/include/page_event/freee_work_records_by_term/list/mins_format.php';
;
		return $value;
	}
				if($table=="freee_work_records_by_term" && $field=="holiday_work_mins")
	{
		include $_SERVER["DOCUMENT_ROOT"].'/include/page_event/freee_work_records_by_term/list/mins_format.php';
;
		return $value;
	}
				if($table=="freee_work_records_by_term" && $field=="latenight_work_mins")
	{
		include $_SERVER["DOCUMENT_ROOT"].'/include/page_event/freee_work_records_by_term/list/mins_format.php';
;
		return $value;
	}
				if($table=="freee_work_records_by_term" && $field=="lateness_mins")
	{
		include $_SERVER["DOCUMENT_ROOT"].'/include/page_event/freee_work_records_by_term/list/mins_format.php';
;
		return $value;
	}
				if($table=="freee_work_records_by_term" && $field=="early_leaving_mins")
	{
		include $_SERVER["DOCUMENT_ROOT"].'/include/page_event/freee_work_records_by_term/list/mins_format.php';
;
		return $value;
	}
				if($table=="freee_work_records_by_term" && $field=="total_work_mins")
	{
		include $_SERVER["DOCUMENT_ROOT"].'/include/page_event/freee_work_records_by_term/list/mins_format.php';
;
		return $value;
	}
				if($table=="freee_work_records_by_term" && $field=="total_sub_over_mins")
	{
		include $_SERVER["DOCUMENT_ROOT"].'/include/page_event/freee_work_records_by_term/list/mins_format.php';
;
		return $value;
	}
				if($table=="freee_work_records_by_term" && $field=="retire_date")
	{
		include $_SERVER["DOCUMENT_ROOT"].'/include/page_event/freee_work_records_by_term/list/retire_date.php';
;
		return $value;
	}
				if($table=="freee_saisanhyou" && $field=="freee_deal_join_manual_journal__detail_id")
	{
		
include $_SERVER["DOCUMENT_ROOT"].'/include/page_event/freee_saisanhyou/list/index.php';;
		return $value;
	}
				if($table=="freee_saisanhyou" && $field=="freee_deals_id")
	{
		$html = $value . "<br>" . "<a target='_blank' href='https://secure.freee.co.jp/deals#deal_id=" . $value . "'>freee取引ページ</a>";
$value = $html;;
		return $value;
	}
				if($table=="freee_saisan_rule_details" && $field=="created_by")
	{
		;
		return $value;
	}
				if($table=="freee_expense_categories" && $field=="updated_by")
	{
		;
		return $value;
	}
				if($table=="Eoc_chigins_for_freee_import" && $field=="remark")
	{
		$value = "古物等の購入：{$data["address1"]}{$data["address2"]}{$data["address3"]}　{$value}";;
		return $value;
	}
				if($table=="shop_front_details" && $field=="links")
	{
		include $_SERVER["DOCUMENT_ROOT"]."/include/page_event/shop_front_details/list/links.php";
;
		return $value;
	}
				if($table=="shop_front_details" && $field=="send_id")
	{
		include $_SERVER["DOCUMENT_ROOT"]."/include/page_event/shop_front_details/list/send_id.php";
;
		return $value;
	}
				if($table=="shop_front_details" && $field=="products_image_path")
	{
		include $_SERVER["DOCUMENT_ROOT"]."/include/page_event/shop_front_details/list/products_image_path.php";
;
		return $value;
	}
				if($table=="shop_front_details" && $field=="signature_image_path")
	{
		include $_SERVER["DOCUMENT_ROOT"]."/include/page_event/shop_front_details/list/signature_image_path.php";
;
		return $value;
	}
				if($table=="shop_front_details" && $field=="id_drive_url")
	{
		include $_SERVER["DOCUMENT_ROOT"]."/include/page_event/shop_front_details/list/id_drive_url.php";
;
		return $value;
	}
				if($table=="shop_front_seiren_details" && $field=="google_drive_id")
	{
		include $_SERVER["DOCUMENT_ROOT"]."/include/page_event/shop_front_seiren_details/list/id_drive_url.php";;
		return $value;
	}
	return $value;
}


/**
 * return custom expression for file
 * @intellisense
 */
function fileCustomExpression($file, $data, $field, $ptype, $table="")
{
	$value = "";
	global $strTableName;
	if(!$table)
		$table = $strTableName;
				if($table=="voice_sub_id_1" && $field=="subject")
	{
		;
		return $value;
	}
	return $value;
}

/**
 * return Lookup Wizard Where expression
 * @intellisense
 */
function GetLWWhere($field, $ptype, $table = "")
{
	global $strTableName;
	if(!$table)
		$table = $strTableName;
				if($table=="Eoc" && $field=="bank_details_code")
	{
		return " category=1 ";
	}
				if($table=="Eoc" && $field=="bank_details_blunch_name")
	{
		return " category=2 ";
	}
				if($table=="PMT_EV001" && $field=="mst_business_partner_id")
	{
		return "_DIA_DA_CUSTOMER_sort > 0";
	}
				if($table=="Eoc_chigins2" && $field=="_status")
	{
		return "goods_id  = 10 or goods_id =34";
	}
				if($table=="Eoc_chigins2" && $field=="status")
	{
		return " kinsa > 0 ";
	}
				if($table=="Eoc_chigins" && $field=="bank_details_code")
	{
		return " category=1 ";
	}
				if($table=="dia" && $field=="status")
	{
		return " dia > 0 ";
	}
				if($table=="dia" && $field=="mst_business_partner_id")
	{
		return " _DIA_DA_CUSTOMER_sort is not null";
	}
				if($table=="kin_satei" && $field=="status")
	{
		return " kinsa > 0 ";
	}
				if($table=="kin_satei" && $field=="category_id")
	{
		return " ( (category_id =  12) OR (category_id =  17) OR (category_id =  18) OR (category_id =  19) OR (category_id =  50) OR (category_id =  20) OR (category_id =  51) OR (category_id =  21) OR (category_id =  22) OR (category_id =  23) OR (category_id = 65) OR (category_id = 66) OR (category_id = 67) OR (category_id = 68) OR (category_id = 69))";
	}
				if($table=="Eoc_catch" && $field=="bank_details_code")
	{
		return " category=1 ";
	}
				if($table=="Eoc_catch" && $field=="bank_details_blunch_name")
	{
		return " category=2 ";
	}
				if($table=="mst_Eoc_received_event_contents" && $field=="category_id")
	{
		return "category_id = 12";
	}
				if($table=="satei_of_jewelry" && $field=="category_id")
	{
		return " ( (category_id =  10) OR (category_id =  12) OR (category_id =  17) OR (category_id =  18) OR (category_id =  19) OR (category_id =  50) OR (category_id =  20) OR (category_id =  51) OR (category_id =  21) OR (category_id =  22) OR (category_id =  23) OR (category_id = 65) OR (category_id = 66) OR (category_id = 67) OR (category_id = 68) OR (category_id = 69))";
	}
				if($table=="satei_of_brand" && $field=="category_id")
	{
		return " ( (category_id !=  10) AND (category_id !=  17) AND (category_id !=  18) AND (category_id !=  19) AND (category_id !=  50) AND (category_id !=  20) AND (category_id !=  51) AND (category_id !=  21) AND (category_id !=  22) AND (category_id !=  23) OR (category_id = 65) OR (category_id = 66) OR (category_id = 67) OR (category_id = 68) OR (category_id = 69))";
	}
				if($table=="buy_campaign_data_detail" && $field=="buy_campaign_data_rules_id")
	{
		return " id > 10 ";
	}
				if($table=="Eoc_chigins_for_freee_import" && $field=="bank_details_code")
	{
		return " category=1 ";
	}
	return "";
}

/**
 * @intellisense
 */
function GetDefaultValue($field, $ptype, $table="")
{
	global $strTableName;
	if(!$table)
		$table=$strTableName;
				if($table=="Eoc" && $field=="created_t")
	{
		return now();
	}
				if($table=="Eoc" && $field=="_registerd_id")
	{
		return $_SESSION["uid"];
	}
				if($table=="Eoc" && $field=="mail_check_type")
	{
		return 2;
	}
				if($table=="Eoc" && $field=="dm_check_type")
	{
		return 2;
	}
				if($table=="Eoc_chigins2" && $field=="create_at")
	{
		return now();
	}
				if($table=="Eoc_chigins2" && $field=="updated_by")
	{
		return $_SESSION["uid"];
	}
				if($table=="Eoc_chigins2" && $field=="created_by")
	{
		return $_SESSION["uid"];
	}
				if($table=="Eoc_chigins2" && $field=="update_at")
	{
		return now();
	}
				if($table=="Eoc_chigins" && $field=="ching_created_at")
	{
		return now();
	}
				if($table=="Eoc_chigins" && $field=="updated_at")
	{
		return now();
	}
				if($table=="satei" && $field=="status")
	{
		return 32;
	}
				if($table=="satei" && $field=="product_num")
	{
		return 631;
	}
				if($table=="satei" && $field=="guarantee")
	{
		return 無;
	}
				if($table=="awazu" && $field=="product_num")
	{
		return 631;
	}
				if($table=="kaibutu" && $field=="product_num")
	{
		return 631;
	}
				if($table=="sagawa_sales_office" && $field=="create_at")
	{
		return now();
	}
				if($table=="sagawa_sales_office" && $field=="update_at")
	{
		return now();
	}
				if($table=="sagawa_sales_office" && $field=="created_by")
	{
		return $_SESSION["uid"];
	}
				if($table=="sagawa_sales_office" && $field=="updated_by")
	{
		return $_SESSION["uid"];
	}
				if($table=="store_sub_categories" && $field=="updated_at")
	{
		return strftime("%Y-%m-%d %H:%M:%S");
	}
				if($table=="store_sub_categories" && $field=="updated_by")
	{
		return $_SESSION["UserID"];
	}
				if($table=="store_sub_categories" && $field=="created_by")
	{
		return $_SESSION["UserID"];
	}
				if($table=="store_sub_categories" && $field=="created_at")
	{
		return strftime("%Y-%m-%d %H:%M:%S");
	}
				if($table=="store_categories" && $field=="created_at")
	{
		return strftime("%Y-%m-%d %H:%M:%S");
	}
				if($table=="store_categories" && $field=="updated_at")
	{
		return strftime("%Y-%m-%d %H:%M:%S");
	}
				if($table=="store_categories" && $field=="updated_by")
	{
		return $_SESSION["UserID"];
	}
				if($table=="Eoc_takuhai" && $field=="status")
	{
		return 1;
	}
				if($table=="dia" && $field=="unit")
	{
		return "ct";
	}
				if($table=="kin_satei" && $field=="product_num")
	{
		return 631;
	}
				if($table=="Eoc_catch" && $field=="created_t")
	{
		return now();
	}
				if($table=="Eoc_catch" && $field=="mail_check_type")
	{
		return 2;
	}
				if($table=="satei_of_jewelry" && $field=="status")
	{
		return 32;
	}
				if($table=="satei_of_jewelry" && $field=="product_num")
	{
		return 631;
	}
				if($table=="satei_of_jewelry" && $field=="logo")
	{
		return "無";
	}
				if($table=="satei_of_jewelry" && $field=="parts")
	{
		return "無";
	}
				if($table=="satei_of_jewelry" && $field=="guarantee")
	{
		return "無";
	}
				if($table=="satei_of_jewelry" && $field=="plate")
	{
		return "無";
	}
				if($table=="satei_of_brand" && $field=="status")
	{
		return 32;
	}
				if($table=="satei_of_brand" && $field=="product_num")
	{
		return 631;
	}
				if($table=="satei_of_brand" && $field=="guarantee")
	{
		return 無;
	}
				if($table=="market_product_performance_original" && $field=="front_hidden_flg")
	{
		return "False";
	}
				if($table=="Eoc_royal_customers" && $field=="mail_check_type")
	{
		return 2;
	}
				if($table=="Eoc_chigins_for_freee_import" && $field=="ching_created_at")
	{
		return now();
	}
				if($table=="Eoc_chigins_for_freee_import" && $field=="updated_at")
	{
		return now();
	}
				if($table=="voice_sub_id_1" && $field=="voice_id")
	{
		return 1;
	}
				if($table=="voice_sub_id_1" && $field=="page_category")
	{
		return 2;
	}
				if($table=="voice_sub_id_1" && $field=="preview_dia_tegami")
	{
		return 1;
	}
				if($table=="voice_sub_id_1" && $field=="preveiw_kin_tegami")
	{
		return 1;
	}
	return "";
}

/**
 * @intellisense
 */
function GetAutoUpdateValue($field, $ptype, $table="")
{
	global $strTableName;
	if(!$table)
		$table=$strTableName;
				if($table=="Eoc_chigins2" && $field=="updated_by")
	{
		return $_SESSION["uid"];
	}
				if($table=="Eoc_chigins2" && $field=="update_at")
	{
		return now();
	}
				if($table=="Eoc_chigins" && $field=="updated_at")
	{
		return now();
	}
				if($table=="sagawa_sales_office" && $field=="update_at")
	{
		return now();
	}
				if($table=="sagawa_sales_office" && $field=="updated_by")
	{
		return $_SESSION["uid"];
	}
				if($table=="store_sub_categories" && $field=="updated_at")
	{
		return strftime("%Y-%m-%d %H:%M:%S");
	}
				if($table=="store_sub_categories" && $field=="updated_by")
	{
		return $_SESSION["UserID"];
	}
				if($table=="store_categories" && $field=="updated_at")
	{
		return strftime("%Y-%m-%d %H:%M:%S");
	}
				if($table=="store_categories" && $field=="updated_by")
	{
		return $_SESSION["UserID"];
	}
				if($table=="Eoc_chigins_for_freee_import" && $field=="updated_at")
	{
		return now();
	}
	return "";
}

/**
 * @intellisense
 */
function GetUploadFolderExpression($field, $file, $table="")
{
	global $strTableName;
	if(!$table)
		$table = $strTableName;
	if($table=="voice_sub_id_1" && $field=="subject")
	{
		$folder = "userfiles/images/".date("Y")."/".date("m")."/".date("d");
;
		return $folder;
	}
	return "";
}

/**
 * @intellisense
 */
function mdeleteIndex($i)
{
	return $i-1;
}

/**
 * Call function stack info parser
 * Return array of function calls

 * @intellisense
 * @return array
 */
function parse_backtrace($errfFile, $errLine, $splitAsArray = true)
{
    // get backtrace array
    $backtrace = debug_backtrace();

    // delete calls to error handler functions
	foreach ($backtrace as $i => $call)
    {
    	// cut error handlers calls etc.
    	if ($call['function'] == 'parse_backtrace' ||  $call['function'] == 'error_handler' || $call['function'] == 'trigger_error'){
    		array_shift($backtrace);
    		continue;
    	}
    }
    // if no data return empty array
    if (count($backtrace) == 0) {
        return array();
    }


    $backTraceLen = count($backtrace);
    $backtrace[$backTraceLen]['file'] = $backtrace[$backTraceLen-1]['file'];
    $backtrace[$backTraceLen]['line'] = $backtrace[$backTraceLen-1]['line'];
    $backtrace[$backTraceLen]['function'] =  'Global scope';


 	// make shift of file: line, for better view. It will show not line where function were called, but line where in function error was happend
    for($i=0;$i<count($backtrace);$i++)
    {
    	$errorLineBefore = $backtrace[$i]['line'];
    	$errorFileBefore = $backtrace[$i]['file'];
    	$backtrace[$i]['file'] = $errfFile;
    	$backtrace[$i]['line'] = $errLine;
    	$errLine = $errorLineBefore;
    	$errfFile = $errorFileBefore;
    }

    // result array with data
    $funCallsArray = array();
    // parse array
    foreach ($backtrace as $i => $call)
    {
    	// proccess the data that may not exist
    	if (isset($call['file']))
    	{
    		// get path to the root
			$pathToRoot = substr(dirname(__FILE__),0,strlen(dirname(__FILE__))-strlen("include"));
			// replace it
			$call['file'] = str_replace($pathToRoot, '', $call['file']);
    	}
    	else
    	{
    		$call['file'] = '(null)';

    	}
    	//$call['file'] = !isset($call['file']) ? '(null)' : $call['file'];
    	$call['line'] = !isset($call['line']) ? '0' : $call['line'];
		// proccess file and error line
        $location = $call['file'] . ':' . $call['line'];
        // if object method was called
        if (isset($call['class']))
        {
        	$function = $call['class'].(isset($call['type']) ? $call['type'] : '.').$call['function'];

        }// if function
        else
        {
        	$function = $call['function'];
        }
		// proccess arguments
        $params = '';
        if (isset($call['args']))
        {
            $args = array();
            $j=0;
            foreach ($call['args'] as $arg)
            {
            	$j++;
            	// proccess array
                if (is_array($arg))
                {
                	$arrStr = runner_print_r($arg, true);
                    $arrStr = strlen($arrStr) < 200 ? $arrStr : substr($arrStr, 0, 200).'...';
                    $args[] = $j.'.&nbsp;'.runner_htmlspecialchars($arrStr).';';
                }
                // process objects
                elseif (is_object($arg))
                {
                    $args[] = $j.'.&nbsp;'.runner_htmlspecialchars(get_class($arg)).';';
                }
                // another arguments
                else
                {
                	$arg = @strlen($arg) < 200 ? $arg : @runner_htmlspecialchars(substr($arg, 0, 200)).'...';
                    $args[] = $j.'.&nbsp;'.$arg.';';
                }
            }
            $params = implode('<br/> ', $args);
        }
		// add in finish array all params
		if (!$splitAsArray)
		{
			$funCallsArray[] = '#'.$i.'&nbsp;&nbsp;'.$function.'('.$params.')&nbsp;&nbsp;called&nbsp;at&nbsp;['.$location.']';
		}
		else
		{
			$funCallsArray[] = array('num' => '#'.$i.'.&nbsp;', 'path' => $location, 'func'=>$function, 'args'=>(strlen($params) ? $params : 'N/A'));
		}
    }

    // return array with call functions strings
    return $funCallsArray;
}

/**
 * display error message
 * @intellisense
 */
function runner_error_handler($errno, $errstr, $errfile, $errline)
{
	global $strLastSQL;
	global $globalSettings;

	if ($errno==2048 || $errno == 8 || $errno == 2 )
		return 0;

	if(strpos($errstr,"It is not safe to rely on the system's timezone settings."))
		return 0;
	if(strpos($errstr,"fopen(")===0)
		return 0;

	if ( !$globalSettings["showDetailedError"] )
	{
		echo $globalSettings["customErrorMessage"];
		exit(0);
	}

	// show error htm
	if(!class_exists("Xtempl"))
		require_once(getabspath("include/xtempl.php"));

	$xt = new Xtempl();
	$xt->assign('errno', $errno);
	$xt->assign('errstr', $errstr);

	$url = $_SERVER["SERVER_NAME"].$_SERVER["SCRIPT_NAME"];
	if(array_key_exists("QUERY_STRING",$_SERVER))
	{
		$url .= "?".runner_htmlspecialchars($_SERVER["QUERY_STRING"]);
	}

	$xt->assign('url', $url);
	$xt->assign('errfile', $errfile);
	$xt->assign('errline', $errline);

	$sqlStr = isset($strLastSQL) ? runner_htmlspecialchars(substr($strLastSQL,0,1024)) : '';
	$xt->assign('sqlStr', $sqlStr);

	$debugInfoArr = parse_backtrace($errfile, $errline);
	$xt->assign_loopsection('debugRow', $debugInfoArr);

	$xt->displayPartial(GetTemplateName("", 'error'));

	exit(0);
}

/**
 * @intellisense
 */
function no_output_done()
{
	if(headers_sent())
		return false;
	if(ob_get_length())
		return false;
	return true;
}

/**
 * @intellisense
 */
function format_currency($val)
{
	return str_format_currency($val);
}

/**
 * @intellisense
 */
function format_number($val,$valDigits = false)
{
	return str_format_number($val,$valDigits);
}

function formatNumberForHTML5($value)
{
	return $value;
}

/**
 * @intellisense
 */
function format_datetime($time)
{
	return str_format_datetime($time);
}

/**
 * @intellisense
 */
function format_time($time)
{
	return str_format_time($time);
}

/**
 * @intellisense
 */
function secondsPassedFrom($datetime)
{
	$arrDateTime=db2time($datetime);
	return time()-mktime($arrDateTime[3],$arrDateTime[4],$arrDateTime[5],$arrDateTime[1],$arrDateTime[2],$arrDateTime[0]);
}

/**
 * @intellisense
 */
function xtempl_call_func($func,&$params)
{
	if(function_exists($func))
	{
		$func($params);
	}
}

/**
 * @intellisense
 */
function echoBinary($string, $bufferSize = 8192)
{
	for ($chars=strlen($string)-1,$start=0;$start <= $chars;$start += $bufferSize)
		echo substr($string,$start,$bufferSize);
}

/**
 * @intellisense
 */
function echoBinaryPartial($string, $startPos, $endPos, $bufferSize = 8192)
{
	$length = $endPos - $startPos + 1;
	if($length < $bufferSize)
		$bufferSize = $length;
	for ($start = $startPos; $start <= $endPos && $bufferSize > 0;)
	{
		echo substr($string, $start, $bufferSize);
		$start += $bufferSize;
		if($start + $bufferSize > $length)
			$bufferSize = $length - $start;
	}
}

/**
 * @intellisense
 */
function setObjectProperty(&$obj,$key,&$value)
{
	$obj->$key = &$value;
}

/**
 * @intellisense
 */
function returnError404()
{
	header("HTTP/1.0 404 Not Found");
}

/**
 * @intellisense
 */
function execute_events(&$params)
{
	if(function_exists(@$params["custom1"]))
		eval($params["custom1"].'($params);');
}

/**
 * @intellisense
 */
function GetMySQLLastInsertID( $connection )
{
//	select LAST_INSERT_ID() for ASP
	return $connection->getInsertedId();
}

/**
 * @intellisense
 */
function DoUpdateRecord( $pageObject )
{
	return DoUpdateRecordSQL( $pageObject );
}
/**
 * @intellisense
 */
function DoInsertRecord($table, &$avalues, &$blobfields, &$pageObject)
{
	return DoInsertRecordSQL($table, $avalues, $blobfields, $pageObject);
}

/**
 * @intellisense
 * @param RunnerPage pageObject
 * @return Boolean
 */
function DoInsertRecordOnAdd( &$pageObject )
{
	return DoInsertRecordSQLOnAdd( $pageObject );
}

/**
 * @intellisense
 */
function xtempl_include_header($xt,$fname,$param)
{
	$xt->assign_function($fname,"xt_include",array("file"=>$param));
}

function xt_include($params)
{
	if(file_exists(getabspath($params["file"])))
		include(getabspath($params["file"]));
}


$db_query_safe_errstr ="";
$db_query_safe_err = false;
/**
 * @param String qstring
 * @param &String errstring
 * @param Connection connection (optional)
 * @return Mixed
 * Todo: It should return QueryResult object //#9875
 * @intellisense
 */
/*function db_query_safe($qstring, &$errstring, $connection = null)
{
	global $db_query_safe_errstr, $db_query_safe_err;

	$db_query_safe_errstr = "";
	$db_query_safe_err = false;
	$errhandler = set_error_handler("errhandler_db_query_safe");

	if( is_null($connection) )
		$connection = getDefaultConnection();

	$rs = $connection->query( $qstring )->getQueryHandle();

	set_error_handler( $errhandler );
	$errstring = $db_query_safe_errstr;
	if( $db_query_safe_err )
		return false;

	return $rs;
}*/

/**
 * @intellisense
 */
/*function errhandler_db_query_safe($errno, $errstr, $errfile, $errline)
{
	global $db_query_safe_errstr, $db_query_safe_err;

	if( $errno==E_ERROR || $errno==E_USER_ERROR )
		$db_query_safe_err = true;

	$db_query_safe_errstr.= "<br>".$errstr;
}*/

/**
 * @intellisense
 */
function binPrint(&$value, $size)
{
	echobig($value,$size);
}

/**
 * construct "good" field name
 * @intellisense
 */
function GoodFieldName($field)
{
	global $cCharset;
	if ($cCharset == "utf-8"){
		$field = utf8_decode($field);
	}
	$field=(string)$field;
	$out="";
	for($i=0;$i<strlen($field);$i++)
	{
		$t=substr($field,$i,1);
		if((ord($t)<ord('a') || ord($t)>ord('z')) && (ord($t)<ord('A') || ord($t)>ord('Z')) && (ord($t)<ord('0') || ord($t)>ord('9')))
			$out.='_';
		else
			$out.=$t;
	}
	return $out;
}

/**
 * @intellisense
 */
function xt_getvar(&$xt,$name)
{
	global $testingLinks;
	for($i = count($xt->xt_stack)-1;$i>=0;$i--)
	{
		if(isset($xt->xt_stack[$i][$name]))
			return $xt->xt_stack[$i][$name];
	}
	if(!$xt->testingFlag)
		return false;

	if(isset($testingLinks[$name]))
		return "func=\"".$testingLinks[$name]."\"";
	else
		return false;
}

/**
 * @intellisense
 */
function xt_process_template(&$xt,$str)
{
//	parse template file tag by tag
	$varparams=array();
	$start=0;
	$literal=false;
	$len = strlen($str);
	while(true)
	{
		$pos = strpos($str,"{",$start);
		if($pos===false)
		{
			echo substr($str,$start,$len-$start);
			break;
		}
		$section=false;
		$var=false;
		$message=false;
		if(substr($str,$pos+1,6)=="BEGIN ")
			$section=true;
		elseif(substr($str,$pos+1,1)=='$')
			$var=true;
		elseif(substr($str,$pos+1,14)=='mlang_message ')
		{
			$message=true;
		}
		else
		{
//	no tag, just '{' char
			echo substr($str,$start,$pos-$start+1);
			$start=$pos+1;
			continue;
		}
		echo substr($str,$start,$pos-$start);
		if($section)
		{
//	section
			$endpos=strpos($str,"}",$pos);
			if($endpos===false)
			{
				$xt->report_error("Page is broken");
				return;
			}
			$section_name=trim(substr($str,$pos+7,$endpos-$pos-7));
			$endtag="{END ".$section_name."}";
			$endpos1=strpos($str,$endtag,$endpos);
			if($endpos1===false)
			{
				echo "End tag not found:".runner_htmlspecialchars($endtag);
				$xt->report_error("Page is broken");
				return;
			}
			$section=substr($str,$endpos+1,$endpos1-$endpos-1);
			$start=$endpos1+strlen($endtag);
			$sectionVar = xt_getvar($xt,$section_name);
			if($sectionVar===false)
			{
				continue;
			}
			$begin="";
			$end="";
			if(is_array($sectionVar))
			{
				$begin=@$sectionVar["begin"];
				$end=@$sectionVar["end"];

				$var=@$sectionVar["data"];
			}
			else
			{
				$var = $sectionVar;
			}
			if(!is_array($var))
			{
//	if section
				echo $begin;
				if(is_array($sectionVar))
					$xt->xt_stack[]=&$sectionVar;
				xt_process_template($xt,$section);
				if(is_array($sectionVar))
					array_pop($xt->xt_stack);
				$xt->processVar($end, $varparams);
			}
			else
			{
//	foreach section
				echo $begin;
				$keys=array_keys($var);
				foreach($keys as $i)
				{
					$xt->xt_stack[]=&$var[$i];
					if(is_array($var[$i]) && isset($var[$i]["begin"]))
						echo $var[$i]["begin"];
					xt_process_template($xt,$section);
					array_pop($xt->xt_stack);
					if(is_array($var[$i]) && isset($var[$i]["end"]))
						echo $var[$i]["end"];
				}
				$xt->processVar($end, $varparams);
			}
		}
		elseif($var)
		{
//	display a variable or call a function
			$endpos=strpos($str,"}",$pos);
			if($endpos===false)
			{
				$xt->report_error("Page is broken");
				return;
			}
			$varparams=array();
			$var_name = trim(substr($str,$pos+2,$endpos-$pos-2));
			if(strpos($var_name," ")!==FALSE)
			{
				$varparams = explode(" ",$var_name);
				$var_name = $varparams[0];
				unset($varparams[0]);
			}
			$start=$endpos+1;
			$var = xt_getvar($xt,$var_name);
			if($var===false)
			{
				continue;
			}
			$xt->processVar($var, $varparams);
		}
		elseif($message)
		{
			$endpos=strpos($str,"}",$pos);
			if($endpos===false)
			{
				$xt->report_error("Page is broken");
				return;
			}
			$tag = trim(substr($str,$pos+15,$endpos-$pos-15));
			$start=$endpos+1;
			echo runner_htmlspecialchars(mlang_message($tag));
		}
	}
}

/**
 * @intellisense
 */
function parse_addr_list($to)
{
	$addr_arr = array();

	$to = preg_replace('/^[\s*,]+|[\s*,]+$/',"", $to);
	$to = preg_replace('/\s+,/',",", $to);
	//split $to by ',' not surrounded by quotes or round brackets
	$arr = preg_split('/(,(?=([^"]*("[^"]*")?)*$))(?![^\(]*(\),|\)$))/', $to);

  	$matches = array();
    foreach($arr as $item)
	{
		$item = trim($item);
		if($item != "")
		{
			//match an email not surrounded by quotes or round brackets
			preg_match_all('/(([A-Za-z\d_\-\.+]+@[A-Za-z\d_\-\.]+\.[A-Za-z\d_\-]+)(?=([^"]*("[^"]*")?)*$))(?![^\(]*(\),|\)$))/', $item, $matches);
			if(count($matches[0]) == 1)
			{
			    $name = preg_replace('/(([A-Za-z\d_\-\.+]+@[A-Za-z\d_\-\.]+\.[A-Za-z\d_\-]+)(?=([^"]*("[^"]*")?)*$))(?![^\(]*(\),|\)$))/',"", $item);
			    $name = preg_replace('/"|<>$/',"", $name);
				$addr_arr[] = array('addr' => $matches[0][0], 'name' => $name);
			}
		}
	}

	return $addr_arr;
}

/**
 * @param Array params
 * @return Array
 * @intellisense
 */
function runner_mail_smtp( $params )
{
	$mail = new PHPMailer( true );
	$mail->IsSMTP(); // telling the class to use SMTP

	$useCustomSMTP = GetGlobalData("useCustomSMTPSettings", false);
	$SMTPUser = GetGlobalData("strSMTPUser", "");
	
	if( $useCustomSMTP && $SMTPUser != "" || isset( $params['username'] ) )
	{
		$mail->SMTPAuth = true;  // enable SMTP authentication
		$mail->Username = isset( $params['username'] ) ? $params['username'] : $SMTPUser;  // SMTP username
		$mail->Password = isset( $params['password'] ) ? $params['password'] : GetGlobalData("strSMTPPassword", "");  // SMTP password
	}
	else
	{
		$mail->SMTPAuth = false;
	}
	
	$SMTPServer = GetGlobalData("strSMTPServer", "");
	if( $useCustomSMTP && $SMTPServer != "" || isset( $params['host'] ) )
	{
		$mail->Host = isset( $params['host'] ) ? $params['host'] : $SMTPServer;  // sets SMTP server
	}
	else if( ini_get('SMTP') != '' )
	{
		$mail->Host = ini_get('SMTP');
	}
	
	$SMTPPort = GetGlobalData("strSMTPPort", "");
	if( $useCustomSMTP && $SMTPPort != "" || isset( $params['port'] ) )
	{
		$mail->Port = isset( $params['port'] ) ? $params['port'] + 0 : $SMTPPort + 0;  // set the SMTP port
	}
	else if( ini_get('smtp_port') != '' )
	{
		 $mail->Port = ini_get('smtp_port') + 0;
	}

	if( GetGlobalData("useSSL", false) )
		$mail->SMTPSecure = "ssl";

	$mail->Subject = $params['subject'];

	$from = isset( $params['from'] ) ? $params['from'] : "";
	if( !$from )
		$from = GetGlobalData("strFromEmail", "");
		
	try 
	{
		$mail->SetFrom( $from, isset( $params['fromName'] ) ? $params['fromName'] : "" );
	}
	catch( phpmailerException $e )
	{
		return array( "mailed" => false, "message"=> nl2br( $e->getMessage() ) );
	}
	
	$to = isset( $params['to'] ) ? $params['to'] : "";
	if( $to != "" )
	{
		$arr_to = parse_addr_list( $to );
		foreach( $arr_to as $email )
		{
			$mail->AddAddress( $email['addr'], $email['name'] );
		}
	}

	// replyTo
	if ( isset( $params['replyTo'] ) )
		$mail->AddReplyTo( $params['replyTo'], "" );

	$body = isset( $params['body'] ) ? $params['body'] : "";
	
	// body, htmlbody
	if ( isset( $params['htmlbody'] ) )
	{
		$mail->AltBody = $body;
		$mail->MsgHTML( $params['htmlbody'] );
	}
	else
	{
		$mail->Body = $body;
	}

	// charset
	if ( isset( $params['charset'] ) )
		$mail->CharSet = $params['charset'];
	else
		$mail->CharSet = "utf-8";

	// priority
	if( isset( $params['priority'] ) )
		$mail->Priority = $params['priority'];

	// CC
	if( isset( $params['cc'] ) )
	{
		$arr_cc = parse_addr_list( $params['cc'] );
		foreach( $arr_cc as $cc )
		{
			$mail->AddCC( $cc['addr'], $cc['name'] );
		}
	}
	
	// BCC
	if( isset( $params['bcc'] ) )
	{
		$arr_bcc = parse_addr_list( $params['bcc'] );
		foreach( $arr_bcc as $bcc )
		{
			$mail->AddBCC( $bcc['addr'], $bcc['name'] );
		}
	}

	if( isset( $params['attachments'] ) && is_array( $params['attachments'] ) )
	{
		foreach ( $params['attachments'] as $attachment )
		{
			$mail->AddAttachment( $attachment['path'],
				isset( $attachment['name'] ) ? $attachment['name'] : '',
				isset( $attachment["encoding"] ) ? $attachment["encoding"] : 'base64',
				isset( $attachment["type"] ) ? $attachment["type"] : 'application/octet-stream' );
		}
	}
	
	try
	{
		$res = $mail->Send();
	}
	catch( phpmailerException $e )
	{
		return array( "mailed" => false, "message"=> nl2br( $e->getMessage() ) );
	}
	
	return array( "mailed" => $res, "message"=> nl2br( $mail->ErrorInfo ) );
}

/**
 * @intellisense
 */
function getFileNameFromURL()
{
	$scriptname = $_SERVER["PHP_SELF"];
	$pos = strrpos($scriptname, "/");
	if($pos !== FALSE)
		$scriptname = substr($scriptname, $pos + 1);
	return $scriptname;
}

/**
 * @intellisense
 */
function strlen_bin(&$str)
{
	return strlen($str);
}

/**
 * The code of ODBCFunctions::stripSlashesBinary was
 * refactored as db_stripslashesbinaryAccess function
 * to convert in ASP correctly
 * @param String str
 * @return String
 * @intellisense
 */
function db_stripslashesbinaryAccess($str)
{
	if( is_array($str) )
		$str = implode('', $str);

	//	try to remove ole header for BMP pictures
	$pos = strpos($str, ".Picture");
	if( $pos === false || $pos > 300 )
		return $str;

	$pos1 = strpos($str, "BM", $pos);
	if( $pos1 === false || $pos1 > 300 )
		return $str;

	return substr($str, $pos1);
}

/**
 * @intellisense
 */
function SendContentLength($len)
{
	header("Content-Length: ".$len);
}

/**
 * @intellisense
 */
function DecodeUTF8($str)
{
	return $str;
}
/**
 * @intellisense
 */
function escapeEntities($str)
{
	return $str;
}

/**
 * @intellisense
 */
function empty_error_handler()
{
	return true;
}

/**
 * @intellisense
 */
function n_printDebug()
{
}

/**
 * @param Array arr
 */
function getArrayWithoutObjects( $arr )
{
	$copyArr = $arr;
	
	foreach( $arr as $idx => $val )
	{
		$type = gettype( $val );
		if( $type == "object" )
			$copyArr[ $idx ] = get_class( $val )." Object";
		if( $type == "array" )
			$copyArr[ $idx ] = getArrayWithoutObjects( $val );
	}
	
	return $copyArr;
}

/**
 * @param Mixed value
 * @param Boolean return
 * @param Number n
 */
function runner_print_r( $value, $return = false )
{
	$valueCopy = $value;
	$type = gettype( $value );
	
	if( $type == "object" )
		$valueCopy = get_class( $valueCopy )." Object";
	
	if( $type == "array" )
		$valueCopy = getArrayWithoutObjects( $valueCopy );
	
	return print_r( $valueCopy, $return );
}

/**
 * @intellisense
 */
function in_arrayi($needle, $haystack)
{
	$found = false;
    foreach( $haystack as $value )
	{
        if( strtolower( $value ) == strtolower( $needle ) )
            $found = true;
    }
    return $found;
}

/**
 * @intellisense
 */
function f_printDebug()
{
	}

if(!function_exists("hex2bin"))
{
/**
 * Convert HEX string to BIN string
 * @param {string} HEX source string
 * @return {string} BIN string
 * @intellisense
 */
function hex2bin($source)
	 {
		if(!is_string($source) || strlen($source) == 0 || strlen($source) % 2 > 0)
			return '';
		$bin = "";
		$i = 0;
		do {
			$bin .= chr(hexdec($source[$i].$source[$i + 1]));
			$i += 2;
		} while ($i < strlen($source));
		return $bin;
	}
}

/**
 * @intellisense
 */
function toPHPTime($datevalue)
{
	$arr = db2time($datevalue);
	return mktime($arr[3],$arr[4],$arr[5],$arr[1],$arr[2],$arr[0]);
}

/**
 * @intellisense
 */
function imageCreateThumb($new_width,$new_height,$img_width,$img_height,$file_path,$options,$new_file_path,$uploadedFile)
{
	$new_img = @imagecreatetruecolor($new_width, $new_height);
	switch (strtolower(substr(strrchr($file_path, '.'), 1))) {
		case 'jpg':
		case 'jpeg':
			$src_img = @imagecreatefromjpeg($file_path);
			$image_quality = isset($options['jpeg_quality']) ?
				$options['jpeg_quality'] : 75;
			$success = @imagecopyresampled(
					$new_img,
					$src_img,
					0, 0, 0, 0,
					$new_width,
					$new_height,
					$img_width,
					$img_height
				) && imagejpeg($new_img, $new_file_path, $image_quality);
			break;
		case 'gif':
			@imagecolortransparent($new_img, @imagecolorallocate($new_img, 0, 0, 0));
			$src_img = @imagecreatefromgif($file_path);
			$image_quality = null;
			$success = @imagecopyresampled(
					$new_img,
					$src_img,
					0, 0, 0, 0,
					$new_width,
					$new_height,
					$img_width,
					$img_height
				) && imagegif($new_img, $new_file_path, $image_quality);
			break;
		case 'png':
			@imagecolortransparent($new_img, @imagecolorallocate($new_img, 0, 0, 0));
			@imagealphablending($new_img, false);
			@imagesavealpha($new_img, true);
			$src_img = @imagecreatefrompng($file_path);
			$image_quality = isset($options['png_quality']) ?
				$options['png_quality'] : 9;
			$success = @imagecopyresampled(
					$new_img,
					$src_img,
					0, 0, 0, 0,
					$new_width,
					$new_height,
					$img_width,
					$img_height
				) && imagepng($new_img, $new_file_path, $image_quality);
			break;
		default:
			$src_img = null;
			$success = false;
	}
	// Free up memory (imagedestroy does not delete files):
	@imagedestroy($src_img);
	@imagedestroy($new_img);
	return $success;
}
/**
 * @intellisense
 */
function uploadFiles($option)
{
	$result = null;
	if(isset($_FILES[$option])){
		$result = array();
		$upload = $_FILES[$option];
		if(is_array($upload['tmp_name'])) {
            foreach ($upload['tmp_name'] as $index => $value)
            {
            	$tempFile = array();
				$tempFile['tmp_name'] = $upload['tmp_name'][$index];
                $tempFile['name'] = $upload['name'][$index];
                $tempFile['size'] = $upload['size'][$index];
                $tempFile['type'] = $upload['type'][$index];
                $tempFile['error'] = $upload['error'][$index];
                $result[] = $tempFile;
            }
		}
		else
		{
			$result[] = $upload;
		}
	}
	return $result;
}
/**
 * @intellisense
 */
function upcount_name_callback($matches)
{
	$index = isset($matches[1]) ? intval($matches[1]) + 1 : 1;
	$ext = isset($matches[2]) ? $matches[2] : '';
	return ' ('.$index.')'.$ext;
}

/**
 * @intellisense
 */
function upcount_name($name)
{
	return preg_replace_callback(
		'/(?:(?: \(([\d]+)\))?(\.[^.]+))?$/',
		'upcount_name_callback',
		$name,
		1
	);
}

/**
 * @intellisense
 */
function trim_file_name($name, $type, $index, $obj)
{
        // Remove path information and dots around the filename, to prevent uploading
        // into different directories or replacing hidden system files.
        // Also remove control characters and spaces (\x00..\x20) around the filename:
        $file_name = trim(basename($name,""));
        // Add missing file extension for known image types:
        if (strpos($file_name, '.') === false &&
            preg_match('/^image\/(gif|jpe?g|png)/', $type, $matches)) {
            $file_name .= '.'.$matches[1];
        }
        while(isset($_SESSION["mupload_".$obj->formStamp][$file_name])) {
            $file_name = upcount_name($file_name);
        }
        return $file_name;
    }

/**
 * @intellisense
 */
function upload_File($uploadedFile, $destination)
{
	if ($uploadedFile && is_uploaded_file($uploadedFile["tmp_name"]))
	{
		move_uploaded_file($uploadedFile["tmp_name"], $destination);
		clearstatcache();
		clearstatcache();
	}
}

/**
 * GDExist
 * Fake function. Only matter something for ASP, because in PHP GD always exist (since 4.3.0)
 * @intellisense
 */
function GDExist()
{
	return true;
}

/**
 * @intellisense
 */
function makeSurePathExists($abspath)
{
	if(!file_exists($abspath))
	{
		return mkdir($abspath, 0777,  true);
	}
	if(is_dir($abspath))
		return true;
	return false;
}

/**
 * @intellisense
 */
function createControlClass($className, $field, $pageObject, $id, $connection)
{
	include_once(getabspath("classes/controls/".$className.".php"));
	return new $className($field, $pageObject, $id, $connection);
}

/**
 * @intellisense
 */
function createViewControlClass($className, $field, $container, $pageObject)
{
	include_once(getabspath("classes/controls/".$className.".php"));
	return new $className($field, $container, $pageObject);
}

/**
 * @intellisense
 */
function getQueryString()
{
	return getenv('QUERY_STRING') ? urldecode(getenv('QUERY_STRING')) : $_SERVER["QUERY_STRING"];
}

/**
 * GetTableLink
 * Form proper link for table. Really need for ASP.Net MVC
 * @param {string} Table name
 * @param {string} Page type
 * @param (string) GET query string
 */
function GetTableLink($table, $pageType = "", $getParams = "")
{
	$url = $table;
	if($pageType != "")
	{
		$url .= "_".$pageType;
	}
	$url .= ".php";
	if($getParams != "")
	{
		$url .= "?".$getParams;
	}

	return $url;
}

/**
 * GetLocalLink
 * Only needed in .NET
 * 
 * @param {string} Table name
 * @param {string} Page type
 * @param (string) GET query string
 */
function GetLocalLink($table, $pageType = "", $getParams = "")
{
	//	used in ASP.NET only
	return GetTableLink( $table, $pageType, $getParams );
}


function GetTemplateName($table, $templateName)
{
	$result = $templateName.".htm";
	if($table != "")
	{
		$result = $table."_".$result;
	}

	return $result;
}

function HeaderRedirect($table, $pageType = "", $getParams = "")
{
	header("Location: ".GetTableLink($table, $pageType, $getParams));
}

function cross_sort_arr_y($a,$b)
{
	global $group_sort_y;
	if($group_sort_y[$a]>$group_sort_y[$b])
		return true;
	else
		return false;
}

function SortForCrossTable(&$sort_y)
{
	usort($sort_y, "cross_sort_arr_y");
}

function getYMDdate($unixTimeStamp)
{
	return date("Y-m-d", $unixTimeStamp);
}

function IsJSONAccepted()
{
	return isset($_SERVER['HTTP_ACCEPT']) && (strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);
}

function GetRootPathForResources($filePath)
{
	return $filePath;
}

function GetPageURLWithGetParams()
{
	$pagename = $_SERVER["REQUEST_URI"];
	if(!$pagename)
	{
		$pagename=basename(__file__);
		$params="";
		foreach($_GET as $k=>$v)
		{
			if(strlen($params))
				$params.="&";
			$params.=rawurlencode($k)."=".rawurlencode($v);
		}
		if(strlen($params))
			$pagename.="?".$params;
	}
	if(strpos($pagename,"?")===false)
	{
		$pagename.="?pdf=1";
	}
	else
	{
		$pagename.="&pdf=1";
	}

	return $pagename;
}

/*
 * Dummy for ASP.Net MVC
 */
function GetWebRootPath()
{
	return "";
}

function GetCaptchaPath()
{
	return "securitycode.php";
}

function GetCaptchaSwfPath()
{
	return "securitycode.swf";
}

function verifyRecaptchaResponse( $response ) {
	$VerifyUrl = "https://www.google.com/recaptcha/api/siteverify?";
	$data = array();
	
	$captchaSettings = GetGlobalData("CaptchaSettings", "");
	
	$data['secret'] = $captchaSettings["secretKey"];
	$data['response'] = $response;
	$data['remoteIp'] = $_SERVER["REMOTE_ADDR"];

	foreach ($data as $key => $value) {
		$req .= $key . '=' . rawurlencode($value) . '&';
	}
	// Cut the last '&'
	$req = substr($req, 0, strlen($req)-1);

	$response = myurl_get_contents($VerifyUrl . $req);
	$answers = my_json_decode($response, true);
	return $answers['success'];
}

/**
 * This function is used for my_json_encode() function.
 * It will emulate the JSON_UNESCAPED_UNICODE option for json_encode() function.
 */
function json_mb_encode_numericentity(&$item, $key)
{
	if (is_string($item))
		$item = runner_encode_numeric_entity($item, array(0x80, 0xffff, 0, 0xffff), 'UTF-8');
}

function makeFloat($value)
{
	$value = str_replace(",",".",$value);
	if(is_numeric($value))
		return (float)$value;
	else
		return "";
}

/**
 * Get the data array containing the flag indicating if
 * the string passed is HTML entity and the length of
 * the HTML entity that is set by the string
 * @param String encodedEntity
 * @return Array
 */
function getHTMLEntityData($encodedEntity)
{
	$entity = html_entity_decode( $encodedEntity );
	if( $encodedEntity == $entity )
		return array("isHTMLEntity"=> false, "entityLength"=> 0);

	return array("isHTMLEntity"=> true, "entityLength"=> runner_strlen( $entity ));
}

/**
 * PHP html entity decode
 */
function runner_html_entity_decode($str)
{
	// '&nbsp;' entity is not ASCII code 32 but ASCII code 160 (0xa0)
	return html_entity_decode(str_replace('&nbsp;', ' ', $str), ENT_COMPAT | ENT_HTML401, 'ISO-8859-1');
}

/**
 * PHP htmlspecialchars wrapper
 */
function runner_htmlspecialchars($str)
{
	global $useUTF8;

	if($useUTF8)
		return htmlspecialchars($str);

	return htmlspecialchars($str, ENT_COMPAT | ENT_HTML401, 'ISO-8859-1');
}

/**
 * PHP strlen wrapper
 */
function runner_strlen($str)
{
	global $useUTF8, $mbEnabled;

	if( !$useUTF8 )
		return strlen($str);

	if( $mbEnabled )
		return mb_strlen($str, 'UTF-8');

	//php.net not ISO-8859-1 characters are converted to '?' (one char).
	return strlen( utf8_decode($str) );
}

/**
 * PHP strpos wrapper
 */
function runner_strpos($haystack, $needle, $offset = 0)
{
	global $useUTF8, $mbEnabled;

	if( !$useUTF8 )
		return strpos($haystack, $needle);

	if( $mbEnabled )
		return mb_strpos($haystack, $needle,  $offset, 'UTF-8');

	if( $offset < 0 )
		return FALSE;

	if( $offset > 0 )
		$haystack = runner_substr($haystack, $offset, runner_strlen($haystack) - $offset);

	$pos = strpos($haystack, $needle);
	if( $pos === FALSE )
		return $pos;

	return $offset + runner_strlen( substr($haystack, 0, $pos) );
}

/**
 * PHP strrpos wrapper
 */
function runner_strrpos($haystack, $needle, $offset = 0)
{
	global $useUTF8, $mbEnabled;

	if( !$useUTF8 )
		return strrpos($haystack, $needle, $offset);

	if( $mbEnabled )
		return mb_strrpos($haystack, $needle, $offset, 'UTF-8');

	if( $offset < 0 )
		$offset = runner_strlen($haystack) + $offset;

	if( $offset > 0 )
		$haystack = runner_substr($haystack, $offset, runner_strlen($haystack) - $offset);

	$rpos = strrpos($haystack, $needle);
	if( $rpos === FALSE )
		return $rpos;

	return $offset + runner_strlen( substr($haystack, 0, $rpos) );
}

/**
 * PHP substr wrapper
 * @param String string
 * @param Number start (>= 0)
 * @param Number length (>= 0)
 * @return String
 */
function runner_substr($string, $start, $length)
{
	if( !$length )
		return "";

	global $useUTF8, $mbEnabled;

	if( !$useUTF8 )
		return substr($string, $start, $length);

	if( $mbEnabled )
		return mb_substr($string, $start, $length, 'UTF-8');

	$end = $start + $length;
	//j is the real chars counter;
	$u8start = $u8end = $j = 0;
	for($i = 0; $i < strlen($string), $j < $end; $i++)
	{
		if($j == $start)
			$u8start = $i;

		$ordord = ord($string[$i]);
		switch(true)
		{
			case (0xFC == (0xFC & $ordord)):
				$i = $i + 5;
			break;
			case (0xF8 == (0xF8 & $ordord)):
				$i = $i + 4;
			break;
			case (0xF0 == (0xF0 & $ordord)):
				$i = $i + 3;
			break;
			case (0xE0 == (0xE0 & $ordord)):
				$i = $i + 2;
			break;
			case (0xC0 == (0xC0 & $ordord)) :
				$i = $i + 1;
		}
		$j++;
	}
	$u8end = $i;

	return substr($string, $u8start, $u8end - $u8start );
}

/**
 * PHP mb_convert_encoding wrapper
 */
function runner_convert_encoding($str, $to_encoding, $from_encoding)
{
	global $mbEnabled;

	if( $mbEnabled )
		return mb_convert_encoding($str, $to_encoding, $from_encoding);

	return $str;
}

/**
 * PHP mb_encode_numericentity wrapper
 */
function runner_encode_numeric_entity($str, $convmap, $encoding)
{
	global $mbEnabled;

	if( $mbEnabled )
		return mb_encode_numericentity($str, $convmap, $encoding);

	return $str;
}

/**
 * PHP mb_decode_numericentity wrapper
 */
function runner_decode_numeric_entity($str, $convmap, $encoding)
{
	global $mbEnabled;

	if( $mbEnabled )
		return mb_decode_numericentity($str, $convmap, $encoding);

	return $str;
}

function hasNonAsciiSymbols($str)
{
	$str = "".$str;
	for($i=0; $i<strlen($str); $i++)
	{
		if(ord($str[$i])>127)
			return true;
	}
	return false;
}

function runner_serialize_array($arr)
{
	return serialize($arr);
}

function runner_unserialize_array($arr)
{
	return unserialize($arr);
}

function runner_getimagesize($file_name, $uploadedFile)
{
	return @getimagesize($file_name);
}

function array_merge_assoc($arr1, $arr2)
{
	return array_merge($arr1, $arr2);
}

function getIntervalLimitsExpressions($table, $field, $idx, $isLowerBound)
{
	return getIntervalLimitsExprs($table, $field, $idx, $isLowerBound);
}

/**
 * @return Boolean
 */
function useMySQLiLib()
{
	global $useOldMysqlLib;

	if( !$useOldMysqlLib )
		return extension_loaded("mysqli") === true;

	return extension_loaded("mysql") !== true;
}

/**
 * @return Boolean
 */
function isSqlsrvExtLoaded()
{
	return extension_loaded("sqlsrv") === true;
}

/**
 * @return Boolean
 */
function useMSSQLWinConnect()
{
	return strtoupper(substr(PHP_OS, 0, 3)) == "WIN" && substr(PHP_VERSION, 0, 1) > '4' && class_exists ('COM');
}

function cutBOM( $line )
{
	if( substr($line, 0, 3) == "\xEF\xBB\xBF" )
		return substr($line, 3);
	return $line;
}

function printBOM( )
{
	echo "\xEF\xBB\xBF";
}

function runner_save_textfile( $fileName, $txtData )
{
	runner_save_file( $fileName, $txtData );
}

function deleteTemporaryFilesFromDirTMP()
{
	return false;
}

function runner_date_format($param, $date="")
{
	if($date == "")
		return date($param);
	else
		return date($param, $date);
}

function runner_set_page_timeout( $seconds )
{
	if( !ini_get("safe_mode") )
		set_time_limit(300);
}

/**
 * password_hash wrapper
 */
function getPasswordHash( $pass )
{
	return password_hash( $pass, PASSWORD_BCRYPT );
}

/**
 * password_hash wrapper
 */
function passwordVerify( $pass, $hash )
{
	return password_verify( $pass, $hash ) ;
}


function callDashboardSnippet( $name, $eventsObject )
{
	$snippetData = array();
	ob_start();
	$header = "";
	$eventsObject->$name( $header );
	$snippetData["header"] = $header;
	$snippetData["body"] = ob_get_contents();	
	ob_end_clean();
	return $snippetData;
}

?>