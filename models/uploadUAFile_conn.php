<?php
	
	//get exel File function
		include_once ("__getFileExcel.php");

	if (@$_REQUEST["mode"] == "html5" || @$_REQUEST["mode"] == "flash") {
		header("Content-Type: text/json");
		$filename = date("d_m_Y__H_i_s");
		$excelType = ['d/vnd.ms-excel','application/vnd.openxmlformats-officedocument.spreadsheetml.sheet','application/vnd.ms-excel','application/vnd.ms-excel.sheet.macroEnabled.12'];
		$fileSize = $_FILES['file']['size'];
		$fileType = $_FILES['file']['type'];
		if($fileSize>1000000){
			$response = [
				'state' 	=>	false,
				'name'   	=>	$filename,
				'extra' 	=>	[
					'mess' => 'File dữ liệu import quá lớn, Vui lòng kiểm tra lại'
				]
			];
		}elseif(!in_array($fileType,$excelType)){
			$response = [
				'state' 	=>	false,
				'name'   	=>	$filename,
				'extra'		=>	[
					'mess' => 'File dữ liệu phải là EXCEL, Vui lòng kiểm tra lại',
				]
			];
		}else{
			$data = getFileExcel($_FILES['file']["tmp_name"]);	
			if(!empty($data)){
				
				require_once ( "../define_constant_system.php");
				require_once (PATH_MODEL . "/__connection.php");  
				//connect host
				$conn = getConnection(); 
				$table = "ua";
				$count = 0;
				foreach($data as $key => $value){ 
					
						$item 		= !empty($value[1])?addslashes($value[1]):''; 
						$size_check = $value[2];
						if ($size_check == 0 || $size_check == '0') {
							$size = '0';
						} else {
							$size 	    = !empty($value[2])?addslashes($value[2]):'';
						}
						$base_roll 	= !empty($value[3])?addslashes($value[3]):'';
						if ( empty($item) && empty($size) && empty($base_roll) ) {
							if ($count<1) {
								continue;
								$count++;
							} else {
								$response = [
									'state' 	=>	false,	
									'name'   	=>	$filename,			
									'extra'		=>	[
										'mess' => 'Kiểm tra lại dữ liệu file EXCEL'
									]
								];
								echo json_encode($response);die;
							}
								
						}
						
						$UPDATED_BY = $_COOKIE["VNRISIntranet"];

						$sql_check = "SELECT 
										`item` 
									FROM 
										$table
									WHERE 
										item='$item' AND size='$size' AND base_roll='$base_roll' ";
						$rowsCheck = mysqli_query($conn, $sql_check);
						if (mysqli_num_rows($rowsCheck)>0) { //UPDATED_BY
							$sql = "UPDATE 
										$table
									SET 
										`UPDATED_BY`='$UPDATED_BY', `CREATED_DATE_TIME`=now()
									WHERE 
										item='$item' AND size='$size' AND base_roll='$base_roll' ";
						} else {
							$sql = "INSERT INTO $table
											(`item`,`size`,`base_roll`,`UPDATED_BY`) 
										VALUES 
											('$item','$size', '$base_roll','$UPDATED_BY')";
						}
		
						$check = mysqli_query($conn, $sql);
						if(!$check){
							$response = [
									'state' 	=>	false,	
									'name'   	=>	$filename,			
									'extra'		=>	[
										'mess' => 'Có lỗi xảy ra trong quá trình import: '.$dbMi2->error,
								]
							];
							echo json_encode($response);die;
						}
						
					
				}		
				if($check){
					$response = [
						'state' 	=>	true,	
						'name'   	=>	$filename,			
						'extra'		=>	[
							'mess' 	=> 'Import dữ liệu thành công, Website sẽ reload!!!!'
						]
					];
				}
			}else{
				$response = [
					'state' 	=>	false,	
					'name'   	=>	$filename,			
					'extra'		=>	[
						'mess' => 'Kiểm tra lại dữ liệu file EXCEL'
					]
				];
			}				
		}
		echo json_encode($response);
	}

	/*

	HTML4 MODE

	response format:

	to cancel uploading
	{state: 'cancelled'}

	if upload was good, you need to specify state=true, name - will passed in form.send() as serverName param, size - filesize to update in list
	{state: 'true', name: 'filename', size: 1234}

	*/

	if (@$_REQUEST["mode"] == "html4") {
		header("Content-Type: text/html");
		if (@$_REQUEST["action"] == "cancel") {
			print_r("{state:'cancelled'}");
		} else {
			$filename = $_FILES["file"]["name"];
			move_uploaded_file($_FILES["file"]["tmp_name"], "uploaded/".$filename);
			print_r("{state: true, name:'".str_replace("'","\\'",$filename)."', size:".$_FILES["file"]["size"]/*filesize("uploaded/".$filename)*/.", extra: {info: 'just a way to send some extra data', param: 'some value here'}}");
		}
	}