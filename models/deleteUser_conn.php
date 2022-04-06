<?php
    require_once ( "../define_constant_system.php");
    require_once (PATH_MODEL . "/__connection.php");
    require_once (PATH_MODEL . "/User_rfid_conn.php");

    // get POST
    $username = isset($_GET['username']) ? trim($_GET['username']) : '';

    if ( empty($username ) ) {
        $data = [
            "status" => false,
            "message" => "Không lấy được dữ liệu "  
        ];
    }
    else {//count =1

        if (isUserExist($username) == false ) {
            $data = [
                "status" => false,
                "message" => "Không tồn tại username: $username trong dữ liệu "  
            ];
        } else {
            $query = deleteUser($username);
            if ($query == true ) {
                $data = [
                    "status" => true,
                    "message" => "Xóa username: $username thành công "  
                ];
            } else {
                $data = [
                    "status" => false,
                    "message" => "Lỗi khi xóa username: $username "  
                ];
            }
        }
    
          
    }//end else

    $results = json_encode($data);

?>

<script>

    var results = '<?php print_r($results); ?>';
	results = JSON.parse(results);

	// check false
    alert(results.message);
    window.location ='../';


</script>

