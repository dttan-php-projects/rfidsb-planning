<?php
    require_once ( "../define_constant_system.php");
    require_once (PATH_MODEL . "/__connection.php");

    //connect 
        $conn252 = getConnection();
        $conn138 = getConnection138();
        $table = 'rfid_po_master_item_combine';
        $table_vnso = "vnso";
        $table_vnso_total = "vnso_total";
    // get Item
        $SO_LINE = isset($_GET['SO_LINE']) ? trim($_GET['SO_LINE']) : '';

        $SO_LINE_ARR = explode('-', $SO_LINE);
    // check 
        if (!isset($SO_LINE_ARR[1]) || empty($SO_LINE_ARR[1]) ) {
            $results = array(
                "status" => false,
                "message" => "Line is empty"
            );
            echo json_encode($results); exit();
        }

    // init results
        $results = array();

    // check
        if (empty($SO_LINE ) ) {
            $results = array(
                "status" => false,
                "message" => "SO# is empty"
            );
        } else {

            $sqlCheck = "SELECT `ORDERED_ITEM`, `SHIP_TO_CUSTOMER` FROM $table_vnso WHERE `ORDER_NUMBER`='$SO_LINE_ARR[0]' AND `LINE_NUMBER` = '$SO_LINE_ARR[1]' ORDER BY ID DESC LIMIT 1 ;";
            $queryCheck = mysqli_query($conn138, $sqlCheck );
            if (mysqli_num_rows($queryCheck) < 1 ) {
                $sqlCheck = "SELECT `ORDERED_ITEM`, `SHIP_TO_CUSTOMER` FROM $table_vnso_total WHERE `ORDER_NUMBER`='$SO_LINE_ARR[0]' AND `LINE_NUMBER` = '$SO_LINE_ARR[1]' ORDER BY ID DESC LIMIT 1 ;";
                $queryCheck = mysqli_query($conn138, $sqlCheck );
            }

            // get ordered item
            $resultCheck = mysqli_fetch_array($queryCheck, MYSQLI_ASSOC);
            $ORDERED_ITEM = (!empty($resultCheck) ) ? trim($resultCheck['ORDERED_ITEM']) : '';
            $SHIP_TO_CUSTOMER = (!empty($resultCheck) ) ? trim($resultCheck['SHIP_TO_CUSTOMER']) : '';

            // check 
            if (empty($ORDERED_ITEM) ) {
                $results = array(
                    "status" => false,
                    "message" => "Ordered Item is not exist"
                );
            } else {

                $ORDERED_ITEM = substr($ORDERED_ITEM, 0,9); 
                // ship to là worldon
                if (strpos(strtolower($SHIP_TO_CUSTOMER), 'worldon' ) !==false ) {
                    $sql = "SELECT `ordered_item_rfid`, `ordered_item_thermal` FROM $table WHERE `ordered_item_rfid` = '$ORDERED_ITEM';";
                    $query = mysqli_query($conn252, $sql );
                    if (!$query ) {
                        $results = array(
                            "status" => false,
                            "message" => "Query Error: $sql "
                        );
                    } else {
                        if (mysqli_num_rows($query) > 0 ) {
                            $results = array(
                                "status" => true,
                                "message" => "Internal Item is exist"
                            );
                        } else {
                            $results = array(
                                "status" => false,
                                "message" => "Internal Item is not exist"
                            );
                        }
                    }
                } else {
                    $results = array(
                        "status" => false,
                        "message" => "Ship to khác WORLDON"
                    );
                }

                

                
            }
            
            

        }

        // if ($conn252) mysqli_close($conn252);
        // if ($conn138) mysqli_close($conn138);

        // results
        echo json_encode($results); exit();