<?php
//require_once("../../Database.php");
require_once ( "../define_constant_system.php");
require_once (PATH_MODEL . "/__connection.php");
require_once (PATH_MODEL . "/automail_conn.php");
require_once (PATH_MODEL . "/getScrap_conn.php");//get getScrap function

require_once (PATH_DATA . "/detachedSOLINE.php");
require_once (PATH_DATA . "/formatDate.php");

//connect 
$conn = getConnection(); 	
	
//$_POST['data'] = ['ATE371665B','ATE371668B','ATE369972B','ATV377842A'];
//$_POST['data'] = ['ATV377842A','ATE371665B'];
$ITEM = $_GET['item'];
$SQL = "SELECT DISTINCT material_code,material_des FROM ms_color where internal_item='$ITEM';";
//$RESULT = MiQuery($SQL,$dbMi2);
$RESULT = mysqli_query($conn, $SQL);
$RESULT = mysqli_fetch_all($RESULT,MYSQLI_ASSOC);
$ITEM_DATA = [];
if(!empty($RESULT)){
	foreach ($RESULT as $MASTER){
		$material_code 		= 	$MASTER['material_code'];
		$material_des 		= 	$MASTER['material_des'];
		$ITEM_DATA[] = [
			'material_code'			=> $material_code,
			'material_des'			=> $material_des,
			];
	}
}

if ($conn ) mysqli_close($conn);

echo json_encode($ITEM_DATA);