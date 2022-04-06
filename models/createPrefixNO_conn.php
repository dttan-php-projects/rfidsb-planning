<?php

    function createPrefixNO($SO_LINE, $PRINT_TYPE) {
        
        $conn = getConnection();
        $table = "rfid_po_save";

        $prefixNO = '';

        $YearMonth = date('ym');
        if ($PRINT_TYPE == 'trim' || $PRINT_TYPE == 'trim_macy' ) {
            $prefixNO_time = 'TRIM.RF'.$YearMonth;
            $prefix = 'TRIM.RF';
        } else if ($PRINT_TYPE == 'pvh_rfid') {
            $prefixNO_time = 'RF'.$YearMonth;
            $prefix = 'RF';
        } else if ($PRINT_TYPE == 'ua_no_cbs' || $PRINT_TYPE == 'rfid' || $PRINT_TYPE == 'ua_cbs' || $PRINT_TYPE == 'cbs') {
            $prefixNO_time = 'RF'.$YearMonth;
            $prefix = 'RF';
        }

        if (isAlreadyExist($SO_LINE) == 1 ) {
            $sql = "SELECT PO_NO FROM $table WHERE PO_SO_LINE = '$SO_LINE' ORDER BY PO_CREATED_TIME DESC LIMIT 0,1 ";
            $query = mysqli_query($conn, $sql);
            if (mysqli_num_rows($query) > 0 ) {
                $po_no_check = mysqli_fetch_array($query, MYSQLI_ASSOC );
                $prefixNO = $po_no_check['PO_NO'];
            }
            
        } else {

            // prefix dạng RF/ TRIM.RF
            $sql = "SELECT PO_NO, PO_CREATED_TIME FROM $table WHERE PO_NO LIKE '$prefix%' ORDER BY PO_CREATED_TIME DESC LIMIT 0,1 ";
            $query = mysqli_query($conn, $sql);
            if (mysqli_num_rows($query) > 0 ) {

                // Lấy PO_NO mới nhất, dựa theo ngày tạo đơn
                $po_no_item = mysqli_fetch_array($query, MYSQLI_ASSOC );
                $po_no = $po_no_item['PO_NO'];

                $po_no_detached = explode('-', $po_no );
                
                // Tách PO_NO, Lấy giá trị tiền tố
                $prefixNO_save = $po_no_detached[0];

                // không so sánh với tháng hiện tại, cho sửa thủ công
                $prefixNO = $prefixNO_save;

            } else {
                
                // Form này chưa làm lệnh, tạo NO mới
                $prefixNO = $prefixNO_time;

            }

        }

        if ($conn) mysqli_close($conn);

        // results
        return $prefixNO;


    }

    // PrefixNO được lấy từ hàm createPrefixNO hoặc do người dùng nhập vào
    function createNO($prefixNO, $PRINT_TYPE ) {

        $conn = getConnection();
        $table = "rfid_po_save";
        
        $prefixNO = trim($prefixNO);
        $suffixNO = '-00001';

        $YearMonth = date('ym');
        if ($PRINT_TYPE == 'trim' || $PRINT_TYPE == 'trim_macy' ) {
            $prefixNO_time = 'TRIM.RF'.$YearMonth;
        } else if ($PRINT_TYPE == 'pvh_rfid') {
            $prefixNO_time = 'RF'.$YearMonth;
        } else if ($PRINT_TYPE == 'ua_no_cbs' || $PRINT_TYPE == 'rfid' || $PRINT_TYPE == 'ua_cbs' || $PRINT_TYPE == 'cbs') {
            $prefixNO_time = 'RF'.$YearMonth;
        }
        
        // prefix dạng RF/ TRIM.RF
        $sql = "SELECT PO_NO, PO_CREATED_TIME FROM $table WHERE PO_NO LIKE '$prefixNO%' ORDER BY PO_CREATED_TIME DESC LIMIT 0,1 ";
        $query = mysqli_query($conn, $sql);

        // Trường hợp  có dang NO theo tháng có trong save. Trường hợp ngược lại, NO theo người dùng nhập
        if (mysqli_num_rows($query) > 0 ) {

            // Lấy PO_NO mới nhất, dựa theo ngày tạo đơn
            $po_no_item = mysqli_fetch_array($query, MYSQLI_ASSOC );
            $po_no = $po_no_item['PO_NO'];

            $po_no_detached = explode('-', $po_no );
            
            // Tách PO_NO, Lấy giá trị tiền tố, hậu tố
            $prefixNO_save = $po_no_detached[0];
            $suffixNO_save = (int)$po_no_detached[1];

            // Trường hợp 1: User đã sửa NO có tháng > tháng hiện tại
            if (strcmp($prefixNO_save, $prefixNO_time ) >= 0 ) {
                $prefixNO = $prefixNO_save;
                // tạo suffixNO
                $suffixNO = $suffixNO_save+1;
                $length = strlen((string)$suffixNO);//chuyển đổi thành string xem có mấy ký tự
                if ($length == 1 ) {
                    $suffixNO = '0000'.$suffixNO;
                } else if ($length == 2 ) {
                    $suffixNO = '000'.$suffixNO;
                } else if ($length == 3 ) {
                    $suffixNO = '00'.$suffixNO;
                } else if ($length == 4 ) {
                    $suffixNO = '0'.$suffixNO;
                } else if ($length == 5 ) {
                    $suffixNO = $suffixNO;
                }

                $suffixNO = "-" . $suffixNO;


            } else {
                
                // Trường hợp 2: Chưa có đơn trong tháng này, tạo mới NO dựa theo tháng năm hiện tại
                $prefixNO = $prefixNO_time;
                // $suffixNO: default
            }


        } 

        if ($conn) mysqli_close($conn);

        // results
        return $prefixNO . $suffixNO;

    }

    /* START ========================================================================================================================== */
    
    date_default_timezone_set('Asia/Ho_Chi_Minh'); ini_set('max_execution_time',300);
    header("Content-Type: application/json; charset=utf-8");

    require_once ( "../define_constant_system.php");
    require_once (PATH_MODEL . "/__connection.php");
    require_once (PATH_MODEL . "/checkSOExist_conn.php");
    require_once (PATH_DATA . "/detachedSOLINE.php");
    
    // CONNECT
    $conn       = getConnection();

    //GET METHOD
    $SO_LINE_0  = $_GET['so_line_0'];
    $PRINT_TYPE = $_GET['print_type'];

    // $SO_LINE_0  = '45354904-1';
    // $PRINT_TYPE = 'rfid';

    if (empty($SO_LINE_0) || empty($PRINT_TYPE) ) {
        
        $data = array(
            'status'    => false,
            'message'   => 'Get Data is empty '
        );

    } else {

        $prefixNo = createPrefixNO($SO_LINE_0, $PRINT_TYPE );

    }

    //sent data
    if ( !empty($prefixNo) ) {
        $data = array(
            'status'    => true,
            'message'   => 'Create New Prefix NO is Success',
            'po_no_new' => $prefixNo
        );
    } else {
        $data = array(
            'status'    => false,
            'message'   => 'Create New Prefix NO is fail, Input NO format is RF2007 or TRIM.RF2007'
        );
    }
    
    echo json_encode($data);

?>
