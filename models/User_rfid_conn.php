<?php if ( ! defined('PATH_SYSTEM')) die ('routes Bad requested!');?>
<?php
    date_default_timezone_set('Asia/Ho_Chi_Minh');
    require_once ("__connection.php");
        
    function login($username, $password){
        
        $conn252   	= getConnection(); 
        $table      = 'user_rfid';
       
            // select all query
        $query = "SELECT `id`, `email`, `password`, `is_admin`, `created` FROM $table WHERE `email`= '$username' AND `password` = '$password' ";
        $result_user = toQueryAssoc($conn252, $query);
            
        return $result_user;
            
    }//End 7 

    function createUser($array ){

        $username = $array['username'];
        $password = $array['password'];
        $is_admin = $array['is_admin'];
        
        $conn252   	= getConnection();
        $table      = 'user_rfid';

        if (isUserExist($username ) == false ) {

            $result = mysqli_query($conn252, "INSERT INTO $table (`email`, `password`, `is_admin`) VALUES ('$username', '$password', '$is_admin') ; ");
            if (!$result ) return false; 

            return true;

        } else {
            return false;
        }
            
    }

    function updateUser($array ){

        $username = $array['username'];
        $password = $array['password'];
        $is_admin = $array['is_admin'];
        $created = date('Y-m-d H:i:s');
        
        $conn252   	= getConnection(); 
        $table      = 'user_rfid';
       //connect 

       if (isUserExist($username ) == false ) {
            return false;
        } else {

            $result = mysqli_query($conn252, "UPDATE $table SET `password` = '$password', `created` = '$created' WHERE `email` = '$username' ; ");
            if (!$result ) return false; 

            return true;
            
        }
       
            
    }

    function deleteUser($username ) {

        $conn252   	= getConnection();
        $table      = 'user_rfid';

        if (isUserExist($username) == true ) {
            $result = mysqli_query($conn252, "DELETE FROM $table WHERE `email` = '$username'; " );
            if (!$result ) {
                return false;
            } 
        } else { 
            return false;
        }

        return true;
        
    }

    function isUserExist($username ) {
        $conn252   	= getConnection();
        $table      = 'user_rfid';

        $query = "SELECT `email` FROM $table WHERE `email`= '$username' ; ";
        $user_item = toQueryAssoc($conn252, $query);
        
        return (count($user_item) > 0) ? true : false;

    }
