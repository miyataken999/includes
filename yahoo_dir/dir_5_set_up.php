<?
//データベース接続
mb_internal_encoding("utf8");
// ini_set( 'display_errors', 1 );
include $_SERVER["DOCUMENT_ROOT"]."/include/connection.php";


$dir_1 = $_GET["dir_1"];
$dir_2 = $_GET["dir_2"];
$dir_3 = $_GET["dir_3"];
$dir_4 = $_GET["dir_4"];

$query = " SELECT distinct dir_5 FROM yahoo_all_dir WHERE dir_1 =  '".$dir_1."' AND dir_2 = '".$dir_2."' AND dir_3 = '".$dir_3."' AND dir_4 = '".$dir_4."' ";

$sql = mysql_query('SET NAMES utf8');
$sql = mysql_query($query);

?>
<option value="">選択してください。</option>
<?
while ($result = mysql_fetch_assoc($sql)) {
?>
<option value="<?=$result['dir_5'];?>"><?=$result["dir_5"];?></option>
<?
}

?>

