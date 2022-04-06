<?php

	// require_once ( "../define_constant_system.php");
	// require_once (PATH_MODEL . "/__connection.php");
	
	function getCustomerJob($SO_LINE) {
		
		
		//Lưu ý đây là trường hợp RFID nên chỉ nhập vào là SO_LINE (không nhập dạng SO) nên xử lý theo hướng nhập SO_LINE
		$conn = getConnection138();
		$table_vnso = "vnso";
		$table_total = "vnso_total";
		
		$GPM = '';

		$SO_LINE_ARR 	= explode('-',$SO_LINE);
		$ORDER_NUMBER 	= $SO_LINE_ARR[0];
		$LINE_NUMBER 	= $SO_LINE_ARR[1];
		
		$result_gpm = array();
		$query 	= mysqli_query($conn, "SELECT CUSTOMER_JOB FROM $table_vnso WHERE ORDER_NUMBER='$ORDER_NUMBER' AND LINE_NUMBER='$LINE_NUMBER' ORDER BY ID DESC LIMIT 0,1;");
		if(mysqli_num_rows($query) > 0 ) {
			$result_gpm = mysqli_fetch_array($query, MYSQLI_ASSOC);
		} else {
			$query 	= mysqli_query($conn, "SELECT CUSTOMER_JOB FROM $table_total WHERE ORDER_NUMBER='$ORDER_NUMBER' AND LINE_NUMBER='$LINE_NUMBER' ORDER BY ID DESC LIMIT 0,1;");
			if(mysqli_num_rows($query) > 0 ) {
				$result_gpm = mysqli_fetch_array($query, MYSQLI_ASSOC);
			}
		}

		// close db
			if ($conn) mysqli_close($conn);
			
		// check data
		if(!empty($result_gpm) ) {

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
		}

		return $GPM;
	}

	function customerJob($PO_SO_LINE, $PO_RBO, $PO_INK_CODE ) {

		if (strpos(strtoupper($PO_RBO), 'NIKE') !== false ) {
			$customerJob = '';
		} else {
			$INK_CODE_CJ_ARR = ['INKJET', 'EPSON', 'KIARO D', 'QL800' ];
			foreach ($INK_CODE_CJ_ARR as $INK_CODE_CJ ) {
				if (strpos(strtoupper($PO_INK_CODE), $INK_CODE_CJ) == false ) {
					$customerJob = getCustomerJob($PO_SO_LINE);
					break;
				} else {
					$customerJob = '';
				}
			}
		
		}

		return $customerJob;
	}
	