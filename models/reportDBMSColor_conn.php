<?php	
	// time zone
		date_default_timezone_set('Asia/Ho_Chi_Minh'); 

	// set time 5 minutes
		ini_set('max_execution_time',300);  
	
	// check login
    	if(!isset($_COOKIE["VNRISIntranet"])) header('Location: login.php');    

	// require
    	require_once ( "../define_constant_system.php");
    	require_once (PATH_MODEL . "/__connection.php");
	
	// require spreedsheet lib
		require_once ("../vendor/autoload.php");
		use PhpOffice\PhpSpreadsheet\Spreadsheet;
		use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

	// spreedsheet
		// init
			$spreadsheet = new Spreadsheet();

		// set the names of header cells
			$columns = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T');

		// Add new sheet
			$spreadsheet->createSheet();

		// Add some data
			$spreadsheet->setActiveSheetIndex(0);

		// active and set title
			$spreadsheet->getActiveSheet()->setTitle('DB_MS_Color');

			$headerArr = array(
				'Internal Item', 'RBO', 'ORDER ITEM', 'Color code', 'item code', 'Material code (paper)', 'Description Mterial', 'Ribbon code', 'Description Ink', 'Chieu Doc (dai)', 
				'Chieu Ngang (rong)', 'Ghi chu item', 'Blank Gap (mm)', 'Remark', 'Other remark 1', 'Other remark 2', 'Other remark 3', 'Other remark 4', 'Updated by', 'Created date'
			);

			$id = 0;
			foreach ($headerArr as $header ) {
				for ($index = $id; $index < count($headerArr); $index++ ) {
					// width
					$spreadsheet->getActiveSheet()->getColumnDimension($columns[$index])->setWidth(20);

					// headers
					$spreadsheet->getActiveSheet()->setCellValue($columns[$index] . '1', $header );

					$id++;
					break;
				}
			}


		// Font
			$spreadsheet->getActiveSheet()->getStyle('A1:T1')->getFont()->setBold(true)->setName('Arial')->setSize(10);
			$spreadsheet->getActiveSheet()->getStyle('A1:T1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('3399ff');
			$spreadsheet->getActiveSheet()->getStyle('A:T')->getFont()->setName('Arial')->setSize(10);
		
	// set data
		$result = mysqli_query(getConnection(), "SELECT * FROM `ms_color` ORDER BY `created_time` DESC ;" );

		if( mysqli_num_rows($result) > 0 ){ 

			// get data from db	
				$rowsResult    = mysqli_fetch_all($result, MYSQLI_ASSOC);
			// set data
				$index = 1;
				foreach ($rowsResult as $key => $value){

					// index
						$index++;

					// add to file 
						$spreadsheet->getActiveSheet()->SetCellValue('A' . $index, trim($value['internal_item']) );
						$spreadsheet->getActiveSheet()->SetCellValue('B' . $index, trim($value['rbo']) );
						$spreadsheet->getActiveSheet()->SetCellValue('C' . $index, trim($value['order_item']));
						$spreadsheet->getActiveSheet()->SetCellValue('D' . $index, trim($value['color_code']) );
						$spreadsheet->getActiveSheet()->SetCellValue('E' . $index, trim($value['item_color']) );

						$spreadsheet->getActiveSheet()->SetCellValue('F' . $index, trim($value['material_code']) );
						$spreadsheet->getActiveSheet()->SetCellValue('G' . $index, trim($value['material_des']) );
						$spreadsheet->getActiveSheet()->SetCellValue('H' . $index, trim($value['ribbon_code']) );
						$spreadsheet->getActiveSheet()->SetCellValue('I' . $index, trim($value['ink_des']) );
						$spreadsheet->getActiveSheet()->SetCellValue('J' . $index, trim($value['width']) );

						$spreadsheet->getActiveSheet()->SetCellValue('K' . $index, trim($value['height']) );
						$spreadsheet->getActiveSheet()->SetCellValue('L' . $index, trim($value['note']) );
						$spreadsheet->getActiveSheet()->SetCellValue('M' . $index, trim($value['blank_gap']) );
						$spreadsheet->getActiveSheet()->SetCellValue('N' . $index, trim($value['remark']) );
						$spreadsheet->getActiveSheet()->SetCellValue('O' . $index, trim($value['other_remark_1']) );

						$spreadsheet->getActiveSheet()->SetCellValue('P' . $index, trim($value['other_remark_2']) );
						$spreadsheet->getActiveSheet()->SetCellValue('Q' . $index, trim($value['other_remark_3']) );
						$spreadsheet->getActiveSheet()->SetCellValue('R' . $index, trim($value['other_remark_4']) );
						$spreadsheet->getActiveSheet()->SetCellValue('S' . $index, trim($value['updated_by']) );
						$spreadsheet->getActiveSheet()->SetCellValue('T' . $index, trim($value['created_time']) );

				}

			// clear cache (IMPORTANT)
				ob_clean();


		}


	// set filename for excel file to be exported
		$filename = 'RFIDSB_MS_Color_' . date("Y_m_d__H_i_s") . '.xlsx';

	// header: generate excel file
		header('Content-type: application/vnd.ms-excel');
		header('Content-disposition: attachment;filename="'.$filename.'"');
		header('Cache-Control: max-age=0');

	// writer
		$writer = new Xlsx($spreadsheet);
		$writer->save('php://output');
?>
