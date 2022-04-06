<?php
	date_default_timezone_set('Asia/Ho_Chi_Minh');
	ini_set('max_execution_time',300);
	header("Content-Type: application/json; charset=utf-8");

	require_once ( "../define_constant_system.php");
	require_once (PATH_MODEL . "/__connection.php");

	require_once (PATH_MODEL . "/loadGridSO_conn_bk.php");
	require_once (PATH_MODEL . "/automail_conn.php");

	require_once (PATH_DATA . "/detachedSOLINE.php");
	require_once (PATH_DATA . "/formatDate.php");
?>
<?php
	
	$SO_LINE = $_GET['SO_LINE'];//GET SOLINE 

	$loadGridSO = loadGridSO($SO_LINE);
	//extract($loadGridSO);

	echo json_encode($loadGridSO);die;