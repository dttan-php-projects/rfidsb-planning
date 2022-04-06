<?php
//require_once ("../../Database.php");
	require_once ( "../define_constant_system.php");
	require_once (PATH_MODEL . "/__connection.php");
	require_once (PATH_MODEL . "/automail_conn.php");
	require_once (PATH_MODEL . "/getScrap_conn.php");//get getScrap function

	require_once (PATH_DATA . "/detachedSOLINE.php");
	require_once (PATH_DATA . "/formatDate.php");

	//connect 
	$conn = getConnection();

	$SQL = "SELECT material_code,material_des,item_color FROM ms_color";
	//$RESULT = MiQuery($SQL,$dbMi2);
	$RESULT = mysqli_query($conn, $SQL);
	$RESULT = mysqli_fetch_all($RESULT,MYSQLI_ASSOC);
	$ITEM_DATA = [];
	if(!empty($RESULT)){
		foreach ($RESULT as $MASTER){
			$material_code 		= 	$MASTER['material_code'];
			$material_des 		= 	$MASTER['material_des'];
			$item_color 		= 	$MASTER['item_color'];
			$ITEM_DATA[] = [
				'material_code'			=> $material_code,
				'material_des'			=> $material_des,
				'item_color'			=> $item_color
				];
		}
	}

	if ($conn) mysqli_close($conn);
	
	echo json_encode($ITEM_DATA);