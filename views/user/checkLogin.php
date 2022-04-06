<?php
    require_once ("../../define_constant_system.php");
    require_once (PATH_MODEL . "/__connection.php");
    
    require_once (PATH_MODEL . "/User_rfid_conn.php");
    //require_once (PATH_MODEL . "/Vnso_zero_conn2.php");
    require_once (PATH_MODEL . "/automail_conn.php"); 
   
    //require_once (PATH_MODEL . "/checkSOExist_conn.php");
    

//1. check Login
function login_check()
{   
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    //$user = login($username, $password);

    if ( empty( $username )  && empty( $password ) ) {
      
        header('Location: login.php?');
      // echo "Empty username or password";
    }
    else {//count =1
    
        $result = login($username, $password);
        $count = count($result);
        if ( $count > 0 ) {
            $row = $result;

            $data = array (
              "status"  => true,
              "message" => "Success Login!",
              "id"      => $row['id'],
              "username"   => $row['email'],
              "is_admin"   => $row['is_admin'],
            );
            extract($data);

            require_once ("../../assets/cookie/cookie_VNRISIntranet.php");
            if(!isset($_COOKIE["VNRISIntranet"])) {
                // header('Content-type: text/html; charset=utf-8');  
                header('Location: login.php');
            } 
            else {
                header('Location: ../../index.php?login='.$username );
            }
        } 
        else {
            $data = array (
                "status"  => false,
                "message" => "Invalid Username or password",
                "username"   => $username
              );

              // header('Content-type: text/html; charset=utf-8');  
              header('Location: login.php??'); 
                
          

        }
          
    }//end else
    
    //return $data;   

}//end Login

login_check();

