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



	//GET data: so#
	$SO_LINE = $_GET['SO_LINE'];//GET SOLINE 
	
	//connect 
	$conn252 = getConnection();
	$conn138 = getConnection138();
	$table_vnso = "vnso";
	$table_vnso_total = "vnso_total";

	//get form type
	$FORM_TYPE = !empty($_COOKIE["print_type_rfsb"])?$_COOKIE["print_type_rfsb"]:'';
	//check form type
	if(empty($FORM_TYPE)){
		$response = [
			'status' => false,
			'mess' =>  "KHÔNG LẤY ĐƯỢC LOẠI FORM VUI LÒNG LIÊN HỆ QUẢN TRỊ (1)!"
		];
		echo json_encode($response);die;
	}
		
	//detached SOLINE INPUT. GET :val_SO (SO_NUMBER), val_LINE, ...
	$detached = detachedSOLINE($SO_LINE);
	extract($detached);

	/* GET RECORD VNSO ******************** */
	// $field_vnso = 'ORDER_NUMBER,LINE_NUMBER,QTY,ITEM,PROMISE_DATE,REQUEST_DATE,ORDERED_DATE,CS,PACKING_INSTRUCTIONS,ORDER_TYPE_NAME,BILL_TO_CUSTOMER,SHIP_TO_CUSTOMER,SOLD_TO_CUSTOMER ';
	$field_vnso = '*';
	if ( $count_check == 1) {
		$where = " ORDER_NUMBER = '$SO_LINE' ORDER BY LENGTH(LINE_NUMBER),LINE_NUMBER ASC; ";
		$query = "SELECT * FROM $table_vnso  WHERE $where "; 
		$result_vnso = mysqli_query($conn138, $query);
		$num_vnso = mysqli_num_rows($result_vnso);
		
		if ( $num_vnso < 1 )  {//Nếu không có trong vnso thì truy cập đến vnso_total
			$query = "SELECT * FROM $table_vnso_total WHERE $where ";
			$result_vnso = mysqli_query($conn138, $query);
			$num_vnso = mysqli_num_rows($result_vnso);
		}

	}
	else { //else $count_check = 2 CREATEDDATE
		$where = " ORDER_NUMBER = '$val_SO'  AND LINE_NUMBER = '$val_LINE' ORDER BY ID DESC LIMIT 1; ";
			$query = "SELECT * FROM $table_vnso WHERE $where ";
			$result_vnso = mysqli_query($conn138, $query);
			$num_vnso = mysqli_num_rows($result_vnso);
			
			if ( $num_vnso < 1 ) { //Nếu không có trong vnso thì truy cập đến vnso_total
				$query = "SELECT * FROM $table_vnso_total WHERE $where ";
				$result_vnso = mysqli_query($conn138, $query);
				$num_vnso = mysqli_num_rows($result_vnso);
			}

	} //end else $count = 2

	/********************* */

	///////////////////////////////////////////////////////////////
	//Option: 1: không xảy ra do đã kiểm tra SOLINE input
	if ( $num_vnso < 1 ) {
		
		$response = [
			'status' => false,
			'mess' =>  "MASTER ITEM $SO_LINE KHÔNG TỒN TẠI, VUI LÒNG CẬP NHẬT (2)!"
		];
		echo json_encode( $response ); die;

	} else { //Option 2

		//result save array
		$dataResult = [];
		
		/* ***GET SUM QTY, COUNT_SO_LINE_ ************************************************ */
		$getTOTAL_QTY = getTOTAL_QTY($SO_LINE); //file automail_conn
		extract($getTOTAL_QTY);//get $QTY_TOTAL, $COUNT_SO_LINE
		/* *********************************************************** */
		//khai báo biến
		$PROMISE_DATE 	= '';
		$REQUEST_DATE 	= '';
		$ORDERED_DATE 	= '';
		$WIDTH = '';
		$HEIGHT = '';	
		$SAMPLE = '';
		$SOLINE_SAMPLE = '';
		$INDEX = 0;
		$SAMPLE15PCS = '';
		$line_tmp1 = '';
		$line_tmp2 = '';
		while ( $row = mysqli_fetch_array($result_vnso) ) {
			$INDEX++;
			
			$line_tmp1 = !empty($row['LINE_NUMBER']) ? trim($row['LINE_NUMBER']):'';
			if ($line_tmp1 == $line_tmp2) {
				continue;
			}
			
			//set record
			$ORDER_NUMBER 			= !empty($row['ORDER_NUMBER']) ? trim($row['ORDER_NUMBER']):'';
			$LINE_NUMBER 			= !empty($row['LINE_NUMBER']) ? trim($row['LINE_NUMBER']):'';
			$ORACLE_ITEM 			= !empty($row['ITEM']) ? trim($row['ITEM']):'';
			
			$QTY 					= !empty($row['QTY']) ? trim($row['QTY']):0;
			$QTY 					= (int)$QTY;

			$PROMISE_DATE 			= !empty($row['PROMISE_DATE']) ? formatDate($row['PROMISE_DATE']):'';

			$REQUEST_DATE 			= !empty($row['REQUEST_DATE']) ? formatDate($row['REQUEST_DATE']):'';
			$ORDERED_DATE 			= !empty($row['ORDERED_DATE']) ? $row['ORDERED_DATE']:'';
			if (!empty($ORDERED_DATE)) {
				$ORDERED_DATE        = date('d-M-y',strtotime($ORDERED_DATE));
			}
			
			$CS 					= !empty($row['CS']) ? trim($row['CS']):'';
			$BILL_TO_CUSTOMER 		= !empty($row['BILL_TO_CUSTOMER']) ? trim($row['BILL_TO_CUSTOMER']):'';		
			$SHIP_TO_CUSTOMER 		= !empty($row['SHIP_TO_CUSTOMER']) ? trim($row['SHIP_TO_CUSTOMER']):'';	
			$SHIP_TO_CUSTOMER 		= str_replace("CONG TY TNHH WORLDON (VIET NAM)","KH WORLDON",$SHIP_TO_CUSTOMER);
			$ORDER_TYPE_NAME 		= !empty($row['ORDER_TYPE_NAME']) ? trim($row['ORDER_TYPE_NAME']):'';

			$PACKING_INSTRUCTIONS 			= !empty($row['PACKING_INSTRUCTIONS']) ? trim($row['PACKING_INSTRUCTIONS']):'';
			// if( strpos(strtoupper($PACKING_INSTRUCTIONS),"DAY LA SO#")!==FALSE ) {
			// 	$PACKING_INSTRUCTIONS 		= str_replace("KHONG KIM LOAI _LAY MAU MOI SIZE 5 PCS _ DAY LA SO#","",$PACKING_INSTRUCTIONS);
			// 	$PACKING_INSTRUCTIONS 		= str_replace("/CHU Y DONG GOI RIENG VOI HANG SAN XUAT","",$PACKING_INSTRUCTIONS);						 
			// }

			/* ***GET @SAMPLE ************************************************ */
			$SAMPLE = 0; //mặc định là không mẫu
			$SAMPLE = getSample($SO_LINE, $ORACLE_ITEM);//file automail get vnso

			/* *********** SET ORDER DATE *********************************************************/
			$ORDER_CREATE_DATE 	= date('d-M-y');

			//20191018: Xu ly ngay lenh san xuat
			$query_po_save_date 	= "SELECT PO_SAVE_DATE FROM rfid_po_save ORDER BY PO_SAVE_DATE DESC LIMIT 0,1";
			$result_po_save_date 	= mysqli_query($conn252, $query_po_save_date);
			if (mysqli_num_rows($result_po_save_date)>0) {
				$result_po_save_date 	= mysqli_fetch_array($result_po_save_date);
				$PO_SAVE_DATE = $result_po_save_date['PO_SAVE_DATE'];
				
				if ( strtotime($PO_SAVE_DATE) > strtotime($ORDER_CREATE_DATE) ) {//chuyen ngay thanh so nguyen de so sanh
					$ORDER_CREATE_DATE = $PO_SAVE_DATE;
				}
				
			}

			// đóng trường hợp này: 20201218

			// Trường hợp Binh nhắn, xử lý theo mail: Fwd: Invitation: R115 material consumption in Vn (nike) @ Thu May 21, 2020 10am - 11am (HKT) (coco.qiu1@ap.averydennison.com)
			// 20200525: Thay đổi material code để sử dụng, tầm 6 tháng ngưng sử dụng thì quay về ban đầu
			
			// // $Material_Code_Special_Bill_To = array(
			// // 	'5-601865-310-00',
			// // 	'5-601865-310-00',
			// // 	'5-601865-310-00',
			// // 	'5-601865-310-00',
			// // 	'5-601863-310-00',
			// // 	'5-601866-310-00',
			// // 	'5-601865-310-00',
			// // 	'5-601865-310-00',
			// // 	'5-601864-310-00',
			// // 	'5-601864-310-00',
			// // 	'5-601864-310-00',
			// // 	'5-601864-310-00',
			// // 	'5-601864-310-00',
			// // 	'5-601864-310-00',
			// // 	'5-601863-310-00',
			// // 	'5-601865-310-00',
			// // 	'5-601864-310-00',
			// // 	'5-601864-310-00'
			// // );

			// $Material_Code_Special_Bill_To = array(
			// 	'5-601863-310-00',
			// 	'5-601863-310-00'
			// );
		
			// // $internal_item_Special_Bill_To = array(
			// // 	'4-221835-310-01',
			// // 	'4-221837-310-01',
			// // 	'4-221826-310-01',
			// // 	'4-221836-310-01',
			// // 	'4-221825-310-01',
			// // 	'4-221840-310-01',
			// // 	'4-221838-310-01',
			// // 	'4-221838-310-01',
			// // 	'4-221851-310-01',
			// // 	'4-221850-310-01',
			// // 	'4-221849-310-01',
			// // 	'4-221841-310-01',
			// // 	'4-221842-310-01',
			// // 	'4-221843-310-01',
			// // 	'4-221830-310-01',
			// // 	'4-221839-310-01',
			// // 	'4-221844-310-01',
			// // 	'4-221845-310-01'
			// // );

			// // $internal_item_Special_Bill_To = array(
			// // 	'4-221825-310-01',
			// // 	'4-221830-310-01'
			// // );

			// // $internal_item_data_check[] = array( 'internal_item' => '4-221825-310-01', 'material_code' => '5-601863-310-00' );
			// // $internal_item_data_check[] = array( 'internal_item' => '4-221830-310-01', 'material_code' => '5-601863-310-00' );

			// // // // $internal_item_data_check = array();
			// // // // foreach ($internal_item_Special_Bill_To as $internal_item_Special_Bill_To_Check ) {
			// // // // 	$internal_item_data_check['internal_item'][] = $internal_item_Special_Bill_To_Check;
			// // // // }

			// // // // foreach ($Material_Code_Special_Bill_To as $Material_Code_Special_Bill_To_Check ) {
			// // // // 	$internal_item_data_check['material_code'][] = $Material_Code_Special_Bill_To_Check;
			// // // // }
		
			// // // print_r($internal_item_data_check); exit();
			// // $Special_Bill_To_array_2020 = array(
			// // 	'Cong ty TNHH May Mac United Sweethearts Viet Nam',
			// // 	'SKY LEADER LIMITED',
			// // 	'SKY TOP LIMITED',
			// // 	'SNOGEN GREEN CO.,LTD'
			// // );
			// // $remark_material_Special_Bill_To = '';
			// // $material_bill_to_special = '';
			// // foreach ($Special_Bill_To_array_2020 as $Special_Bill_To_value ) {
			// // 	if (strtoupper($BILL_TO_CUSTOMER) == strtoupper($Special_Bill_To_value) ) {
			// // 		foreach ($internal_item_data_check as $keyCheck => $internal_item_data_check_value ) {
			// // 			if ($ORACLE_ITEM == $internal_item_data_check_value['internal_item'] ) {
			// // 				//$remark_material_Special_Bill_To = '<span style="color:blue;font-weight:bold;">Vật tư có thể sử dụng thay thế: ' . $internal_item_data_check['material_code'][$keyCheck] . '</span>';
			// // 				$material_bill_to_special = $internal_item_data_check_value['material_code'];
			// // 			}
			// // 		}
			// // 	}
			// // }

			/* *********** LOAD DATABASE *********************************************************/
			/* Trường hợp pvh_rfid: internal_item sẽ nằm trong 2 database ms color và trim. Ưu tiên db ms color*/
			if ($FORM_TYPE=='pvh_rfid' ) {
				$query = "SELECT * FROM ms_color WHERE  INTERNAL_ITEM = '$ORACLE_ITEM' ORDER BY CREATED_TIME DESC ";
				$result = mysqli_query($conn252, $query);
				// if($result === FALSE) { die(mysql_error()); }
				if (mysqli_num_rows($result)>0) {
					
					//load script
					include ("loadScriptMSColorData.php");
					
				} else {
					
					//load script
					include ("loadScriptTrimData.php");

				}

			}
			else if ( $FORM_TYPE=='ua_cbs'|| $FORM_TYPE=='cbs') {

				//load script
				include ("loadScriptMSColorData.php");
				
				if ($numMS<1) {
					$response = [
						'status' => false,
						'mess' 	 =>  "MASTER ITEM $ORACLE_ITEM KHÔNG TỒN TẠI, VUI LÒNG CẬP NHẬT! (DB MS COLOR)"
					];
					echo json_encode($response);
				}

			} //TH3: Form ua no cbs và rfid
			else if( $FORM_TYPE=='ua_no_cbs'||$FORM_TYPE=='rfid' ) {	
				
				//load script
				include ("loadScriptNOCBSData.php");

			}//end UA_NO_CBS, RFID
			else if( $FORM_TYPE=='trim'||$FORM_TYPE=='trim_macy' ) {

				//check trường hợp item oracle là: VN FREIGHT CHARGE thì bỏ qua
				if(strpos($ORACLE_ITEM,'VN')!==FALSE||strpos($ORACLE_ITEM,'FREIGHT')!==FALSE||strpos($ORACLE_ITEM,'CHARGE"')!==FALSE) {
					continue;
				}
				
				//load script numTRIMMACY
				include ("loadScriptTrimData.php");

				if ($numTRIMMACY<1) {
					$response = [
						'status' => false,
						'mess' 	 =>  "MASTER ITEM $ORACLE_ITEM KHÔNG TỒN TẠI, VUI LÒNG CẬP NHẬT! (DB MS COLOR)"
					];
					echo json_encode($response);
				}

				/* 
					==== NHÂN ĐÔI MỰC IN - FORM TRIM, TRIM MACY ====== 
					1. email: [RFID-SB] NHÃN IN HAI MẶT MỰC
					- Sử dụng material code làm điều kiện
					- Remark thêm vào
					2. email: [RFID-SB] NHÃN IN HAI MẶT MỰC 2022
					- Sử dụng internal item là điều kiện
					- Remark thêm vào
					
				*/ 
				
					if ($FORM_TYPE == 'trim' || $FORM_TYPE == 'trim_macy' ) {

						// mail: [RFID-SB] NHÃN IN HAI MẶT MỰC
							$material_arr = array(
								'4-218393-236-00', 
								'4-219667-236-00',
								'4-219667-236-01',
								'5-603057-236-00',
								'5-602682-385-00' // mới thêm vào 20211019
							);

							foreach ($material_arr as $material_check ) {
								if ($MATERIAL_CODE == $material_check ) {
									$INK_QTY = ( ($QTY * $WIDTH * 1.014)/1000 ) * 2;
									$INK_QTY = ceil($INK_QTY);
									break;
								}
							}

						// 20220216 - mail: [RFID-SB] NHÃN IN HAI MẶT MỰC 2022
							$internal_item_arr = array(
								'4-232729-000-00', 
								'4-232631-000-00'
							);

							foreach ($internal_item_arr as $item_check ) {
								if ($ORACLE_ITEM == $item_check ) {
									$INK_QTY = ( ($QTY * $WIDTH * 1.014)/1000 ) * 2;
									$INK_QTY = ceil($INK_QTY);
									break;
								}
							}

					}
				

			}//end TRIM, TRIM_MACY

			// đóng trường hợp này 20201218

			// // // 20200525: Check lại material, Nếu có material đặc biệt (dựa vào bill to và internal item (trên)), thì lấy material này
			// // // Sử dụng trường hợp này tầm 6 tháng thì ngừng thay đổi
			// // if (!empty($material_bill_to_special)) {
			// // 	$MATERIAL_REMARK .= " $material_bill_to_special thay thế cho code vật tư: $MATERIAL_CODE ";
			// // 	$MATERIAL_CODE = $material_bill_to_special;
				
			// // }

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


			//GET RESULT
			$dataResult[] = [
				'id' 	=> $INDEX,
				'data' 	=> [$INDEX,$ORDER_NUMBER,$LINE_NUMBER,$QTY,$ORACLE_ITEM,$ORDER_ITEM,$PROMISE_DATE,$REQUEST_DATE,$ORDERED_DATE,$CS,$RBO,$WIDTH,$HEIGHT,$INK_CODE,$INK_QTY,$INK_DES,$MATERIAL_CODE,$MATERIAL_QTY, $MATERIAL_DES,$GAP,$FORM_TYPE,$SAMPLE,$PACKING_INSTRUCTIONS,$BILL_TO_CUSTOMER,$COUNT_SO_LINE,$SHIP_TO_CUSTOMER,$ORDER_TYPE_NAME, $MATERIAL_REMARK, $INK_REMARK,$ORDER_CREATE_DATE,$SAMPLE15PCS,$SOLINE_SAMPLE]
			];
				
			
			$line_tmp2 = $line_tmp1;
			
		}//else while1
		

		//***RESULT****************** */
		$response = [
			'status' => true,
			'data' => $dataResult
		];
		
		echo json_encode($response);die;

	}//end else