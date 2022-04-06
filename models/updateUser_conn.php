<?php
    require_once ( "../define_constant_system.php");
    require_once (PATH_MODEL . "/__connection.php");
    
    require_once (PATH_MODEL . "/User_rfid_conn.php");

    // get POST
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    if ( empty($username ) && empty($password ) ) {
        $data = [
            "status" => false,
            "message" => "Không lấy được dữ liệu POST "  
        ];
    }
    else {//count =1

        $userInfo = array(
            'username' => $username,
            'password' => $password,
            'is_admin' => 0,
            'created' => date('Y-m-d H:i:s')
        );
        if (isUserExist($username) == true ) {
            // update
            $update = updateUser($userInfo);
        } else {
            // create
            $update = createUser($userInfo);
        }

        if ($update == true ) {
            $data = [
                "status" => true,
                "message" => "Lưu thành công"  
            ];
        } else {
            $data = [
                "status" => false,
                "message" => "Không cập nhật được thông tin người dùng "  
            ];
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

