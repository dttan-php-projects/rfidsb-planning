<?php
    date_default_timezone_set('Asia/Ho_Chi_Minh');
    // require_once ( "../define_constant_system.php");
    // require_once (PATH_MODEL . "/__connection.php");
    // require_once (PATH_MODEL . "/checkSOExist_conn.php");
    // require_once (PATH_DATA . "/detachedSOLINE.php");

    function isAlreadyExist($SO_LINE){
            
        //connect host    
            $conn = getConnection(); 
            $table = "rfid_po_save";

        $detached = detachedSOLINE($SO_LINE);
        extract($detached);
        $val_SO = $val_SO."%";

        if ( $count_check == 2 ) //input SOLINE is 1 line
        {
            $query = "SELECT PO_NO FROM $table WHERE PO_SO_LINE ='$SO_LINE'";
            $result = toQuery ($conn, $query);
            if( !empty ($result) ) {
                    $checkSOExist = 1;
            } else  $checkSOExist = 0;
            
            
        } else { ////input SOLINE is only SO
            $query = "SELECT PO_NO FROM $table WHERE PO_SO_LINE LIKE '$val_SO' ";
            $result = toQuery( $conn, $query );
            if( !empty ($result) ) {
                    $checkSOExist = 1;
            } else  $checkSOExist = 0;
        }

        // if ($conn) mysqli_close($conn);

        return $checkSOExist;
            
    }//1. End isAlreadyExist
