<?php if (!defined('PATH_SYSTEM')) die('routes Bad requested!');
date_default_timezone_set('Asia/Ho_Chi_Minh'); ?>
<?php

//1. get record createddate column in  vnso table OK
function getCreateddate()
{
    $query = "SELECT CREATEDDATE FROM autoload_log where FUNC = 'AUTOMAIL' order by ID desc limit 0,1;";
    $result = toQueryArr(getConnection138(), $query);

    return !empty($result) ? $result['CREATEDDATE'] : '';
} //end 1


//2. check SOLINE
function checkSOLINE($SO_LINE)
{
    $table_vnso = 'vnso';
    $table_vnso_total = 'vnso_total';
    //connect 
    $conn = getConnection138();

    $detached = detachedSOLINE($SO_LINE);
    extract($detached);

    $list = array();

    if ($count_check == 1) {
        $query = "SELECT ID,ORDER_NUMBER,LINE_NUMBER,QTY,ITEM,PROMISE_DATE,REQUEST_DATE,ORDERED_DATE,CS,PACKING_INSTRUCTIONS,
                            ORDER_TYPE_NAME,BILL_TO_CUSTOMER,SHIP_TO_CUSTOMER,SOLD_TO_CUSTOMER 
                     FROM $table_vnso WHERE ORDER_NUMBER = '$SO_LINE'  ";

        $result = mysqli_query($conn, $query);
        $num = mysqli_num_rows($result);
        $result_vnso = mysqli_fetch_array($result);

        if ($num > 0) {
            $flag = "vnso";
        } else { //Nếu không có trong vnso thì truy cập đến vnso_total

            $query = "SELECT ID,ORDER_NUMBER,LINE_NUMBER,QTY,ITEM,PROMISE_DATE,REQUEST_DATE,ORDERED_DATE,CS,PACKING_INSTRUCTIONS,
                                 ORDER_TYPE_NAME,BILL_TO_CUSTOMER,SHIP_TO_CUSTOMER,SOLD_TO_CUSTOMER
                          FROM  $table_vnso_total WHERE ORDER_NUMBER = '$SO_LINE' ";
            $result = mysqli_query($conn, $query);
            $num = mysqli_num_rows($result);
            $result_vnso = mysqli_fetch_array($result);

            if ($num > 0) {
                $flag = "vnso_total";
            } else    $flag = "";
        }
    } else { //else $count_check = 2
        $query = "SELECT ID,ORDER_NUMBER,LINE_NUMBER,QTY,ITEM,PROMISE_DATE,REQUEST_DATE,ORDERED_DATE,CS,PACKING_INSTRUCTIONS,
                             ORDER_TYPE_NAME,BILL_TO_CUSTOMER,SHIP_TO_CUSTOMER,SOLD_TO_CUSTOMER 
                      FROM $table_vnso WHERE ORDER_NUMBER = '$val_SO'  AND LINE_NUMBER = '$val_LINE' ";
        $result = mysqli_query($conn, $query);
        $num = mysqli_num_rows($result);
        $result_vnso = mysqli_fetch_array($result);

        if ($num > 0) {
            $flag = "vnso";
        } else { //Nếu không có trong vnso thì truy cập đến vnso_total

            $query = "SELECT ID,ORDER_NUMBER,LINE_NUMBER,QTY,ITEM,PROMISE_DATE,REQUEST_DATE,ORDERED_DATE,CS,PACKING_INSTRUCTIONS,
                                 ORDER_TYPE_NAME,BILL_TO_CUSTOMER,SHIP_TO_CUSTOMER,SOLD_TO_CUSTOMER
                          FROM $table_vnso_total WHERE ORDER_NUMBER = '$val_SO'  AND LINE_NUMBER = '$val_LINE' ";
            $result = mysqli_query($conn, $query);
            $num = mysqli_num_rows($result);
            $result_vnso = mysqli_fetch_array($result);

            if ($num > 0) {
                $flag = "vnso_total";
            } else $flag = "";
        }
    } //end else $count = 2

    if ($num > 0) {
        $data = array(
            'status_vnso'    => true,
            'result_vnso' => $result_vnso
        );
    } else {
        $data = array(
            'status_vnso'    => false,
            'flag'          => $flag
        );
    }

    if ($conn) mysqli_close($conn);

    return $data;
} //2. end checkSOLINE     

