<?php
header("Content-Type: application/json");

require_once ( "../define_constant_system.php");
require_once (PATH_MODEL . "/__connection.php");

$data = $_POST['data'];
//$data = '["167"]';

$conn = getConnection();

if( !empty($data) ){
	$formatData = json_decode($data,true);
	if(!empty($formatData)){
		$listID = implode(',',$formatData);
		if(!empty($listID)){
			$delete_ua = "DELETE FROM ua WHERE ID IN ($listID);";
			$check_1 = mysqli_query($conn, $delete_ua);
			if($check_1){
				$response = [
					'status' => true,
					'mess' =>'Xóa thành công! Chương trình sẽ tự động load!'
				];
			}else{
				$response = [
						'status' => false,
						'mess' =>  $dbMi2->error
					];
			}
			echo json_encode($response); exit();
		}
	}
}