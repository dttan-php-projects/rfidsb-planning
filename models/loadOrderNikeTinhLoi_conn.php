<?php
	date_default_timezone_set('Asia/Ho_Chi_Minh');
	ini_set('max_execution_time',300);
	header("Content-Type: application/json; charset=utf-8");

	require_once ( "../define_constant_system.php");
	require_once (PATH_MODEL . "/__connection.php");
	require_once (PATH_MODEL . "/automail_conn.php");
	require_once (PATH_MODEL . "/getScrap_conn.php");//get getScrap function

	require_once (PATH_DATA . "/detachedSOLINE.php");
	require_once (PATH_DATA . "/formatDate.php");

	// get all orders in rfid sb (2)
	function getAllOrders($ORDER_NUMBER, $LINE_NUMBER )
	{
		
		// query vnso hoac vnso_total
			$results = array();
			$conn = getConnection138();
			$where = " ORDER_NUMBER = '$ORDER_NUMBER' AND `LINE_NUMBER` >= $LINE_NUMBER ORDER BY LENGTH(LINE_NUMBER), LINE_NUMBER ASC;"; 
			$query = mysqli_query($conn, "SELECT * FROM `vnso` WHERE $where " );
			if (mysqli_num_rows($query) > 0 ) {
				$results = mysqli_fetch_all($query, MYSQLI_ASSOC);
			} else {
				$query = mysqli_query($conn, "SELECT * FROM `vnso_total` WHERE $where " );
				if (mysqli_num_rows($query) > 0 ) {
					$results = mysqli_fetch_all($query, MYSQLI_ASSOC);
				}
			}

		// close db
			mysqli_close($conn);

		// results
			return $results;


	}

	// Lấy các đơn hàng theo big group và small group
	function getCombineOrders($ordersData ) 
	{

		$results = array();

		if (!empty($ordersData) ) {
			foreach ($ordersData as $key => $order ) {
				$qty = (int)$order['QTY'];
				$line_number = (int)$order['LINE_NUMBER'];
				$internal_item = $order['ITEM'];
				$cust_po_number = trim($order['CUST_PO_NUMBER']);
				

				if ($key == 0 ) {
					$qty_min_200 = ($qty < 200 ) ? 1 : 0; 
					$line_number_check = $line_number - 1;
					$internal_item_check = $internal_item;
					$cust_po_number_check = $cust_po_number;
				}

				// default
					$break = false;

				// Condition 1: check qty >=200 hoặc <200
					if ($qty_min_200 == 1 ) {
						if ($qty >= 200 ) $break = true;
					} else {
						if ($qty < 200 ) $break = true;
					}

					

				// Condition 2: count >25 lines => break
					if (count($results) >= 25 ) $break = true;

				// condition 3: consecutive line (liên tục)
					if ($line_number != ($line_number_check + 1) ) $break = true;

				// condition 4: sample internal item
					if ($internal_item != $internal_item_check ) $break = true;

				// condition 5: sample PO cust_po_number
					if ($cust_po_number != $cust_po_number_check ) $break = true;
 
				// check 
					if ($break == false ) {
						$results[] = $order; // get orders data
					} else {
						break;
					}


				// reset check
					$qty_min_200 = ($qty < 200 ) ? 1 : 0; 
					$line_number_check = $line_number;
					// $internal_item_check = $internal_item;
					// $cust_po_number_check = $cust_po_number;
				
			}
		}

		return $results;

	}

	// connect 
		$conn252 = getConnection();

	// ------------  Get SOLine
		$SO_LINE = $_GET['SO_LINE'];

	//get form type
		$FORM_TYPE = !empty($_COOKIE["print_type_rfsb"])?$_COOKIE["print_type_rfsb"]:'';

	//check form type
		if(empty($FORM_TYPE)){
			$response = [
				'status' => false,
				'mess' =>  "KHÔNG LẤY ĐƯỢC LOẠI FORM VUI LÒNG LIÊN HỆ QUẢN TRỊ (1)!"
			];
			echo json_encode($response);exit();
		}

	// tách SOLin thành mảng
		$SO_LINE_ARR = explode('-', $SO_LINE);
		$ORDER_NUMBER = $SO_LINE_ARR[0];
		$LINE_NUMBER = $SO_LINE_ARR[1];

	// get data VNSO
		$allData = getAllOrders($ORDER_NUMBER,$LINE_NUMBER ); 
		$resultOrders = getCombineOrders($allData );

	
	// check group
	if (empty($resultOrders) ) {
		$response = [
			'status' => false,
			'mess' =>  "Không tách được nhóm đơn hàng!!! "
		];
	} else {
		
		$dataResult = array();

		// load data
		$index=0;
		
		$count_line = count($resultOrders);

		foreach ($resultOrders as $key => $row ) {
			
			// get data 1
				$ORDER_NUMBER = trim($row['ORDER_NUMBER']);
				$LINE_NUMBER = trim($row['LINE_NUMBER']);
				$ORACLE_ITEM = trim($row['ITEM']);
				
			//check trường hợp item oracle là: VN FREIGHT CHARGE thì bỏ qua
				if(strpos($ORACLE_ITEM,'VN')!==FALSE||strpos($ORACLE_ITEM,'FREIGHT')!==FALSE||strpos($ORACLE_ITEM,'CHARGE"')!==FALSE) continue;

			// get data 2
				$QTY = (int)trim($row['QTY']);
				$ORDERED_ITEM = !empty($row['ORDERED_ITEM']) ? substr($row['ORDERED_ITEM'], 0,9) : '';
				$cust_po_number = trim($row['CUST_PO_NUMBER']);

				$ORDERED_DATE = !empty($row['ORDERED_DATE']) ? date('d-M-y',strtotime(trim($row['ORDERED_DATE']) ) ) : '';
				$REQUEST_DATE = !empty($row['REQUEST_DATE']) ? formatDate(trim($row['REQUEST_DATE']) ) : '';
				$PROMISE_DATE = !empty($row['PROMISE_DATE']) ? formatDate(trim($row['PROMISE_DATE']) ) : '';

				$CS = trim($row['CS']);
				$BILL_TO_CUSTOMER = trim($row['BILL_TO_CUSTOMER']);
				$SHIP_TO_CUSTOMER = trim($row['SHIP_TO_CUSTOMER']);
			
				$ORDER_TYPE_NAME = trim($row['ORDER_TYPE_NAME']);
				$PACKING_INSTRUCTIONS =trim($row['PACKING_INSTRUCTIONS']);

			/* ***GET @SAMPLE ************************************************ */
				$SAMPLE = 0; //mặc định là không mẫu
				$SAMPLE = getSample($SO_LINE, $ORACLE_ITEM); //file automail get vnso

			/* *********** SET ORDER DATE *********************************************************/
				$ORDER_CREATE_DATE 	= date('d-M-y');
				
				$query_po_save_date 	= "SELECT `PO_SAVE_DATE` FROM `rfid_po_save` ORDER BY `PO_SAVE_DATE` DESC LIMIT 0,1";
				$result_po_save_date 	= mysqli_query($conn252, $query_po_save_date);
				if (mysqli_num_rows($result_po_save_date)>0) {
					$result_po_save_date 	= mysqli_fetch_array($result_po_save_date);
					$PO_SAVE_DATE = $result_po_save_date['PO_SAVE_DATE'];
					
					if ( strtotime($PO_SAVE_DATE) > strtotime($ORDER_CREATE_DATE) ) {//chuyen ngay thanh so nguyen de so sanh
						$ORDER_CREATE_DATE = $PO_SAVE_DATE;
					}
					
				}

			/* *********** LOAD MASTER ITEM *********************************************************/
				include ("loadScriptNOCBSData.php");

			/* 
				20200702: Xử lý ĐH INKJET và ĐH EPSON
				- Nếu code mực = INKJET hoặc EPSON thì là đơn INKJET hoặc EPSON
				- số lượng mực gán = 0
				- Remark ĐH INKJET hoặc EPSON
				- Lấy số GPM và Layout của các đơn này (áp dụng cho tất cả RBO)
			*/
		
			// tách ra sau này dễ xử lý
				if (strpos(strtoupper($INK_CODE), 'INKJET') !== false ) {
					$INK_QTY = 0;
				} else if (strpos(strtoupper($INK_CODE), 'EPSON') !== false ) {
					$INK_QTY = 0;
				}

			// SOLINE_SAMPLE
				$SOLINE_SAMPLE = '';
		
			// get data list
				$id = $index+1;
				$dataResult[] = [
					'id' 	=> $id,
					'data' 	=> [$id,$ORDER_NUMBER,$LINE_NUMBER,$QTY,$ORACLE_ITEM,$ORDER_ITEM,$PROMISE_DATE,$REQUEST_DATE,$ORDERED_DATE,$CS,$RBO,$WIDTH,$HEIGHT,$INK_CODE,$INK_QTY,$INK_DES,$MATERIAL_CODE,$MATERIAL_QTY, $MATERIAL_DES,$GAP,$FORM_TYPE,$SAMPLE,$PACKING_INSTRUCTIONS,$BILL_TO_CUSTOMER,$count_line,$SHIP_TO_CUSTOMER,$ORDER_TYPE_NAME, $MATERIAL_REMARK, $INK_REMARK,$ORDER_CREATE_DATE,$SAMPLE15PCS,$SOLINE_SAMPLE]
				];



			$index++;
			

		} // end for

		// success
			$response = [
				'status' => true,
				'data' => $dataResult
			];


	}

	echo json_encode($response);die;