function clean($string)
{
    // $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
    preg_match('/\d{8}/', $string, $matches, PREG_OFFSET_CAPTURE);
    $S = "";
    foreach ($matches as $R) {
        $S = $S . $R[0];
    }
    return $S;
}


//@3: get SAMPLE: OK
function getSample($SO_LINE, $ORACLE_ITEM)
{
    //connect 
    $conn = getConnection138();
    $table      = 'vnso';

    $detached = detachedSOLINE($SO_LINE);
    extract($detached);

    $SAMPLE = 0; //mặc định đơn không mẫu

    $query = "SELECT ID FROM $table WHERE  PACKING_INSTRUCTIONS LIKE '%$val_SO%' "; //ITEM = '$ORACLE_ITEM' AND LINE_NUMBER = '$val_LINE' AND
    $result = mysqli_query($conn, $query);
    $num = mysqli_num_rows($result);
    if ($num > 0) {
        //Nếu có thì SO# này là Đơn có mẫu
        $SAMPLE = 1;
    } else {
        //Không có thì kiểm tra xem tại SO# này trong PACKING_INSTRUCTIONS có tồn tại SO không, 
        //nếu có thì nó là đơn mẫu, ngược lại trả về đơn không mẫu (điều kiện này đã ghi trong mail)
        if ($count_check == 2) { //nguoi dung nhap so-line
            $query = "SELECT PACKING_INSTRUCTIONS FROM $table WHERE ORDER_NUMBER = '$val_SO' AND LINE_NUMBER = '$val_LINE' ";
        } else { //count_check = 1 nguoi dung nhap SO, khong LINE
            $query = "SELECT PACKING_INSTRUCTIONS FROM $table WHERE ORDER_NUMBER = '$val_SO' ";
        }

        $result = mysqli_query($conn, $query);
        $num = mysqli_num_rows($result);
        if ($num > 0) {
            $result = mysqli_fetch_array($result);
            $PACKING_INSTRUCTIONS = $result['PACKING_INSTRUCTIONS'];
            $check_1 = strpos($PACKING_INSTRUCTIONS, 'DAY LA SO# MAU CUA SO#');
            $check_2 = strpos($PACKING_INSTRUCTIONS, 'DAY la SO# sample cua SO#');
            $check_3 = strpos($PACKING_INSTRUCTIONS, 'SAMPLE CUA SO#');
            $check = clean($PACKING_INSTRUCTIONS); //kiem tra co chuoi 8 chu so lien tiep khong.
            //Neu co thi sample = 2; don mau
            if ($check_1 !== false || $check_2 !== false || $check_3 !== false || !empty($check)) {
                $SAMPLE = 2; //Đơn mẫu set 2 cho phù hợp với init FORM
            }
        } else {
            $SAMPLE = 0; //ĐƠn không mẫu.
        }
    }

    if ($conn) mysqli_close($conn);

    return $SAMPLE;
} //end 3

