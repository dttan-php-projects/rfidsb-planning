<?php
    //1. getPrintType
    function getPrintType($SOLINE)
    {
        $table      = 'setting_item_form';
        $table_vnso = "vnso";
        $table_vnso_total = "vnso_total";
        $data = '';

        //connect 
        $conn138 = getConnection138();
        $conn252 = getConnection();

        $detached = detachedSOLINE($SOLINE);
        extract($detached);
        if ($count_check == 1) { //Chi nhap SO#
            $queryCheck = "SELECT `ITEM`, `ORDER_NUMBER`, `LINE_NUMBER` FROM $table_vnso WHERE `ORDER_NUMBER`='$val_SO' ORDER BY LENGTH(LINE_NUMBER), LINE_NUMBER ASC  "; 
        } else {
            $queryCheck = "SELECT `ITEM`, `ORDER_NUMBER`, `LINE_NUMBER` FROM $table_vnso WHERE `ORDER_NUMBER`='$val_SO' AND `LINE_NUMBER`='$val_LINE' ORDER BY CREATEDDATE DESC LIMIT 0,1 "; 
        }

        $resultCheck = mysqli_query($conn138, $queryCheck);
        if (mysqli_num_rows($resultCheck) > 0) {
            $row = mysqli_fetch_all($resultCheck, MYSQLI_ASSOC);
        } else {
            if ($count_check == 1) { //Chi nhap SO#
                $queryCheck = "SELECT `ITEM`, `ORDER_NUMBER`, `LINE_NUMBER` FROM $table_vnso_total WHERE `ORDER_NUMBER`='$val_SO' ORDER BY LENGTH(LINE_NUMBER), LINE_NUMBER ASC  "; 
            } else {
                $queryCheck = "SELECT `ITEM`, `ORDER_NUMBER`, `LINE_NUMBER` FROM $table_vnso_total WHERE `ORDER_NUMBER`='$val_SO' AND `LINE_NUMBER`='$val_LINE' ORDER BY CREATEDDATE DESC LIMIT 0,1 "; 
            }

            $resultCheck = mysqli_query($conn138, $queryCheck);
            if (mysqli_num_rows($resultCheck) > 0 ) {
                $row = mysqli_fetch_all($resultCheck, MYSQLI_ASSOC);
            }
        }

        if (count($row) > 0 ) {
            foreach ($row as $value ) {

                $ORACLE_ITEM = trim($value['ITEM']);

                //check trường hợp item oracle là: VN FREIGHT CHARGE thì bỏ qua
                if( strpos($ORACLE_ITEM,'FREIGHT')!==FALSE || strpos($ORACLE_ITEM,'CHARGE')!==FALSE) {
                    continue;
                }
    
                //Truy van setting form
                $query = "SELECT FORM_TYPE FROM $table WHERE INTERNAL_ITEM = '$ORACLE_ITEM' LIMIT 1; "; 
                $result = mysqli_query($conn252, $query);
                
                if ( mysqli_num_rows($result) > 0 ) {
                    $result_item = mysqli_fetch_array($result);
                    $data = array (
                        'status_item' => 1,
                        'result_item' => $result_item['FORM_TYPE']
                    );
                }
                else {
                    $data = array (
                        'status_item' => 0
                    );
                }

            }

        }

        if($conn138) mysqli_close($conn138);
        if($conn252) mysqli_close($conn252);

        return $data;

    }  

    //2. getPrintType ms color
    function getPrintTypeMS($ITEM_VNSO)
    {

        $conn = getConnection();

        //Trường hợp MS COLOR: lấy item vnso kiểm tra item color, lấy ra internal_item, truy vấn vào setting form
        $queryMS = "SELECT internal_item FROM `ms_color` WHERE item_color = '$ITEM_VNSO'  "; 
        $resultMS = mysqli_query($conn, $queryMS);
        if($resultMS === FALSE) { die(mysql_error()); }
        if (mysqli_num_rows($resultMS) > 0) {
            $resultMS = mysqli_fetch_assoc($resultMS);
            $INTERNAL_ITEM = !empty($resultMS['internal_item']) ? trim($resultMS['internal_item']): '';

            //query
            $query = "SELECT FORM_TYPE FROM `setting_item_form` WHERE INTERNAL_ITEM = '$INTERNAL_ITEM'  "; 
            $result = mysqli_query($conn, $query);
            
            if ( mysqli_num_rows($result) > 0 ) {
                $result_item = mysqli_fetch_assoc($result);
                $data = array (
                    'status_item' => 1,
                    'result_item' => $result_item['FORM_TYPE']
                );
                //echo json_encode($data);
            }
            else {
                $data = array (
                    'status_item' => 0
                );
                //echo json_encode($data);
            }
        }

        if($conn) mysqli_close($conn);

        return $data;

    }  