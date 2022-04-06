<?php
header("Content-Type: application/json");
require_once ( "../define_constant_system.php");
require_once (PATH_MODEL . "/__connection.php");
require_once (PATH_MODEL . "/automail_conn.php");
require_once (PATH_MODEL . "/getScrap_conn.php");//get getScrap function

require_once (PATH_DATA . "/detachedSOLINE.php");
require_once (PATH_DATA . "/formatDate.php");

//$data = trim($_POST['data']);
$data = $_POST['data'];
//$data = $_GET['data'];
if(!empty($data)){
	//connect 
	$conn = getConnection(); 
    
    $list_item = implode("','", $data);  
    $list_item = "'".$list_item."'";
    
    $sql = "SELECT size,base_roll FROM ua WHERE concat(item,size) IN ($list_item)";
    
    $rowsResult = mysqli_query($conn, $sql);
	$rowsResult = mysqli_fetch_all($rowsResult,MYSQLI_ASSOC);
    $data = [];
    if(!empty($rowsResult)){
        foreach ($rowsResult as $key => $valueNewSize) {
            $item_size = $valueNewSize['size'];
            $data[$item_size] = $valueNewSize['base_roll'];
        }
    }
	if(count($data)>0){
		$response = [
			'status' => true,
			'data'   => $data
		];		
	}else{
		$response = [
				'status' => false,
				'mess'   =>  "KHÔNG LẤY ĐƯỢC BASE ROLL, VUI LÒNG CẬP NHẬT!"
			];
		echo json_encode($response);die;
    }   
    
    if ($conn) mysqli_close($conn);
    
}else{
    $response = [
        'status' => false,
        'data'   => null
    ];
}
echo json_encode($response);
?>