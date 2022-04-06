<?php
	header("Content-Type: application/json");
	require_once ( "../define_constant_system.php");
	require_once (PATH_MODEL . "/__connection.php"); 
	
	$table = "ua";
	$conn252 = getConnection(); //host

	$data = $_POST['data'];
	// $data = '{"item":"BCVCRFIDSTKR-1","size":"0","base_roll":"4-219241-320-CG4-1211","idUA":"113"}';

	if(!empty($data)){
		$formatData = json_decode($data,true);
		if(!empty($formatData)){
			
			$item = $formatData['item'];
			$size = (string)$formatData['size'];
			
			$base_roll = $formatData['base_roll'];
			$idUA = $formatData['idUA'];
			if(strpos($idUA,'new_id_')!==false){  // insert
				$sql = "INSERT INTO $table (`item`,`size`, `base_roll`) VALUES ('$item','$size', '$base_roll')";
			}else{
				// update
				$sql = "UPDATE $table SET item='$item',size='$size',base_roll='$base_roll' where ID='$idUA'";
			}
			$check_1 = mysqli_query($conn252, $sql);
			if($check_1){
				$response = [
					'status' => true,
					'mess' =>'' . $size,
				];
			}else{
				$response = [
						'status' => false,
						'mess' =>  $dbMi_73252->error
					];
			}
			echo json_encode($response);
		}
	}