//@4: filter SAMPLE:
function filterSample($SO_LINE, $ORACLE_ITEM)
{
    //connect 
    $conn = getConnection138();
    $table = 'vnso';

    $detached = detachedSOLINE($SO_LINE);
    extract($detached);

    $SOLINE_SAMPLE = ''; //Mặc định mang  SO sample rong

    $query = "SELECT DISTINCT ORDER_NUMBER,LINE_NUMBER FROM $table WHERE  PACKING_INSTRUCTIONS LIKE '%$val_SO%' "; //ITEM = '$ORACLE_ITEM' AND LINE_NUMBER = '$val_LINE' AND
    $result = mysqli_query($conn, $query);
    $num = mysqli_num_rows($result);
    $count = 0;
    if ($num > 0) {
        //Nếu có thì SO# này là Đơn có mẫu, nên lọc ra hết tất cả các line bắt được, tất cả trường hợp này là mẫu của SO đang làm lệnh
        while ($row = mysqli_fetch_array($result)) {
            $ORDER_NUMBER = $row['ORDER_NUMBER'];
            $LINE_NUMBER = $row['LINE_NUMBER'];
            break;
        }
    }

    if ($num > 1) {
        $SOLINE_SAMPLE = $ORDER_NUMBER . '-1-' . $num; //implode(',',$SOLINE_SAMPLE_ARR);
    } else if ($num == 0) {
        $SOLINE_SAMPLE = $ORDER_NUMBER . '-' . $LINE_NUMBER; //implode(',',$SOLINE_SAMPLE_ARR);
    }

    if ($conn) mysqli_close($conn);

    return $SOLINE_SAMPLE;
} //end 4


