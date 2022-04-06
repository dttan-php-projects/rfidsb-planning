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

	// get all rfidsb ordered item in rfid_po_master_item_combine table (1)
	function getSpecialOrderedItem() 
	{

		$results = array();
		$conn = getConnection();

		$query = mysqli_query($conn, "SELECT * FROM `rfid_po_master_item_combine`; " );
		if (mysqli_num_rows($query) > 0 ) {
			$masterCombine = mysqli_fetch_all($query, MYSQLI_ASSOC);
			foreach ($masterCombine as $value ) {
				$results[] = array('ordered_item_rfid' => trim($value['ordered_item_rfid']) );
			}
		}

		// close db
			mysqli_close($conn);

		return $results;
	}

	// get all orders in rfid sb (2)
	function getAllOrders($ORDER_NUMBER)
	{
		// Lấy tất cả các Ordered Item RFID từ Ryo (bảng `rfid_po_master_item_combine`)
			$array = getSpecialOrderedItem();	
			
			$orderedItemList = '';
			foreach ($array as $key => $value ) {
				// Nối thêm ký tự ^ (tìm từ vị trí bắt đầu sử dụng REGEXP trong mysql)
				if ($key == 0 ) {
					$orderedItemList .= '^' . trim($value['ordered_item_rfid']);	
				} else {
					$orderedItemList .= '|^' . trim($value['ordered_item_rfid']);
				}
				
			}
		
		// query vnso hoac vnso_total
			$results = array();
			
			$conn = getConnection138();
			$where = " ORDER_NUMBER = '$ORDER_NUMBER' AND ORDERED_ITEM REGEXP '$orderedItemList' ORDER BY LENGTH(LINE_NUMBER), LINE_NUMBER ASC;"; 
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

	// split big groups in orders
	function splitBigGroups($ORDER_NUMBER, $LINE_NUMBER)
	{
		// connect db
		$conn = getConnection();
		$table = 'rfid_upc_order_groups';

		$status = false;
		$qtyTotal = 0;
		$data = array(); 
		$allOrders = getAllOrders($ORDER_NUMBER);
		
		$countOrders = count($allOrders); 
		$count = 0;
		if (!empty($allOrders) ) {
			$breakBigGr = false;
			$big_group = 1;

			// Xóa các group trong bảng
				$order_number_tmp = $allOrders[0]['ORDER_NUMBER'];
				$line_number_tmp = $allOrders[0]['LINE_NUMBER'];
				$queryCheck = mysqli_query($conn, "SELECT `big_group` FROM $table WHERE `order_number`='$order_number_tmp' AND `line_number`='$line_number_tmp'; " );
				if (mysqli_num_rows($queryCheck) > 0 ) {
					$bigGroupArrTmp = mysqli_fetch_array($queryCheck, MYSQLI_ASSOC);
					$big_group_tmp = $bigGroupArrTmp['big_group'];
					mysqli_query($conn, "DELETE FROM $table WHERE `order_number`='$order_number_tmp';");
				}

			foreach ($allOrders as $key => $order ) {
				// check
				$line_number = (int)$order['LINE_NUMBER'];
				$qty = (int)$order['QTY'];
				if ($key == 0 ) $line_number_check = $line_number - 1 ;
				
				$qtyTotal += (int)$qty;
				

				// Nếu qty total > 30000 và line không liên tiếp thì dừng để save big group
				$breakBigGr = ($line_number !== ($line_number_check+1) ) ? true : false;
				if (!$breakBigGr) {
					$breakBigGr = ($qtyTotal > 30000 ) ? true : false;
				}
				
				if ( $breakBigGr == false ) {
					$data[] = array( 
						'order_number' => $order['ORDER_NUMBER'], 
						'line_number' => $order['LINE_NUMBER'], 
						'big_group' => $big_group, 
						'item' => $order['ITEM'], 
						'cust_po_number' => $order['CUST_PO_NUMBER'],
						'qty' => $qty
						
					);

				} else {

					$count++;
					
					if (!empty($data) ) {
						// remark
							$countLineBigGroup = count($data);
							$len = $countLineBigGroup - 1;
							$line_start = $data[0]['line_number'];
							$line_end = $data[$len]['line_number'];
							$remarkLines = "BIG GROUP: $countLineBigGroup LINE: $line_start - $line_end ";

						// save data to rfid_upc_order_groups table
						foreach ($data as $value ) {
							$order_number_s = $value['order_number'];
							$line_number_s = $value['line_number'];
							$big_group_s = $value['big_group'];
							$item = $value['item'];
							$cust_po_number = $value['cust_po_number'];
							$qty_s = $value['qty'];
							
							// save data
							$sql = "INSERT INTO $table (`order_number`, `line_number`, `big_group`, `item`, `cust_po_number`, `qty`, `remark`)  VALUES ('$order_number_s', '$line_number_s', '$big_group_s', '$item', '$cust_po_number', '$qty_s', '$remarkLines');";
							$result = mysqli_query($conn, $sql );
							if (!$result ) {
								// close db
									mysqli_close($conn);
								// echo "Query Error: $sql \n";
								return false;
							}

							$status = true;
							
						}

					}

					
					/* RESET DATA */ 
					$qtyTotal = $qty;
					// $line_number_check = $line_number - 1 ;
					$breakBigGr = false;
					$big_group++;
					$data = array();
					$data[] = array( 
						'order_number' => $order['ORDER_NUMBER'], 
						'line_number' => $order['LINE_NUMBER'], 
						'big_group' => $big_group, 
						'item' => $order['ITEM'], 
						'cust_po_number' => $order['CUST_PO_NUMBER'],
						'qty' => $qty
					);

				}

				// line check
					$line_number_check = $line_number;

			}

			if (!empty($data) ) {

				// remark
					$countLineBigGroup = count($data);
					$len = $countLineBigGroup - 1;
					$line_start = $data[0]['line_number'];
					$line_end = $data[$len]['line_number'];
					$remarkLines = "BIG GROUP: $countLineBigGroup LINE: $line_start - $line_end ";

				// save data to rfid_upc_order_groups table
				foreach ($data as $value ) {
					$order_number_s = $value['order_number'];
					$line_number_s = $value['line_number'];
					$big_group_s = $value['big_group'];
					$item = $value['item'];
					$cust_po_number = $value['cust_po_number'];
					$qty_s = $value['qty'];
					
					// save data
					// $sql = "INSERT INTO $table (`order_number`, `line_number`, `big_group`, `item`, `cust_po_number`, `qty`)  VALUES ('$order_number_s', '$line_number_s', '$big_group_s', '$item', '$cust_po_number', '$qty_s');";
					$sql = "INSERT INTO $table (`order_number`, `line_number`, `big_group`, `item`, `cust_po_number`, `qty`, `remark`)  VALUES ('$order_number_s', '$line_number_s', '$big_group_s', '$item', '$cust_po_number', '$qty_s', '$remarkLines');";
					$result = mysqli_query($conn, $sql );
					if (!$result ) {
						// close db
							mysqli_close($conn);
						return false;
					}
	
					$status = true;
					
				}
				
				
			}

		}

		

		

		// close db
			mysqli_close($conn);

		

		return $status;

	}

	// dang lam o day nha

	// split small groups in the big group in orders
	function splitSmallGroups($ORDER_NUMBER, $LINE_NUMBER)
	{
		// connect db
		$conn = getConnection();
		$table = 'rfid_upc_order_groups';
		$updated_by = isset($_COOKIE["VNRISIntranet"]) ? $_COOKIE["VNRISIntranet"] : '';

		$status = false;
		/* ------------------------------------------------------------------------
			LẤY DỮ LIỆU BIG GROUP ĐỂ PHÂN TÍCH SMALL GROUP (là đơn hàng)
		*/
		
			$queryCheck = mysqli_query($conn, "SELECT DISTINCT `big_group` FROM $table WHERE `order_number`='$ORDER_NUMBER' ORDER BY `big_group` ASC; " );
			if (mysqli_num_rows($queryCheck) > 0 ) {
				
				$allBiGroupOrders = mysqli_fetch_all($queryCheck, MYSQLI_ASSOC);
				// print_r($allBiGroupOrders); exit();
				foreach ($allBiGroupOrders as $bigGroupOrder ) {
					// print_r($bigGroupOrder); // exit();
					$big_group = $bigGroupOrder['big_group'];
					$query = mysqli_query($conn, "SELECT DISTINCT * FROM $table WHERE `order_number`='$ORDER_NUMBER' AND `big_group`='$big_group' ORDER BY LENGTH(`LINE_NUMBER`), LINE_NUMBER ASC; " );
					if (mysqli_num_rows($query) > 0 ) {
						
						$data = mysqli_fetch_all($query, MYSQLI_ASSOC);

						$small_group = 1;
						$qty_min_200 = 1;
						$cust_po_number_check = '';
						$count_line = 0;
						$item_check = '';
						$smallGroupData = array();
						foreach ($data as $key => $value ) {
							$order_number = $value['order_number'];
							$line_number = $value['line_number'];
							$item = $value['item'];
							$qty = $value['qty'];
							$cust_po_number = $value['cust_po_number'];

							if ($key == 0 ) {
								$item_check = $item;
								$cust_po_number_check = $cust_po_number;
								// qty >=200 set 1 else set 0
								$qty_min_200 = ($qty >= 200 ) ? 1 : 0; 
							} 
								
							/* 
								ĐIỀU KIỆN CÙNG SMALL GROUP (1 ĐƠN TỜ LỆNH SX)
								1. tất cả đơn trong group phải nhỏ hơn/bằng 200 hoặc lớn hơn 200
								2. customer po number giống nhau
								3. tổng số line <= 25
								4. item (internal item ) giống nhau (đợ Ryo phản hồi)
								5. Không có ngắt line (chắc chắn, do big group đã bắt trường hợp này)
							*/

							$breakSmallGroup = false;
							// check qty >=200 hoặc <200
								if ($qty_min_200 == 1 ) {
									if ($qty <200 ) $breakSmallGroup = true;
								} else {
									if ($qty >=200 ) $breakSmallGroup = true;
								}

							// // // check cust_po_number
							// // 	if ($cust_po_number !== $cust_po_number_check ) $breakSmallGroup = true;
							// // 	echo "Break PO: $breakSmallGroup  \n";
							// check count line > 25
								if ($count_line >= 25 ) $breakSmallGroup = true;
							// item check
								if ($item_check !== $item ) $breakSmallGroup = true;

								$item_check = $item;
								$cust_po_number_check = $cust_po_number;
								// qty >=200 set 1 else set 0
								$qty_min_200 = ($qty >= 200 ) ? 1 : 0; 
							
							// set small group data
								
								if ($breakSmallGroup == false ) {
									$smallGroupData[] = array(
										'order_number' => $order_number,
										'line_number' => $line_number,
										'small_group' => $small_group,
										'item' => $item,
										'cust_po_number' => $cust_po_number,
										'qty' => $qty
									);
								} else { // Nếu thỏa điều kiện dừng small group thì tăng số small group lên 1 và update small group
									// update small group data
										foreach ($smallGroupData as $keyU => $valueU ) {
											$order_number_up = $valueU['order_number'];
											$line_number_up = $valueU['line_number'];
											$small_group_up = $valueU['small_group'];
											$item_up = $valueU['item'];
											$cust_po_number_up = $valueU['cust_po_number'];
											$sql = "UPDATE $table SET `small_group`='$small_group_up', `item`='$item_up', `cust_po_number`='$cust_po_number_up', `updated_by`='$updated_by', `updated_date`=NOW()  WHERE `order_number`='$order_number_up' AND `line_number`='$line_number_up';";
											$result = mysqli_query($conn, $sql);
											if (!$result)
											{
												mysqli_close($conn);
												return false; // Nếu không save thành công thì dừng lại không làm lệnh
											} 

											$status = true;
										}
										

									$small_group++;
									$count_line = 0;
									$qty_min_200 = ($qty >= 200 ) ? 1 : 0; 

									// giữ giá trị tại dòng chuyển sang small group khác
									$smallGroupData = array();
									$smallGroupData[] = array(
										'order_number' => $order_number,
										'line_number' => $line_number,
										'small_group' => $small_group,
										'item' => $item,
										'cust_po_number' => $cust_po_number,
										'qty' => $qty,
									);

								}

								// Trường hợp không phải dừng điều kiện nhưng còn dữ liệu để lưu
								if (!empty($smallGroupData) ) {
									
									// update small group data
									foreach ($smallGroupData as $keyU => $valueU ) {
										$order_number_up = $valueU['order_number'];
										$line_number_up = $valueU['line_number'];
										$small_group_up = $valueU['small_group'];
										$item_up = $valueU['item'];
										$cust_po_number_up = $valueU['cust_po_number'];
										$sql = "UPDATE $table SET `small_group`='$small_group_up', `item`='$item_up', `cust_po_number`='$cust_po_number_up', `updated_by`='$updated_by', `updated_date`=NOW()  WHERE `order_number`='$order_number_up' AND `line_number`='$line_number_up';";
										$result = mysqli_query($conn, $sql);
										if (!$result)
										{
											mysqli_close($conn);
											return false; // Nếu không save thành công thì dừng lại không làm lệnh
										} 

										$status = true;
									}
								}


							// tmp
								$count_line++;
							

						}
						
					}

				}


			}

		
		// close db
			mysqli_close($conn);

		return $status;

	}

	// Lấy các đơn hàng theo big group và small group
	function getOrders($ORDER_NUMBER, $LINE_NUMBER) 
	{
		$conn = getConnection();
		$conn138 = getConnection138();
		$table = 'rfid_upc_order_groups';
		$table_vnso = 'vnso';
		$results = array();

		$sql = "SELECT `ITEM` FROM $table_vnso WHERE `ORDER_NUMBER`='$ORDER_NUMBER' AND `LINE_NUMBER`='$LINE_NUMBER'; ";
		$query_vnso = mysqli_query($conn138, $sql);
		if (mysqli_num_rows($query_vnso) == 0 ) {
			$sql = "SELECT ITEM FROM vnso_total WHERE `ORDER_NUMBER`='$ORDER_NUMBER' AND `LINE_NUMBER`='$LINE_NUMBER'; ";
			$query_vnso = mysqli_query($conn138, $sql);
			if (mysqli_num_rows($query_vnso) == 0 ) {
				$table_vnso = 'vnso_total';
			}
		}

		$lines = '';
		$sql_small = "SELECT `big_group`, `small_group` FROM $table WHERE `order_number`='$ORDER_NUMBER' AND `line_number`='$LINE_NUMBER'";
		$query_small = mysqli_query($conn, $sql_small );
		if (mysqli_num_rows($query_small) > 0 ) {
			$result_small = mysqli_fetch_array($query_small, MYSQLI_ASSOC);
			$small_group = trim($result_small['small_group']);
			$big_group = trim($result_small['big_group']);
			$sql_1 = "SELECT * FROM $table WHERE `order_number`='$ORDER_NUMBER' AND `small_group`='$small_group' AND `big_group`='$big_group'; ";
			$query = mysqli_query($conn, $sql_1 );
			if (mysqli_num_rows($query) > 0 ) {
				$smallGroupArr = mysqli_fetch_all($query, MYSQLI_ASSOC);
				foreach ($smallGroupArr as $small ) {
					$line = $small['line_number'];
					$lines .= "'$line',";
				}
				
			}
		}

		

		if (!empty($lines) ) {
			$lines = substr($lines, 0, -1);
			$lines = rtrim($lines,',');
			
			// echo "lines: $lines <br/>\n";
			$sqlOK = "SELECT * FROM $table_vnso WHERE `ORDER_NUMBER`='$ORDER_NUMBER' AND `LINE_NUMBER` IN ($lines) ORDER BY LENGTH(`LINE_NUMBER`), `LINE_NUMBER` ASC; ";
			$queryOK = mysqli_query($conn138, $sqlOK);
			if (mysqli_num_rows($queryOK) > 0 ) {
				$results = mysqli_fetch_all($queryOK, MYSQLI_ASSOC);
			}
		}

		mysqli_close($conn);
		mysqli_close($conn138);

		return $results;

	}

	function remarkBigGroupCombine($ORDER_NUMBER, $LINE_NUMBER )
	{
		$conn = getConnection();
		$table = 'rfid_upc_order_groups';
		$remark = '';

		$query = mysqli_query($conn, "SELECT `remark` FROM $table WHERE `order_number`='$ORDER_NUMBER' AND `line_number`='$LINE_NUMBER'  ORDER BY `small_group` ASC LIMIT 1; " );
		if (mysqli_num_rows($query) > 0 ) {
			$result = mysqli_fetch_array($query, MYSQLI_ASSOC);
			$remark = !empty($result) ? $result['remark'] : '';
		}

		return $remark;
	}


	// ------------  Get SOLine
		$SO_LINE = $_GET['SO_LINE'];
		// $SO_LINE = '50164430-4';

	//connect 
		$conn252 = getConnection();
		$conn138 = getConnection138();

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

	// get data VNSO
		$smallGroupResults = false;
		$bigGroupResults = splitBigGroups($SO_LINE_ARR[0], $SO_LINE_ARR[1]);
		if ($bigGroupResults ) {
			$smallGroupResults = splitSmallGroups($SO_LINE_ARR[0], $SO_LINE_ARR[1]);
		}

	
	// check group
	if ($smallGroupResults == false ) {
		$response = [
			'status' => false,
			'mess' =>  "Không tách được nhóm đơn hàng!!! "
		];
		echo json_encode($response ); exit();
	} else {
		
		$dataResult = array();

		$resultOrders = getOrders($SO_LINE_ARR[0], $SO_LINE_ARR[1]);

		// load data
		$index=0;
		
		$count_line = !empty($resultOrders) ? count($resultOrders) : 0;

		$SOLINE_SAMPLE = remarkBigGroupCombine($SO_LINE_ARR[0], $SO_LINE_ARR[1]);
		foreach ($resultOrders as $key => $row ) {
			

			$ORDER_NUMBER = isset($row['ORDER_NUMBER']) ? trim($row['ORDER_NUMBER']):'';
			$LINE_NUMBER = isset($row['LINE_NUMBER']) ? (int)(trim($row['LINE_NUMBER'])) : '';


			/* ------------------------------------------------------------------------------------------------
				GET ALL DATA
			------------------------------------------------------------------------------------------------ */
				$ORACLE_ITEM = isset($row['ITEM']) ? trim($row['ITEM']) : '';
			
				//check trường hợp item oracle là: VN FREIGHT CHARGE thì bỏ qua
					if(strpos($ORACLE_ITEM,'VN')!==FALSE||strpos($ORACLE_ITEM,'FREIGHT')!==FALSE||strpos($ORACLE_ITEM,'CHARGE"')!==FALSE) continue;

				$QTY = isset($row['QTY']) ? (int)trim($row['QTY']) : 0;
				$ORDERED_ITEM = !empty($row['ORDERED_ITEM']) ? substr($row['ORDERED_ITEM'], 0,9) : '';
				$CUST_PO_NUMBER = !empty($row['CUST_PO_NUMBER']) ? trim($row['CUST_PO_NUMBER']) : '';

				$ORDERED_DATE = !empty($row['ORDERED_DATE']) ? date('d-M-y',strtotime(trim($row['ORDERED_DATE']) ) ) : '';
				$REQUEST_DATE = !empty($row['REQUEST_DATE']) ? formatDate(trim($row['REQUEST_DATE']) ) : '';
				$PROMISE_DATE = !empty($row['PROMISE_DATE']) ? formatDate(trim($row['PROMISE_DATE']) ) : '';

				$CS = isset($row['CS']) ? trim($row['CS']) : '';
				$BILL_TO_CUSTOMER = isset($row['BILL_TO_CUSTOMER']) ? trim($row['BILL_TO_CUSTOMER']) : '';
				$SHIP_TO_CUSTOMER = isset($row['SHIP_TO_CUSTOMER']) ? trim($row['SHIP_TO_CUSTOMER']) : '';

				if (strpos(strtoupper($SHIP_TO_CUSTOMER), "CONG TY TNHH WORLDON (VIET NAM)") !== false ) {
					$SHIP_TO_CUSTOMER = str_replace("CONG TY TNHH WORLDON (VIET NAM)","KH WORLDON",$SHIP_TO_CUSTOMER);
				}
			
				$ORDER_TYPE_NAME = isset($row['ORDER_TYPE_NAME']) ? trim($row['ORDER_TYPE_NAME']) : '';
				$PACKING_INSTRUCTIONS = isset($row['PACKING_INSTRUCTIONS']) ? trim($row['PACKING_INSTRUCTIONS']) : '';

			/* ***GET @SAMPLE ************************************************ */
				$SAMPLE = 0; //mặc định là không mẫu
				$SAMPLE = getSample($SO_LINE, $ORACLE_ITEM);//file automail get vnso

			/* *********** SET ORDER DATE *********************************************************/
				$ORDER_CREATE_DATE 	= date('d-M-y');
				
				$query_po_save_date 	= "SELECT PO_SAVE_DATE FROM rfid_po_save ORDER BY PO_SAVE_DATE DESC LIMIT 0,1";
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