<?php	
	date_default_timezone_set('Asia/Ho_Chi_Minh'); ini_set('max_execution_time',300);  // set time 5 minutes
    if(!isset($_COOKIE["VNRISIntranet"])) header('Location: login.php');    
        // $USER_CUR = $_COOKIE["VNRISIntranet"];
    require_once ( "../define_constant_system.php");
    require_once (PATH_MODEL . "/__connection.php");
    require_once ($_SERVER["DOCUMENT_ROOT"]."/Module/PHPExcel.php");
    //connect host
    $conn = getConnection();
    
    function formatDate($value){
        return date('d-M-y',strtotime($value));
    }

    function formatTime($value){
        return date('H:m:s',strtotime($value));
    }

	// CALL QUERY
    $query      = "SELECT * FROM `ua` ORDER BY ID ASC ";
    $result     = mysqli_query($conn, $query);
	if($result  === FALSE) { die(mysql_error()); }
    $num        = mysqli_num_rows($result);
	if( $num > 0 ){ 
		$rowsResult    = mysqli_fetch_all($result, MYSQLI_ASSOC);//sử dụng hàm này cho truy vấn data (không dùng hàm khác)
		
		foreach ($rowsResult as $key => $formatData){
            $ID 			    = !empty($formatData['ID'])?($formatData['ID']):'';
			$item 		= !empty($formatData['item'])?($formatData['item']):'';
			$size 			= !empty($formatData['size'])?($formatData['size']):'';
			$base_roll 		= !empty($formatData['base_roll'])?($formatData['base_roll']):'';
			$UPDATED_BY 		= !empty($formatData['UPDATED_BY'])?($formatData['UPDATED_BY']):'';
			$CREATED_DATE_TIME 		= !empty($formatData['CREATED_DATE_TIME'])?($formatData['CREATED_DATE_TIME']):'';

			$data[]=[
				$ID, $item, $size, $base_roll, $UPDATED_BY, $CREATED_DATE_TIME
			];
		}//end foreach

	}
	/*
	echo "<pre>";
	print_r($data);die;
	*/
	//Khởi tạo đối tượng
	$excel = new PHPExcel();
	//Chọn trang cần ghi (là số từ 0->n)
	$excel->setActiveSheetIndex(0);
	//Tạo tiêu đề cho trang. (có thể không cần)
	//$excel->getActiveSheet()->setTitle('demo ghi dữ liệu');

	//Xét chiều rộng cho từng, nếu muốn set height thì dùng setRowHeight()
	//$excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
	//$excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
	//$excel->getActiveSheet()->getColumnDimension('C')->setWidth(30);

	//Xét in đậm cho khoảng cột
	//$excel->getActiveSheet()->getStyle('A1:C1')->getFont()->setBold(true);
	//Tạo tiêu đề cho từng cột
	//Vị trí có dạng như sau:
	/**
	 * |A1|B1|C1|..|n1|
	 * |A2|B2|C2|..|n1|
	 * |..|..|..|..|..|
	 * |An|Bn|Cn|..|nn|
	 */
	$excel->getActiveSheet()->setCellValue('A1', 'TT');
	$excel->getActiveSheet()->setCellValue('B1', 'ITEM');
	$excel->getActiveSheet()->setCellValue('C1', 'SIZE');
	$excel->getActiveSheet()->setCellValue('D1', 'BASE ROLL');
	$excel->getActiveSheet()->setCellValue('E1', 'UPDATED DATE');
	$excel->getActiveSheet()->setCellValue('F1', 'CREATED DATE');
	
	// thực hiện thêm dữ liệu vào từng ô bằng vòng lặp
	// dòng bắt đầu = 2
	$numRow = 2;
	foreach ($data as $row) {

		$excel->getActiveSheet()->setCellValue('A' . $numRow, $row[0]);
		$excel->getActiveSheet()->setCellValue('B' . $numRow, $row[1]);
		$excel->getActiveSheet()->setCellValue('C' . $numRow, $row[2]);
		$excel->getActiveSheet()->setCellValue('D' . $numRow, $row[3]);
		$excel->getActiveSheet()->setCellValue('E' . $numRow, $row[4]);
		$excel->getActiveSheet()->setCellValue('F' . $numRow, $row[5]);

		$numRow++;
		
	}
	// Khởi tạo đối tượng PHPExcel_IOFactory để thực hiện ghi file
	// ở đây mình lưu file dưới dạng excel2007
	$filename = "RFIDSB_Report_UA_".date("d_m_Y__H_i_s");
	header('Content-type: application/vnd.ms-excel;charset=utf-8');	
	header('Content-Encoding: UTF-8');
	header("Cache-Control: no-store, no-cache");
	header("Content-Disposition: attachment; filename=$filename.xlsx");
	PHPExcel_IOFactory::createWriter($excel, 'Excel2007')->save('php://output');
?>