//@5: 
function getTOTAL_QTY($SO_LINE)
{
    //connect 
    $conn = getConnection138();
    $table_vnso = 'vnso';
    $table_vnso_total = 'vnso_total';

    //$SO_LINE = '25445912';
    $detached = detachedSOLINE($SO_LINE);
    extract($detached);

    if ($len_LINE < 1) { //len_LINE from detachedSOLINE(): len LINE. Avery.vnso 
        $flag_line = 0;
        $sql_TOTAL_QTY = "SELECT  ORDER_NUMBER,LINE_NUMBER,QTY FROM $table_vnso WHERE ORDER_NUMBER='$val_SO'; ";
    } else {
        $flag_line = 1;
        $sql_TOTAL_QTY = "SELECT  ORDER_NUMBER,LINE_NUMBER,QTY FROM $table_vnso WHERE ORDER_NUMBER='$val_SO' AND LINE_NUMBER='$val_LINE'; ";
    }
    $result = mysqli_query($conn, $sql_TOTAL_QTY);
    $num = mysqli_num_rows($result);
    $RFID_CB_CHECK = [];
    if ($num > 0) {

        $QTY_TOTAL = 0;
        $COUNT_SO_LINE = 0;

        $tmp_Line1 = '';
        $tmp_Line2 = '';
        $value_qty = mysqli_fetch_assoc($result);

        if ($flag_line == 0) { //Xử lý các SOLINE trùng nhau trong vnso

            while ($value_qty = mysqli_fetch_assoc($result)) {

                $tmp_Line1 = $value_qty['LINE_NUMBER'];
                if ($tmp_Line1 == $tmp_Line2) { //trùng thì không cộng
                    continue; //không làm gì cả, next
                } else {
                    $COUNT_SO_LINE += 1;
                    $QTY_TOTAL += $value_qty['QTY'];
                    $SO_LINE_TMP = $value_qty['ORDER_NUMBER'] . "-" . $value_qty['LINE_NUMBER'];
                    $RFID_CB_CHECK[] = [
                        'SO_LINE_CB' => $SO_LINE_TMP
                    ];
                }
                $tmp_Line2 = $tmp_Line1;
            } //while

        } else { //trường hợp chỉ có 1 line, không sử dụng vòng lặp
            $COUNT_SO_LINE += 1;
            $QTY_TOTAL += $value_qty['QTY'];
            $SO_LINE_TMP = $SO_LINE; //input
            $RFID_CB_CHECK[] = [
                'SO_LINE_CB' => $SO_LINE_TMP
            ];
        }

        //Tạm thời set $COUNT_SO_LINE = $num //do đã xóa trùng

        $COUNT_SO_LINE = $num;

        $data = array(
            "status_TOTAL_QTY"  => true,
            "COUNT_SO_LINE"     => $COUNT_SO_LINE,
            "QTY_TOTAL"               => $QTY_TOTAL,
            "RFID_CB_CHECK"     => $RFID_CB_CHECK
        );

        //echo json_encode ($data);

    } else {
        if ($len_LINE < 1) { //len_LINE from detachedSOLINE(): len LINE. Avery.vnso_total
            $flag_line = 0;
            $sql_TOTAL_QTY = "SELECT  ORDER_NUMBER,LINE_NUMBER,QTY FROM $table_vnso_total WHERE ORDER_NUMBER='$val_SO'; ";
        } else {
            $flag_line = 1;
            $sql_TOTAL_QTY = "SELECT  ORDER_NUMBER,LINE_NUMBER,QTY FROM $table_vnso_total WHERE ORDER_NUMBER='$val_SO' AND LINE_NUMBER='$val_LINE'; ";
        }

        $result = mysqli_query($conn, $sql_TOTAL_QTY);
        $num = mysqli_num_rows($result);
        //$RESULT_TOTAL_QTY = mysqli_fetch_assoc($result);

        $QTY_TOTAL = 0;
        $COUNT_SO_LINE = 0;

        $tmp_Line1 = '';
        $tmp_Line2 = '';
        $value_qty = mysqli_fetch_assoc($result);
        if ($num > 0) {
            if ($flag_line == 0) { //Xử lý các SOLINE trùng nhau trong vnso

                while ($value_qty = mysqli_fetch_assoc($result)) {
                    $tmp_Line1 = $value_qty['LINE_NUMBER'];

                    if ($tmp_Line1 == $tmp_Line2) { //trùng thì không cộng
                        continue; //không làm gì cả, next
                    } else {
                        $COUNT_SO_LINE += 1;
                        $QTY_TOTAL += $value_qty['QTY'];
                        $SO_LINE_TMP = $value_qty['ORDER_NUMBER'] . "-" . $value_qty['LINE_NUMBER'];
                        $RFID_CB_CHECK[] = [
                            'SO_LINE_CB' => $SO_LINE_TMP
                        ];
                    }

                    $tmp_Line2 = $tmp_Line1;
                } //while
            } else { //trường hợp chỉ có 1 line, không sử dụng vòng lặp
                $COUNT_SO_LINE += 1;
                $QTY_TOTAL += $value_qty['QTY'];
                $SO_LINE_TMP = $SO_LINE; //input
                $RFID_CB_CHECK[] = [
                    'SO_LINE_CB' => $SO_LINE_TMP
                ];
            }

            //Tạm thời 
            $COUNT_SO_LINE = $num;
            $data = array(
                "status_TOTAL_QTY"  => true,
                "COUNT_SO_LINE"     => $COUNT_SO_LINE,
                "QTY_TOTAL"         => $QTY_TOTAL,
                "RFID_CB_CHECK"     => $RFID_CB_CHECK
            );
            //echo json_encode($data);die;

        } else {
            $data = array(
                "status_TOTAL_QTY"  => false,
                "COUNT_SO_LINE"     => $COUNT_SO_LINE,
                "QTY_TOTAL"               => $QTY_TOTAL,
                "RFID_CB_CHECK"     => $RFID_CB_CHECK
            );
            //echo json_encode( $data ); die;
        }
    }

    if ($conn) mysqli_close($conn);

    return $data;
} //end 5

//@6 packing instruction
function getPACKING_INSTRUCTIONS($order, $line = "")
{
    $conn = getConnection138();
    $table_vnso = "vnso";
    $table_vnso_total = "vnso_total";

    $whereSOLIne = " ORDER_NUMBER= '$order' AND LINE_NUMBER='$line' ORDER BY CREATEDDATE DESC; ";
    $whereSO = " ORDER_NUMBER= '$order' ORDER BY LENGTH(LINE_NUMBER),LINE_NUMBER ASC ;";

    $PACKING_INSTRUCTIONS = "";

    $query = !empty($line) ? "SELECT PACKING_INSTRUCTIONS FROM $table_vnso WHERE  $whereSOLIne " : "SELECT PACKING_INSTRUCTIONS FROM $table_vnso WHERE  $whereSO ";
    $result = mysqli_query($conn, $query);
    if ($result === FALSE) {
        die(mysql_error());
    }
    if (mysqli_num_rows($result) < 1) {
        $query = !empty($line) ? "SELECT PACKING_INSTRUCTIONS FROM $table_vnso_total WHERE  $whereSOLIne " : "SELECT PACKING_INSTRUCTIONS FROM $table_vnso_total WHERE  $whereSO ";
        $result = mysqli_query($conn, $query);
    }

    if (mysqli_num_rows($result) > 0) {
        $result = mysqli_fetch_array($result);
        $PACKING_INSTRUCTIONS = !empty($result) ? trim($result["PACKING_INSTRUCTIONS"]) : '';
    }

    if ($conn) mysqli_close($conn);

    return $PACKING_INSTRUCTIONS;
}

