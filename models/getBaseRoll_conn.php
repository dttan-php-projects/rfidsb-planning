<?php
	date_default_timezone_set('Asia/Ho_Chi_Minh');
	ini_set('max_execution_time',300);
	header("Content-Type: application/json; charset=utf-8");

	require_once ( "../define_constant_system.php");
	require_once (PATH_MODEL . "/__connection.php");
	require_once (PATH_DATA . "/detachedSOLINE.php");

    

    $data = $_POST['data'];
    if(!empty($data)){

        //connect 
            $conn1 = getConnection();

        $item = $data;  
        $query = "SELECT SIZE, BASE_ROLL FROM ua WHERE ITEM='$item'";
        $rowsResult = mysqli_query($conn1, $query);
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
        
        if ($conn1) mysqli_close($conn1);
        
    }else{
        $response = [
            'status' => false,
            'data' => null,
        ];
    }
    echo json_encode($response);