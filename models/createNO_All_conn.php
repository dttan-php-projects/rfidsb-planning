<?php
    date_default_timezone_set('Asia/Ho_Chi_Minh'); ini_set('max_execution_time',300);
    header("Content-Type: application/json; charset=utf-8");

    require_once ( "../define_constant_system.php");
    require_once (PATH_MODEL . "/__connection.php");
    require_once (PATH_MODEL . "/checkSOExist_conn.php");
    require_once (PATH_DATA . "/detachedSOLINE.php");
    
?>
<?php

///1111111111 Ink no cbs: INK_NO
function createINK_NO($PO_NO) {
    //connect host
    $conn = getConnection(); 
    $table = 'rfid_po_ink_no_cbs_save';
    
    $result1 = mysqli_query($conn, "SELECT INK_NO FROM $table WHERE INK_PO_NO = '$PO_NO'; ");
    $num1 = mysqli_num_rows($result1);
    if ($num1 > 0 ) {   
        //Xóa hết các so_line mà có mã đơn PO_NO
        $row = mysqli_fetch_array($result1, MYSQLI_ASSOC);
        $INK_NO_NEW = $row['INK_NO'];//set INK_NO
        
        $result2 = mysqli_query($conn, "DELETE FROM $table WHERE INK_PO_NO = '$PO_NO'; ");
        

    } else {//không có thì tạo mới
        //Nếu không có, kiểm tra tiếp để vẫn giữ MN_NO và tăng MN_ID lên (trường hợp có nhiều SOLINE)
        $query1 = "SELECT INK_NO FROM $table WHERE INK_PO_NO = '$PO_NO' ";
        $result1 = mysqli_query($conn, $query1);
        $num1 = mysqli_num_rows($result1);
        if ($num1 > 0) { 
            $row1 = mysqli_fetch_array($result1);
            $INK_NO_NEW = $row1['INK_NO'];//set INK_NO
        } else {
            $YearMonth = date('ym');
            $pre_INK_NO = 'INK'.$YearMonth;//có thì tăng lên +1, không có thì là qua tháng mới

            $query3 = "SELECT INK_NO,INK_ID FROM $table WHERE INK_NO LIKE '$pre_INK_NO%' ORDER BY INK_NO DESC LIMIT 0,1 ";
            $result3 = mysqli_query($conn, $query3);
            $num3 = mysqli_num_rows($result3);
            if ($num3 > 0) {
                $row3 = mysqli_fetch_array($result3);
                $INK_NO_CUR = $row3['INK_NO'];
                //tách INK_NO_CUR hiện tại ra bởi dấu '-', lấy phần số kiểm tra
                $detachINK_NO_CUR = explode('-',$INK_NO_CUR);
                $num_INK_NO_CUR = (int)$detachINK_NO_CUR[1];//to change to number

                //set INK_NO
                $num_INK_NO = $num_INK_NO_CUR+1;
                $len_INK_NO = strlen((string)$num_INK_NO);//chuyển đổi thành string xem có mấy ký tự
                if ( $len_INK_NO == 1) {
                    $num_INK_NO = '0000'.$num_INK_NO;
                    $INK_NO_NEW = $pre_INK_NO.'-'.$num_INK_NO;
                } else if ( $len_INK_NO == 2) {
                    $num_INK_NO = '000'.$num_INK_NO;
                    $INK_NO_NEW = $pre_INK_NO.'-'.$num_INK_NO;
                } else if ( $len_INK_NO == 3 ) {
                    $num_INK_NO = '00'.$num_INK_NO;
                    $INK_NO_NEW = $pre_INK_NO.'-'.$num_INK_NO;
                } else if ( $len_INK_NO == 4 ) {
                    $num_INK_NO = '0'.$num_INK_NO;
                    $INK_NO_NEW = $pre_INK_NO.'-'.$num_INK_NO;
                } else if ( $len_INK_NO == 5 ) {
                    $num_INK_NO = $num_INK_NO;
                    $INK_NO_NEW = $pre_INK_NO.'-'.$num_INK_NO;
                }
            } else {//trường hợp không có, tức là đã qua tháng mới, set lại PO_NO từ đầu
                $INK_NO_NEW = $pre_INK_NO.'-00001';
            }
        }
        
        
    }

    if ($conn) mysqli_close($conn);

    return $INK_NO_NEW;

}//end function