//@7 packing instruction and attachment
function getPACKING_INSTRUCTIONS_ATTACHMENT($order, $line = "")
{
    $conn = getConnection138();
    $table_vnso = "vnso";
    $table_vnso_total = "vnso_total";

    $fields = " CONCAT(VIRABLE_BREAKDOWN_INSTRUCTIONS,PACKING_INSTRUCTIONS) AS PACKING_INSTRUCTIONS ";
    $whereSOLIne = " ORDER_NUMBER= '$order' AND LINE_NUMBER='$line' ORDER BY CREATEDDATE DESC; ";
    $whereSO = " ORDER_NUMBER= '$order' ORDER BY LENGTH(LINE_NUMBER),LINE_NUMBER ASC ;";

    $PACKING_INSTRUCTIONS = "";

    $query = !empty($line) ? "SELECT $fields FROM $table_vnso WHERE  $whereSOLIne " : "SELECT $fields FROM $table_vnso WHERE  $whereSO ";
    $result = mysqli_query($conn, $query);
    if ($result === FALSE) {
        die(mysql_error());
    }
    if (mysqli_num_rows($result) < 1) {
        $query = !empty($line) ? "SELECT $fields FROM $table_vnso_total WHERE  $whereSOLIne " : "SELECT $fields FROM $table_vnso_total WHERE  $whereSO ";
        $result = mysqli_query($conn, $query);
    }

    if (mysqli_num_rows($result) > 0) {
        $result = mysqli_fetch_array($result);
        $PACKING_INSTRUCTIONS = !empty($result) ? trim($result["PACKING_INSTRUCTIONS"]) : '';
    }

    if ($conn) mysqli_close($conn);

    return $PACKING_INSTRUCTIONS;
}

//@6 packing instruction
function getColAutomail($ORDER_NUMBER, $LINE_NUMBER, $colAutomail)
{
    $conn = getConnection138();
    $table_vnso = "vnso";
    $table_vnso_total = "vnso_total";

    $colAutomailOk = "";

    $whereSOLine = " ORDER_NUMBER= '$ORDER_NUMBER' AND LINE_NUMBER='$LINE_NUMBER' ORDER BY CREATEDDATE DESC; ";
    $whereSO = " ORDER_NUMBER= '$ORDER_NUMBER' ORDER BY LENGTH(LINE_NUMBER), LINE_NUMBER DESC  LIMIT 1; ";

    $query = !empty($LINE_NUMBER) ? "SELECT * FROM $table_vnso WHERE $whereSOLine " : "SELECT * FROM $table_vnso WHERE $whereSO ";
    $result = mysqli_query($conn, $query);
    if ($result === FALSE) {
        die(mysql_error());
    }
    if (mysqli_num_rows($result) < 1) {
        $query = !empty($LINE_NUMBER) ? "SELECT * FROM $table_vnso_total WHERE $whereSOLine " : "SELECT * FROM $table_vnso_total WHERE $whereSO ";
        $result = mysqli_query($conn, $query);
    }

    if (mysqli_num_rows($result) > 0) {
        $result = mysqli_fetch_array($result);
        $colAutomailOk = !empty($result) ? trim($result[$colAutomail]) : '';
    }

    if ($conn) mysqli_close($conn);
    
    return $colAutomailOk;
}

