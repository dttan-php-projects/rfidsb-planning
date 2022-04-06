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
			$columns = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA', 'AB');

		// Add new sheet
			$spreadsheet->createSheet();

		// Add some data
			$spreadsheet->setActiveSheetIndex(0);

		// active and set title
			$spreadsheet->getActiveSheet()->setTitle('DB_1_Line');

			$headerArr = array(
				'Internal Item', 'RBO', 'ORDER ITEM', 'Material code (paper)', 'Ribbon code', 'Description Mterial', 'Description Ink', 'Chieu Doc (dai) Length', 'Chieu Ngang (rong) Width', 'Blank Gap (mm)', 
				'Ghi chu item', 'Notes RBO', 'remark GIAY', 'Lay sample 15pcs', 'remark MUC', 'first orders', 'pcs/shit', 'Kind of Label', 'Standard LT', 'Note', 
				'Co gia/Khong gia', 'Color', 'Other remark 1', 'Other remark 2', 'Other remark 3', 'Other remark 4', 'Updated by', 'Created date'
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
			$spreadsheet->getActiveSheet()->getStyle('A1:AB1')->getFont()->setBold(true)->setName('Arial')->setSize(10);
			$spreadsheet->getActiveSheet()->getStyle('A1:AB1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('3399ff');
			$spreadsheet->getActiveSheet()->getStyle('A:AB')->getFont()->setName('Arial')->setSize(10);
		
	// set data
		$result = mysqli_query(getConnection(), "SELECT * FROM `no_cbs` ORDER BY `CREATED_DATE_TIME` DESC ;" );

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
						$spreadsheet->getActiveSheet()->SetCellValue('D' . $index, trim($value['material_code']) );
						$spreadsheet->getActiveSheet()->SetCellValue('E' . $index, trim($value['ribbon_code']) );
						$spreadsheet->getActiveSheet()->SetCellValue('F' . $index, trim($value['material_des']) );

						$spreadsheet->getActiveSheet()->SetCellValue('G' . $index, trim($value['ink_des']) );
						$spreadsheet->getActiveSheet()->SetCellValue('H' . $index, trim($value['width']) );
						$spreadsheet->getActiveSheet()->SetCellValue('I' . $index, trim($value['height']) );
						$spreadsheet->getActiveSheet()->SetCellValue('J' . $index, trim($value['blank_gap']) );
						$spreadsheet->getActiveSheet()->SetCellValue('K' . $index, trim($value['ghi_chu_item']) );

						$spreadsheet->getActiveSheet()->SetCellValue('L' . $index, trim($value['note_rbo']) );
						$spreadsheet->getActiveSheet()->SetCellValue('M' . $index, trim($value['remark_GIAY']) );
						$spreadsheet->getActiveSheet()->SetCellValue('N' . $index, trim($value['lay_sample_15_pcs']) );
						$spreadsheet->getActiveSheet()->SetCellValue('O' . $index, trim($value['remark_MUC']) );
						$spreadsheet->getActiveSheet()->SetCellValue('P' . $index, trim($value['first_order']) );

						$spreadsheet->getActiveSheet()->SetCellValue('Q' . $index, trim($value['pcs_sht']) );
						$spreadsheet->getActiveSheet()->SetCellValue('R' . $index, trim($value['kind_of_label']) );
						$spreadsheet->getActiveSheet()->SetCellValue('S' . $index, trim($value['STANDARD_LT']) );
						$spreadsheet->getActiveSheet()->SetCellValue('T' . $index, trim($value['note']) );
						$spreadsheet->getActiveSheet()->SetCellValue('U' . $index, trim($value['note_price']) );

						$spreadsheet->getActiveSheet()->SetCellValue('V' . $index, trim($value['note_color']) );
						$spreadsheet->getActiveSheet()->SetCellValue('W' . $index, trim($value['OTHER_REMARK_1']) );
						$spreadsheet->getActiveSheet()->SetCellValue('X' . $index, trim($value['OTHER_REMARK_2']) );
						$spreadsheet->getActiveSheet()->SetCellValue('Y' . $index, trim($value['OTHER_REMARK_3']) );
						$spreadsheet->getActiveSheet()->SetCellValue('Z' . $index, trim($value['OTHER_REMARK_4']) );

						$spreadsheet->getActiveSheet()->SetCellValue('AA' . $index, trim($value['UPDATED_BY']) );
						$spreadsheet->getActiveSheet()->SetCellValue('AB' . $index, trim($value['CREATED_DATE_TIME']) );

				}

			// clear cache (IMPORTANT)
				ob_clean();


		}




	// set filename for excel file to be exported
		$filename = 'RFIDSB_DB_1_Line_' . date("Y_m_d__H_i_s") . '.xlsx';

	// header: generate excel file
		header('Content-type: application/vnd.ms-excel');
		header('Content-disposition: attachment;filename="'.$filename.'"');
		header('Cache-Control: max-age=0');

	// writer
		$writer = new Xlsx($spreadsheet);
		$writer->save('php://output');
?>
