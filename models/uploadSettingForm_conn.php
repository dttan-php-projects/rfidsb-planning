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
					'mess' => 'File dữ liệu import quá lớn, Vui lòng kiểm tra lại ' . $fileType
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
			// move_uploaded_file($_FILES["file"]["tmp_name"],"uploaded/".$filename);
			$data = getFileExcel($_FILES['file']["tmp_name"]);	
			if(!empty($data)){
				
				require_once ( "../define_constant_system.php");
				require_once (PATH_MODEL . "/__connection.php");  
				//connect host
				$table      = "setting_item_form";
				$conn = getConnection(); 
				
				foreach($data as $key => $value){ 
					
						$INTERNAL_ITEM 	= !empty($value[1])?addslashes($value[1]):'';  					
						$FORM_TYPE 	    = !empty($value[2])?addslashes($value[2]):'';
						
						$CREATED_BY     = $_COOKIE["VNRISIntranet"];

						$sql_check = "SELECT INTERNAL_ITEM FROM $table WHERE INTERNAL_ITEM='$INTERNAL_ITEM' ";
						$rowsCheck = mysqli_query($conn, $sql_check);
						$num_check = mysqli_num_rows($rowsCheck);
						if( $num_check > 0 ){
							// update
							$sql = "UPDATE $table SET `FORM_TYPE`='$FORM_TYPE', `CREATED_BY`='$CREATED_BY', `CREATED_TIME`=now()  WHERE INTERNAL_ITEM = '$INTERNAL_ITEM' ";
						}else{
							// insert
							$sql = "INSERT INTO $table (`INTERNAL_ITEM`,`FORM_TYPE`,`CREATED_BY`) VALUES ('$INTERNAL_ITEM','$FORM_TYPE','$CREATED_BY')";
						}
						$check = mysqli_query($conn, $sql);
						if(!$check){
							$response = [
									'state' 	=>	false,	
									'name'   	=>	$filename,			
									'extra'		=>	[
										'mess' => 'Có lỗi xảy ra trong quá trình import '
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
							'mess' 	=> 'Import dữ liệu thành công, Website sẽ reload!!!!',
						]
					];
				}
			}else{
				$response = [
					'state' 	=>	false,	
					'name'   	=>	$filename,			
					'extra'		=>	[
						'mess' => 'Kiểm tra lại dữ liệu file EXCEL',
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
?>