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
		$table      = "rfid_po_save";
	
	//set content type and xml tag
		header("Content-type:text/xml");
		echo "<?xml version=\"1.0\"?>";
	// get data
		$FROM_DATE = $_GET['from_date_value'];
		$FROM_DATE = date('Y-m-d',strtotime($FROM_DATE));
		$TO_DATE = $_GET['to_date_value'];
		$TO_DATE = date('Y-m-d',strtotime($TO_DATE));

		$current = date('Y-m-d');
		$checkDate = strtotime ( '-1 day' , strtotime ( date('Y-m-d') ) ) ;
		$checkDate = date ( 'Y-m-d' , $checkDate );

	// query model
	$fields = " * ";
	if($FROM_DATE!='1970-01-01' && $TO_DATE!='1970-01-01'){
		$query_form = " SELECT $fields  FROM $table WHERE ( PO_SAVE_DATE>='$FROM_DATE' AND PO_SAVE_DATE<='$TO_DATE') ORDER BY PO_CREATED_TIME DESC ";
	}else{
		$query_form = " SELECT $fields FROM $table WHERE PO_SAVE_DATE >= '$checkDate' ORDER BY PO_CREATED_TIME DESC LIMIT 0,300 "; 
	}

	$rowsResult = toQueryAll($conn252, $query_form);
	if (!empty($rowsResult) ) {
		$header = '<head>
						<column width="110" type="ed" align="center" sort="str">Ngày Làm Đơn</column>
						<column width="140" type="ed" align="center" sort="str">Loại Form</column>
						<column width="120" type="ed" align="center" sort="str">Số LSX</column>
						<column width="80" type="ed" align="center" sort="str">SOLine</column>
						<column width="80" type="edn" align="center" sort="str">Số Lượng</column>
						<column width="150" type="ed" align="center" sort="str">Internal Item</column>
						<column width="210" type="ed" align="center" sort="str">Ordered Item</column>
						<column width="210" type="txt" align="center" sort="str">RBO</column>
						<column width="*" type="txt" align="center" sort="str">Ship To Customer</column>
						<column width="110" type="ed" align="center" sort="str">Promise Date</column>
						<column width="110" type="ed" align="center" sort="str">Người Tạo Đơn</column>
						
						<column width="80" type="link" align="center" sort="str">Print</column>
						<column width="62" type="link" align="center" sort="str">Xóa</column>
						
					</head>';
		echo("<rows>");

			echo $header;

			if(!empty($rowsResult)){  
				$ID = 0;
				$cellStart = "<cell><![CDATA[";
				$cellEnd = "]]></cell>";
				foreach ($rowsResult as $row){
					$ID++;
					$PO_SAVE_DATE = $row['PO_SAVE_DATE']; 
					$PO_SAVE_DATE = formatDate($PO_SAVE_DATE);
					$PO_NO = $row['PO_NO'];

					$FORM_TYPE = trim($row['PO_FORM_TYPE']);

					$PO_SHIP_TO_CUSTOMER = trim($row['PO_SHIP_TO_CUSTOMER']);
					$PO_RBO = trim(html_entity_decode($row['PO_RBO'], ENT_QUOTES)); 
					$PO_QTY = $row['PO_QTY'];
					$PO_PROMISE_DATE = formatDate($row['PO_PROMISE_DATE']);
					// $PO_PROMISE_DATE = date('Y-m-d', strtotime($row['PO_PROMISE_DATE']) );

					//get PO_NO từ getPO_NO_FI_conn.php (xử lý hiển thị PO_NO)
					$FR_SHOW = ''; //mac dinh
					$PO_NO_FI = $PO_NO;
					include_once ("getPO_NO_FI_conn.php");
					$PO_NO_FI = getPO_NO_FI($PO_NO);

					//echo 'FORM '.$FORM_TYPE;
					if($FORM_TYPE=='ua_cbs'){
						$FORM_TYPE_TEXT = 'UNDER ARMOUR CBS';
					}elseif($FORM_TYPE == 'ua_no_cbs'){
						$FORM_TYPE_TEXT = 'UNDER ARMOUR NO CBS';
					}elseif($FORM_TYPE == 'cbs'){
						$FORM_TYPE_TEXT = 'COLOR BY SIZE';
					}elseif($FORM_TYPE == 'rfid'){
						$FORM_TYPE_TEXT = 'RFID';
					}elseif($FORM_TYPE == 'pvh_rfid'){
						$FORM_TYPE_TEXT = 'PVH RFID';
					}elseif($FORM_TYPE == 'trim'){
						$FORM_TYPE_TEXT = 'TRIM';
					}elseif($FORM_TYPE == 'trim_macy'){
						$FORM_TYPE_TEXT = 'TRIM MACY';
					}
					$PO_SO_LINE = $row['PO_SO_LINE'];
					$PO_INTERNAL_ITEM = $row['PO_INTERNAL_ITEM'];
					$PO_ORDER_ITEM = $row['PO_ORDER_ITEM'];
					// if($FORM_TYPE=='pvh_rfid'||$FORM_TYPE=='trim'||$FORM_TYPE=='trim_macy'){
					// 	$PO = 'PO gộp';
					// 	$ITEM = 'ITEM gộp';
					// }			
					$PO_PRINTED = $row['PO_PRINTED'];
					$PO_CREATED_BY = $row['PO_CREATED_BY'];
					
					//kiểm tra user tạo đơn có đang đăng nhập hay không
					$is_access = 0;
					if ($PO_CREATED_BY == $USER_CUR ) $is_access = 1;
					
					if($is_access == 1 || ($USER_CUR == 'tan.doan') ){
						// $delete_url  = 'DELETE^javascript:deleteNO("'.$PO_NO.'");^_self';
						$delete_url = "./models/deleteNO_conn.php?data=$PO_NO";
					} else {
						$delete_url = '';
					}		
					$linkPrint = "./views/print/printNO.php?PRINT_PO_NO=$PO_NO";		
					// $linkPrint2 = "./views/print/printNO.php?PRINT_PO_NO=$PO_NO";		
					echo("<row id='".$ID."'>");
						echo( $cellStart);  // LENGTH
							echo($PO_SAVE_DATE);  //value for product name                 
						echo( $cellEnd);
						echo( $cellStart);  // LENGTH
							echo($FORM_TYPE_TEXT);  //value for product name                 
						echo( $cellEnd);
						echo( $cellStart);  // LENGTH
							echo($PO_NO_FI);  //value for product name                 
						echo( $cellEnd);
						echo( $cellStart);  
							echo($PO_SO_LINE);  //value for product name                 
						echo( $cellEnd);
						echo( $cellStart);  
							echo($PO_QTY);  //value for product name                 
						echo( $cellEnd);
						
						echo( $cellStart);  // LENGTH
							echo($PO_INTERNAL_ITEM);  //value for product name                 
						echo( $cellEnd);
						echo( $cellStart);  
							echo($PO_ORDER_ITEM);  //value for product name                 
						echo( $cellEnd);
						echo( $cellStart);  
							echo($PO_RBO);  //value for product name                 
						echo( $cellEnd);
						echo( $cellStart);  
							echo($PO_SHIP_TO_CUSTOMER);  //value for product name                 
						echo( $cellEnd);
						echo( $cellStart);  
							echo($PO_PROMISE_DATE);  //value for product name                 
						echo( $cellEnd);
						
						echo( $cellStart);  // LENGTH
							echo($PO_CREATED_BY);  //value for product name                 
						echo( $cellEnd);
						
						// if($PO_PRINTED=='1'){
						// 	echo( $cellStart);
						// 		echo("YES");  //value for product name                 
						// 	echo( $cellEnd);
						// }else{
						// 	echo( $cellStart);
						// 		echo("NO");  //value for product name                 
						// 	echo( $cellEnd);
						// }
						if($PO_PRINTED=='1'){
							echo("<cell><![CDATA[<font color='red'></front>");  // LENGTH
								echo("Printed^$linkPrint");  //value for product name                          
							echo("]]></cell>");
						}else{
							echo("<cell><![CDATA[<font color='blue'></front>");  // LENGTH
								echo("Print NO^$linkPrint");  //value for product name       
							echo("]]></cell>");
						}
						
						echo( $cellStart);  // LENGTH
							echo("Delete^$delete_url");  //value for product name       
						echo( $cellEnd);			
					echo("</row>");
				}
			}
		echo("</rows>");
	}else{
		echo("<rows></rows>");
	}
?>