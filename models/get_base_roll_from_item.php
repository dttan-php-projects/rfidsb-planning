<?php
header("Content-Type: application/json");
require_once ( "../define_constant_system.php");
require_once (PATH_MODEL . "/__connection.php");
require_once (PATH_MODEL . "/automail_conn.php");
require_once (PATH_MODEL . "/getScrap_conn.php");//get getScrap function

require_once (PATH_DATA . "/detachedSOLINE.php");
require_once (PATH_DATA . "/formatDate.php");

$data = $_POST['data'];

//$data = 'ST-908033-WCS-CS';
if(!empty($data)){
	//connect 
	$conn = getConnection(); 

    $item = trim($data);  
    $sql = "SELECT * FROM ua WHERE item='$item';";
    $rowsResult = mysqli_query($conn, $sql);
	$rowsResult = mysqli_fetch_all($rowsResult,MYSQLI_ASSOC);
    $data = [];
    if(!empty($rowsResult)){
        foreach ($rowsResult as $key => $value) {
            $item_size = $item.$value['size'];
            $data[$item_size] = $value['base_roll'];
        }
    }
    $response = [
        'status' => true,
        'data' => $data,
    ]; 

    if ($conn) mysqli_close($conn);
    
}else{
    $response = [
        'status' => false,
        'data' => null,
    ];
}
echo json_encode($response);
?>