//2222222222222 Material no cbs: MN_NO
function createMATERIAL_NO($PO_NO) {
    //connect host
    $conn = getConnection();
    $table = 'rfid_po_material_no_cbs_save';

    $result1 = mysqli_query($conn, "SELECT MN_NO FROM $table WHERE MN_PO_NO = '$PO_NO'; " );
    $num1 = mysqli_num_rows($result1);
    if ($num1 > 0) {   
        //Xóa hết các so_line mà có mã đơn PO_NO
        $row1 = mysqli_fetch_array($result1);
        $MN_NO_NEW = $row1['MN_NO'];//set MN_NO

        $result2 = mysqli_query($conn, "DELETE FROM $table WHERE MN_PO_NO = '$PO_NO'; " );
        
        
    } else {//không có thì tạo mới
        //Nếu không có, kiểm tra tiếp để vẫn giữ MN_NO và tăng MN_ID lên (trường hợp có nhiều SOLINE)
        $query1 = "SELECT MN_NO FROM $table WHERE MN_PO_NO = '$PO_NO' ";
        $result1 = mysqli_query($conn, $query1);
        $num1 = mysqli_num_rows($result1);
        if ($num1 > 0) { 
            $row1 = mysqli_fetch_array($result1);
            $MN_NO_NEW = $row1['MN_NO'];//set MN_NO
        } else {
            $YearMonth = date('ym');
            $pre_MN_NO = 'MN'.$YearMonth;//có thì tăng lên +1, không có thì là qua tháng mới

            $query3 = "SELECT MN_NO FROM $table WHERE MN_NO LIKE '$pre_MN_NO%' ORDER BY MN_NO DESC LIMIT 0,1 ";
            $result3 = mysqli_query($conn, $query3);
            $num3 = mysqli_num_rows($result3);
            if ($num3 > 0) {
                $row3 = mysqli_fetch_array($result3);
                $MN_NO_CUR = $row3['MN_NO'];
                //tách MN_NO_CUR hiện tại ra bởi dấu '-', lấy phần số kiểm tra
                $detachMN_NO_CUR = explode('-',$MN_NO_CUR);
                $num_MN_NO_CUR = (int)$detachMN_NO_CUR[1];//to change to number

                //set MN_NO
                $num_MN_NO = $num_MN_NO_CUR+1;
                $len_MN_NO = strlen((string)$num_MN_NO);//chuyển đổi thành string xem có mấy ký tự
                if ( $len_MN_NO == 1) {
                    $num_MN_NO = '0000'.$num_MN_NO;
                    $MN_NO_NEW = $pre_MN_NO.'-'.$num_MN_NO;
                } else if ( $len_MN_NO == 2) {
                    $num_MN_NO = '000'.$num_MN_NO;
                    $MN_NO_NEW = $pre_MN_NO.'-'.$num_MN_NO;
                } else if ( $len_MN_NO == 3 ) {
                    $num_MN_NO = '00'.$num_MN_NO;
                    $MN_NO_NEW = $pre_MN_NO.'-'.$num_MN_NO;
                } else if ( $len_MN_NO == 4 ) {
                    $num_MN_NO = '0'.$num_MN_NO;
                    $MN_NO_NEW = $pre_MN_NO.'-'.$num_MN_NO;
                } else if ( $len_MN_NO == 5 ) {
                    $num_MN_NO = $num_MN_NO;
                    $MN_NO_NEW = $pre_MN_NO.'-'.$num_MN_NO;
                }
            } else {//trường hợp không có, tức là đã qua tháng mới, set lại PO_NO từ đầu
                $MN_NO_NEW = $pre_MN_NO.'-00001';
            }

        }
        
    }
    if ($conn) mysqli_close($conn);

    return $MN_NO_NEW;

}//end function

