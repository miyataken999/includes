<?
// Parameters
// $values     - array of values to be written to the database.
//               To access specific field value use $values["FieldName"]
//               "dict" is an alternate name for this parameter.
// $message    - place the message to be displayed into this variable.
// $inline     - equals to true when the Inline Add in process, false otherwise
// $pageObject - an object of Page class representing the current page


$nowtime = date("Y-m-d H:i:s");
$user_id = $_SESSION['uid'];
$current_page_pass = $_SERVER["REQUEST_URI"];

if($user_id != ""){
	if($user_id != ""){
		/**********************************************************
		登録者
		**********************************************************/
		if($_SESSION['uid'] != ""){
			$values["created_by"] = $_SESSION['uid'];
		}

		/**********************************************************
		登録日
		**********************************************************/
		if($nowtime != ""){
			$values["created_by"] = $nowtime;
		}
	}

}