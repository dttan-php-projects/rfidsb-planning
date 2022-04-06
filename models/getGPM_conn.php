<?php
	date_default_timezone_set('Asia/Ho_Chi_Minh'); ini_set('max_execution_time',300); 
	header("Content-Type: application/json; charset=utf-8");

	require_once ( "../define_constant_system.php");
	require_once (PATH_MODEL . "/__connection.php");
	require_once (PATH_DATA . "/detachedSOLINE.php");

	// $table      = 'oe_soview_text';
	//connect 
		$conn = getConnection138();
		// $conn252 = getConnection("avery");
		$table_vnso = "vnso";
		$table_total = "vnso_total";
	// get data
		$SO_LINE = trim($_GET['SO_LINE']);
	// $SO_LINE = '45488534-1';

	//Lưu ý đây là trường hợp RFID nên chỉ nhập vào là SO_LINE (không nhập dạng SO) nên xử lý theo hướng nhập SO_LINE

	$SO_LINE_ARR 	= explode('-',$SO_LINE);
	$ORDER_NUMBER 	= $SO_LINE_ARR['0'];
	$LINE_NUMBER 	= $SO_LINE_ARR['1'];
	$query_gpm 		= "SELECT `CUSTOMER_JOB` FROM $table_vnso WHERE `ORDER_NUMBER`='$ORDER_NUMBER' AND `LINE_NUMBER`='$LINE_NUMBER' ORDER BY `ID` DESC LIMIT 0,1";
	$result_gpm 	= mysqli_query($conn, $query_gpm);
	if(mysqli_num_rows($result_gpm) < 1 ) {
		$query_gpm 		= "SELECT `CUSTOMER_JOB` FROM $table_total WHERE `ORDER_NUMBER`='$ORDER_NUMBER' AND `LINE_NUMBER`='$LINE_NUMBER' ORDER BY `ID` DESC LIMIT 0,1";
		$result_gpm 	= mysqli_query($conn, $query_gpm);
	}

	if(mysqli_num_rows($result_gpm) > 0 ) {

		$result_gpm = mysqli_fetch_array($result_gpm, MYSQLI_ASSOC);
		$GPM = trim($result_gpm['CUSTOMER_JOB']);
		$GPM = (strpos($GPM, ' ') !==false ) ? str_replace(' ', '',$GPM) : $GPM;

		//Lấy số ký tự số GPM, Nếu có ký tự / thì tách ra thành mảng từ ký tự /.  
		// Sau đó, chạy vòng lặp mảng này, Từng phần tử loại bỏ các ký tự đặc biệt, chữ ra, nếu cái nào chỉ có số thôi thì lấy GPM
		$GPM_len = 0;
		if (strpos($GPM,'/' ) !== false ) {
			// Trường hợp có dấu /
			$GPM_DETACHED = explode('/', $GPM );
			foreach ($GPM_DETACHED as $GPM_CHECK ) {
				$GPM_CHECK = preg_replace('/[^0-9]/', '', $GPM_CHECK);
				if (is_numeric($GPM_CHECK) ) {
					$GPM_len = strlen($GPM_CHECK);
					break;
				}
			}

		} else {
			// Không có dấu /
			$GPM_len = strlen($GPM); 
		}

		// Trường hợp này: Nếu GPM có nhiều số thì lấy số đầu
		if (strpos($GPM,',' ) !== false ) {
			$GPM_DETACHED = explode(',', $GPM );
			$GPM = $GPM_DETACHED[0];
		}
		
		// Loại bỏ các ký tự khác, chỉ giữ lại ký tự số
		$GPM = preg_replace('/[^0-9]/', '', $GPM);
		// Lấy đúng độ dài của GPM
		$GPM = is_numeric(substr($GPM,0,$GPM_len))?substr($GPM,0,$GPM_len):'';

		// True
		$response = [
			'status' => true,
			'data' => $GPM
		];

	}else{

		$response = [
			'status' => false,
			'mess' =>  ''
		];

	}

	echo json_encode($response);


	