//333333333333  Material CBS: M_NO
function createMATERIALCBS_NO($PO_NO) {
    //connect host
    $conn = getConnection();
    $table = 'rfid_po_material_cbs_save';
    
    $query1 = "SELECT M_NO FROM $table WHERE M_PO_NO = '$PO_NO' ";
    $result1 = mysqli_query($conn, $query1);
    $num1 = mysqli_num_rows($result1);
    if ($num1 > 0) {   
        //Xóa hết các so_line mà có mã đơn PO_NO
        $row1 = mysqli_fetch_array($result1);
        $M_NO_NEW = $row1['M_NO'];//set M_NO

        $result2 = mysqli_query($conn, "DELETE FROM $table WHERE M_PO_NO = '$PO_NO'; ");

        
    } else {//không có thì tạo mới
        $query1 = "SELECT M_NO FROM $table WHERE M_PO_NO = '$PO_NO' ";
        $result1 = mysqli_query($conn, $query1);
        $num1 = mysqli_num_rows($result1);
        if ($num1 > 0) {
            $row1 = mysqli_fetch_array($result1);
            $M_NO_NEW = $row1['M_NO'];//set M_NO
        } else {
            $YearMonth = date('ym');
            $pre_M_NO = 'M'.$YearMonth;//có thì tăng lên +1, không có thì là qua tháng mới

            $query3 = "SELECT M_NO FROM $table WHERE M_NO LIKE '$pre_M_NO%' ORDER BY M_NO DESC LIMIT 0,1 ";
            $result3 = mysqli_query($conn, $query3);
            $num3 = mysqli_num_rows($result3);
            if ($num3 > 0) {
                $row3 = mysqli_fetch_array($result3);
                $M_NO_CUR = $row3['M_NO'];
                //tách M_NO_CUR hiện tại ra bởi dấu '-', lấy phần số kiểm tra
                $detachM_NO_CUR = explode('-',$M_NO_CUR);
                $num_M_NO_CUR = (int)$detachM_NO_CUR[1];//to change to number

                //set M_NO
                $num_M_NO = $num_M_NO_CUR+1;
                $len_M_NO = strlen((string)$num_M_NO);//chuyển đổi thành string xem có mấy ký tự
                if ( $len_M_NO == 1) {
                    $num_M_NO = '0000'.$num_M_NO;
                    $M_NO_NEW = $pre_M_NO.'-'.$num_M_NO;
                } else if ( $len_M_NO == 2) {
                    $num_M_NO = '000'.$num_M_NO;
                    $M_NO_NEW = $pre_M_NO.'-'.$num_M_NO;
                } else if ( $len_M_NO == 3 ) {
                    $num_M_NO = '00'.$num_M_NO;
                    $M_NO_NEW = $pre_M_NO.'-'.$num_M_NO;
                } else if ( $len_M_NO == 4 ) {
                    $num_M_NO = '0'.$num_M_NO;
                    $M_NO_NEW = $pre_M_NO.'-'.$num_M_NO;
                } else if ( $len_M_NO == 5 ) {
                    $num_M_NO = $num_M_NO;
                    $M_NO_NEW = $pre_M_NO.'-'.$num_M_NO;
                }
            } else {//trường hợp không có, tức là đã qua tháng mới, set lại PO_NO từ đầu
                $M_NO_NEW = $pre_M_NO.'-00001';
            }
        }

    }

    if ($conn) mysqli_close($conn);

    return $M_NO_NEW;

}//end function

///44444444444444 Size CBS: S_NO
function createSize_NO($PO_NO) {
    
    $conn = getConnection();
    $table = 'rfid_po_size_cbs_save';
    
    $query1 = "SELECT S_NO FROM $table WHERE S_PO_NO = '$PO_NO' ";
    $result1 = mysqli_query($conn, $query1);
    $num1 = mysqli_num_rows($result1);
    if ($num1 > 0) {   
        //Xóa hết các so_line mà có mã đơn PO_NO
        $row1 = mysqli_fetch_array($result1);
        $S_NO_NEW = $row1['S_NO'];//set S_NO

        $result2 = mysqli_query($conn, "DELETE FROM $table WHERE S_PO_NO = '$PO_NO'; ");

    } else {//không có thì tạo mới
        // $table = 'rfid_po_size_cbs_save';
        $query1 = "SELECT S_NO FROM $table WHERE S_PO_NO = '$PO_NO' ";
        $result1 = mysqli_query($conn, $query1);
        $num1 = mysqli_num_rows($result1);
        if ($num1 > 0) {   
            $row1 = mysqli_fetch_array($result1);
            $S_NO_NEW = $row1['S_NO'];//set S_NO
        } else {
            $YearMonth = date('ym');
            $pre_S_NO = 'S'.$YearMonth;//có thì tăng lên +1, không có thì là qua tháng mới

            $query3 = "SELECT S_NO FROM $table WHERE S_NO LIKE '$pre_S_NO%' ORDER BY S_NO DESC LIMIT 0,1 ";
            $result3 = mysqli_query($conn, $query3);
            $num3 = mysqli_num_rows($result3);
            if ($num3 > 0) {
                $row3 = mysqli_fetch_array($result3);
                $S_NO_CUR = $row3['S_NO'];
                //tách S_NO_CUR hiện tại ra bởi dấu '-', lấy phần số kiểm tra
                $detachS_NO_CUR = explode('-',$S_NO_CUR);
                $num_S_NO_CUR = (int)$detachS_NO_CUR[1];//to change to number

                //set S_NO
                $num_S_NO = $num_S_NO_CUR+1;
                $len_S_NO = strlen((string)$num_S_NO);//chuyển đổi thành string xem có mấy ký tự
                if ( $len_S_NO == 1) {
                    $num_S_NO = '0000'.$num_S_NO;
                    $S_NO_NEW = $pre_S_NO.'-'.$num_S_NO;
                } else if ( $len_S_NO == 2) {
                    $num_S_NO = '000'.$num_S_NO;
                    $S_NO_NEW = $pre_S_NO.'-'.$num_S_NO;
                } else if ( $len_S_NO == 3 ) {
                    $num_S_NO = '00'.$num_S_NO;
                    $S_NO_NEW = $pre_S_NO.'-'.$num_S_NO;
                } else if ( $len_S_NO == 4 ) {
                    $num_S_NO = '0'.$num_S_NO;
                    $S_NO_NEW = $pre_S_NO.'-'.$num_S_NO;
                } else if ( $len_S_NO == 5 ) {
                    $num_S_NO = $num_S_NO;
                    $S_NO_NEW = $pre_S_NO.'-'.$num_S_NO;
                }
            } else {//trường hợp không có, tức là đã qua tháng mới, set lại PO_NO từ đầu
                $S_NO_NEW = $pre_S_NO.'-00001';
            }
        }

    }
    
    if ($conn) mysqli_close($conn);

    return $S_NO_NEW;

}//end function

