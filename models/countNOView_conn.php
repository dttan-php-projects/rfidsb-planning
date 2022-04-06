<?php
	//check login
	if(!isset($_COOKIE["VNRISIntranet"])) { header('Location: login.php');//check login
	} else { $USER_CUR = $_COOKIE["VNRISIntranet"]; }

	date_default_timezone_set('Asia/Ho_Chi_Minh');
	
	function formatDate($value){ return date('d-M-y',strtotime($value)); } 
	
	require_once ( "../define_constant_system.php");
	require_once (PATH_MODEL . "/__connection.php");  
	
	//connect
		$conn252 = getConnection();
		$table = "rfid_po_save";

	// get current date
		$current = date('Y-m-d');

	// init 
		$countAll = 0;
		$countCurrent = 0;

	// query model
		$fields = " PO_NO ";
		$sql = " SELECT $fields  FROM $table ORDER BY PO_UPDATED_TIME DESC; ";
		$query = mysqli_query($conn252, $sql);
		if (!$query) {
			$status = false;
		} else {
			$countAll = mysqli_num_rows($query);

			// count current date
				$sql = " SELECT $fields  FROM $table WHERE PO_SAVE_DATE='$current' ORDER BY PO_UPDATED_TIME DESC ";
				$query = mysqli_query($conn252, $sql);
				$countCurrent = mysqli_num_rows($query);

			$status = true;
		}

		if ($conn252) mysqli_close($conn252);
		
	
	// result 
		$results = [
			'status' => $status,
			'countAll' => $countAll,
			'countCurrent' => $countCurrent
		];
		echo json_encode($results);exit();
	

	