<?php
    require_once ( "../define_constant_system.php");
    require_once (PATH_MODEL . "/__connection.php");

    function getOrdersData($SO_LINE )
    { 
        // connect 
            $conn138 = getConnection138();
            $table_vnso = "vnso";
            $table_vnso_total = "vnso_total";

        // init
            $results = array();

        // detache
            $SO_LINE_ARR = explode('-', $SO_LINE);
            $ORDER_NUMBER = $SO_LINE_ARR[0];
            $LINE_NUMBER = isset($SO_LINE_ARR[1]) ? $SO_LINE_ARR[1] : '';

        // check line
            if (!empty($LINE_NUMBER) ) {
                // sql
                    $where = " `ORDER_NUMBER`='$ORDER_NUMBER' AND `LINE_NUMBER` = '$LINE_NUMBER' ORDER BY ID DESC LIMIT 1 ;";
                    $sqlCheck = "SELECT `SOLD_TO_CUSTOMER`, `ORDERED_ITEM`, `SHIP_TO_CUSTOMER` FROM $table_vnso WHERE $where ";
                    $queryCheck = mysqli_query($conn138, $sqlCheck );                    
                    if (mysqli_num_rows($queryCheck) < 1 ) $sqlCheck = "SELECT `SOLD_TO_CUSTOMER`, `ORDERED_ITEM`, `SHIP_TO_CUSTOMER` FROM $table_vnso_total WHERE $where ";
                    
                    $queryCheck = mysqli_query($conn138, $sqlCheck );

                // get ordered item
                    $results = mysqli_fetch_array($queryCheck, MYSQLI_ASSOC);
            }

        // results
            return $results;

    }

    function checkNikeRBO($RBO )
    {
        return (strpos(strtoupper($RBO), 'NIKE') !== false ) ? true : false;
    }

    function checkWORLDON($SHIP_TO_CUSTOMER )
    {
        return (strpos(strtoupper($SHIP_TO_CUSTOMER), 'WORLDON') !== false ) ? true : false;
    }


    function checkTINHLOI($SHIP_TO_CUSTOMER )
    {
        return (strpos(strtoupper($SHIP_TO_CUSTOMER), 'MAY TINH LOI') !== false ) ? true : false;
    }

    function checkWORLDONCombine($checkWORLDON, $ORDERED_ITEM ) 
    {
        //connect 
            $conn252 = getConnection();
            $table = 'rfid_po_master_item_combine';

        // init
            $result = false;

        // get 9 char
            $ORDERED_ITEM = substr($ORDERED_ITEM, 0,9); 

        // check 
            if ($checkWORLDON == true ) {
                $sql = "SELECT `ordered_item_rfid` FROM $table WHERE `ordered_item_rfid` = '$ORDERED_ITEM';";
                $query = mysqli_query($conn252, $sql );
                if (mysqli_num_rows($query) > 0 ) $result = true;
            }
            
        // return
            return $result;

    }

    function checkTINHLOICombine($checkTL )
    {     
        return ($checkTL == true) ? true : false;        
    }

    // GET method
        $SO_LINE = isset($_GET['SO_LINE']) ? trim($_GET['SO_LINE']) : '';


    // get
        $check = 0;
        $status = false;
        $message = '';

    // check 
        $ordersData = getOrdersData($SO_LINE );
        if (empty($ordersData) ) {
            $message = "Không lấy được dữ liệu Automail";
        } else {
            $SOLD_TO_CUSTOMER = trim($ordersData['SOLD_TO_CUSTOMER']);
            $ORDERED_ITEM = trim($ordersData['ORDERED_ITEM']);
            $SHIP_TO_CUSTOMER = trim($ordersData['SHIP_TO_CUSTOMER']);

            $checkNIKE = checkNikeRBO($SOLD_TO_CUSTOMER );
            $checkWORLDON = checkWORLDON($SHIP_TO_CUSTOMER );

            if ($checkNIKE == false ) {
                $message = "Không phải đơn hàng NIKE";
            } else {

                // check ship to WORLDON
                    $checkWORLDON = checkWORLDON($SHIP_TO_CUSTOMER );
                // check combine NIKE WORLDON
                    $checkWORLDONCombine = checkWORLDONCombine($checkWORLDON, $ORDERED_ITEM );
                    // echo "checkWORLDONCombine: $checkWORLDONCombine";

                    if ($checkWORLDONCombine == true ) {
                        $check = 1; // nike worldon = 1
                        $status = true;
                        $message = "ĐƠN HÀNG NIKE - WORLDON";
                    } else {
                        $checkTL = checkTINHLOI($SHIP_TO_CUSTOMER);
                        $checkTINHLOICombine = checkTINHLOICombine($checkTL );
                        if ($checkTINHLOICombine == true ) {
                            $check = 2; // TINH LOI
                            $status = true;
                            $message = "ĐƠN HÀNG NIKE - TINH LOI ";
                        }
                    }
            }
        }

    // results
        $results = array( 'check' => $check, 'status' => $status, 'message' => $message );
        echo json_encode($results); exit();