///5555555555555 Material - Ink form ngang. MI_NO
function createMATERIAL_INK_NO($PO_NO) {

    $conn       = getConnection(); 
    $table = 'rfid_po_material_ink_save';
    
    $query1 = "SELECT MI_NO FROM $table WHERE MI_PO_NO = '$PO_NO'  ";
    $result1 = mysqli_query($conn, $query1);
    $num1 = mysqli_num_rows($result1);
    if ($num1 > 0) {   
        //Xóa hết các so_line mà có mã đơn PO_NO
        $row1 = mysqli_fetch_array($result1);
        $MI_NO_NEW = $row1['MI_NO'];//set MI_NO

        $result2 = mysqli_query($conn, "DELETE FROM $table WHERE MI_PO_NO = '$PO_NO';");
        
    } else {//không có thì tạo mới
        $query1 = "SELECT MI_NO FROM $table WHERE MI_PO_NO = '$PO_NO' ";
        $result1 = mysqli_query($conn, $query1);
        $num1 = mysqli_num_rows($result1);
        if ($num1 > 0) { 
            $row1 = mysqli_fetch_array($result1);
            $MI_NO_NEW = $row1['MI_NO'];//set MI_NO
        } else {
            $YearMonth = date('ym');
            $pre_MI_NO = 'MI'.$YearMonth;//có thì tăng lên +1, không có thì là qua tháng mới

            $query3 = "SELECT MI_NO FROM $table WHERE MI_NO LIKE '$pre_MI_NO%' ORDER BY MI_NO DESC LIMIT 0,1 ";
            $result3 = mysqli_query($conn, $query3);
            $num3 = mysqli_num_rows($result3);
            if ($num3 > 0) {
                $row3 = mysqli_fetch_array($result3);
                $MI_NO_CUR = $row3['MI_NO'];
                //tách MI_NO_CUR hiện tại ra bởi dấu '-', lấy phần số kiểm tra
                $detachMI_NO_CUR = explode('-',$MI_NO_CUR);
                $num_MI_NO_CUR = (int)$detachMI_NO_CUR[1];//to change to number

                //set MI_NO
                $num_MI_NO = $num_MI_NO_CUR+1;
                $len_MI_NO = strlen((string)$num_MI_NO);//chuyển đổi thành string xem có mấy ký tự
                if ( $len_MI_NO == 1) {
                    $num_MI_NO = '0000'.$num_MI_NO;
                    $MI_NO_NEW = $pre_MI_NO.'-'.$num_MI_NO;
                } else if ( $len_MI_NO == 2) {
                    $num_MI_NO = '000'.$num_MI_NO;
                    $MI_NO_NEW = $pre_MI_NO.'-'.$num_MI_NO;
                } else if ( $len_MI_NO == 3 ) {
                    $num_MI_NO = '00'.$num_MI_NO;
                    $MI_NO_NEW = $pre_MI_NO.'-'.$num_MI_NO;
                } else if ( $len_MI_NO == 4 ) {
                    $num_MI_NO = '0'.$num_MI_NO;
                    $MI_NO_NEW = $pre_MI_NO.'-'.$num_MI_NO;
                } else if ( $len_MI_NO == 5 ) {
                    $num_MI_NO = $num_MI_NO;
                    $MI_NO_NEW = $pre_MI_NO.'-'.$num_MI_NO;
                }
            } else {//trường hợp không có, tức là đã qua tháng mới, set lại PO_NO từ đầu
                $MI_NO_NEW = $pre_MI_NO.'-00001';
            }
        }//else
        
    }
    
    if ($conn) mysqli_close($conn);


    return $MI_NO_NEW;

}//end function