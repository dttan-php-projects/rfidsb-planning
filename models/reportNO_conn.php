<?php
    date_default_timezone_set('Asia/Ho_Chi_Minh'); ini_set('max_execution_time',3000);  // set time 50 minutes
    if(!isset($_COOKIE["VNRISIntranet"])) {header('Location: login.php');} 
    else {$USER_CUR = $_COOKIE["VNRISIntranet"];}

    require_once ( "../define_constant_system.php");
    require_once (PATH_MODEL . "/__connection.php");
    //connect host

    $table      = "rfid_po_save";
    $table2      = "user_rfid";

    $table_so = "rfid_po_soline_save";
    $table_ink = "rfid_po_ink_no_cbs_save";
    $table_mn = "rfid_po_material_no_cbs_save";
    $table_mi = "rfid_po_material_ink_save";
    $table_s = "rfid_po_size_cbs_save";
    $table_m = "rfid_po_material_cbs_save";
    $table_no_cbs = "no_cbs";

    $conn       = getConnection(); 

    //get data 
    $FORM_TYPE = $_GET['form_type'];
    $FROM_DATE = $_GET['from_date_value'];
    $FROM_DATE = date('Y-m-d',strtotime($FROM_DATE));
    $TO_DATE = $_GET['to_date_value'];
    $TO_DATE = date('Y-m-d',strtotime($TO_DATE));
    if ( empty($FROM_DATE) || empty($TO_DATE) ) {
        echo "Bạn chưa chọn ngày export";
        return false;
    }
    
    function formatDate($value){
        return date('d-M-y',strtotime($value));
    }

    function formatTime($value){
        return date('H:m:s',strtotime($value));
    }

    //csv
    $filename = "RFID_Report_".date("d_m_Y__H_i_s");
    header('Content-Encoding: UTF-8');
    header('Content-Type: text/csv; charset=UTF-8');  
    //header("Content-type: text/csv");
    header("Cache-Control: no-store, no-cache");
    header("Content-Disposition: attachment; filename=$filename.csv");
    //Đoạn code fix lỗi tiếng việt
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');
    echo "\xEF\xBB\xBF"; // UTF-8 BOM

    $output = fopen("php://output", "w");
    $header = [
	    "FORM TYPE","NGAY LAM DON HANG","STT PLANNING","SO#","PROMISE DATE","REQUEST DATE","MA HANG - ITEM CODE","RBO","NHAN - ORDER ITEM","SO LUONG CON NHAN QTY - PCS","MA VAT TU - ORACLE MATERIAL"," Description - MATERIAL"," SO LUONG VAT TU CAN - QTY-EA"," SO LUONG VAT TU CAN - QTY-YD"," SO LUONG VAT TU CAN - QTY-MT"," Kich thuoc nhan chieu dai (DVT:mm)"," Kich thuoc nhan chieu rong (DVT: mm)"," MA MUC - ORACLE MATERIAL ","Description - MUC","SO LUONG MUC CAN - QTY-MT","GHI CHU/SO GPM","Notes item","SO SIZE","Note","Releaseddate to PPC","Urgent orderto PPC","ReceivedPPC","URGENT","Remarks","CREATED BY","Gio Lam Lenh"//Bỏ cột "SO UP","CS NAME", ,"SIZE (CBS)","LABEL ITEM (CBS)","BASE ROLL (CBS)","SO LUONG (CBS)" @Duyen yêu cầu
    ];

    /*GET DATA ALL*/
    //1. get data form save
    $fields     = 'PO_NO, PO_SO_LINE, PO_FORM_TYPE,PO_INTERNAL_ITEM,PO_ORDER_ITEM, PO_GPM, PO_RBO, PO_SHIP_TO_CUSTOMER, PO_CS,PO_QTY, PO_LABEL_SIZE, PO_MATERIAL_CODE, PO_MATERIAL_DES, PO_MATERIAL_QTY, PO_INK_CODE, PO_INK_DES, PO_INK_QTY, PO_COUNT_SO_LINE, PO_SAVE_DATE, PO_PROMISE_DATE, PO_REQUEST_DATE, PO_ORDERED_DATE, PO_MAIN_SAMPLE_LINE, PO_SAMPLE, PO_SAMPLE_15PCS, PO_MATERIAL_REMARK, PO_INK_REMARK, PO_CREATED_BY, PO_REMARK_1,PO_REMARK_2,PO_REMARK_3,PO_REMARK_4,PO_DATE_RECEIVED, PO_FILE_DATE_RECEIVED,PO_CREATED_TIME';
    
    /* TRƯỜNG HỢP MUỐN EXPORT TỪNG FORM THÌ SỬ DỤNG*/
    // if (!empty($FORM_TYPE) ) {
    //     $query_form   = "SELECT $fields FROM $table WHERE (PO_CREATED_TIME>='$FROM_DATE' AND PO_CREATED_TIME<='$TO_DATE') AND PO_FORM_TYPE = '$FORM_TYPE' order by PO_CREATED_TIME ASC";
    // } else {//report all
    //     $query_form   = "SELECT $fields FROM $table WHERE (PO_CREATED_TIME>='$FROM_DATE' AND PO_CREATED_TIME<='$TO_DATE')  ORDER BY PO_CREATED_TIME ASC";
    // }

    $query_form     = "SELECT $fields FROM $table WHERE (PO_SAVE_DATE>='$FROM_DATE' AND PO_SAVE_DATE<='$TO_DATE') ORDER BY PO_NO, PO_CREATED_TIME DESC";
    $result_form    = mysqli_query($conn, $query_form);
	if($result_form === FALSE) { die(mysql_error()); }
    $num_form       = mysqli_num_rows($result_form);
    //echo "Num form: ".$num_form;

    fputcsv($output, $header); 
    if ($num_form > 0 ) {
        $result_form    = mysqli_fetch_all($result_form, MYSQLI_ASSOC);//sử dụng hàm này cho truy vấn data (không dùng hàm khác)
        foreach ($result_form as $key => $rows_form) {
            //reset lại data save vào file, tránh trường hợp ghi trùng
            $CREATED_DATE=$FORM_NO=$SO_LINE=$PO_REQUEST_DATE=$PO_PROMISE_DATE=$SO_INTERNAL_ITEM=$PO_RBO=$SO_ORDER_ITEM=$SO_PO_QTY=$SO_PO_QTY=$MATERIAL_CODE=$MATERIAL_DES=$MATERIAL_QTY=$SO_WIDTH=$SO_HEIGHT=$INK_CODE=$INK_DES=$INK_QTY=$PO_COUNT_SO_LINE=$PO_CS=$CREATED_BY=$SIZE=$LABEL_ITEM=$BASEROLL=$S_QTY='';

            //1. get and set data from FORM to export
            $PO_NO                  = $rows_form['PO_NO'];
            $PO_SO_LINE             = $rows_form['PO_SO_LINE'];
            $PO_FORM_TYPE           = $rows_form['PO_FORM_TYPE'];
            $PO_INTERNAL_ITEM       = $rows_form['PO_INTERNAL_ITEM'];
            $PO_ORDER_ITEM          = $rows_form['PO_ORDER_ITEM'];
            $PO_GPM                 = $rows_form['PO_GPM'];
            $PO_RBO                 = $rows_form['PO_RBO'];
            
            $PO_RBO                 = html_entity_decode($PO_RBO);
            // &#-39;
            if (strpos($PO_RBO, "&#039;") !== false ) {
                $PO_RBO             = str_replace("&#039;","'",$PO_RBO);
            } else if (strpos($PO_RBO, '&amp;') !== false ) {
                $PO_RBO                 = str_replace('&amp;','&',$PO_RBO);//chuyển &amp; thành &
            }
            
            $PO_SHIP_TO_CUSTOMER    = $rows_form['PO_SHIP_TO_CUSTOMER'];
            $PO_CS                  = $rows_form['PO_CS'];
            $PO_QTY                 = $rows_form['PO_QTY']; //int
            $PO_LABEL_SIZE          = $rows_form['PO_LABEL_SIZE'];
            $PO_MATERIAL_CODE       = $rows_form['PO_MATERIAL_CODE'];
            $PO_MATERIAL_DES        = $rows_form['PO_MATERIAL_DES'];
            $PO_MATERIAL_QTY        = $rows_form['PO_MATERIAL_QTY']; //int
            $PO_INK_CODE            = $rows_form['PO_INK_CODE'];
            $PO_INK_DES             = $rows_form['PO_INK_DES'];
            $PO_INK_QTY             = $rows_form['PO_INK_QTY']; //int
            $PO_COUNT_SO_LINE       = $rows_form['PO_COUNT_SO_LINE'];

            $PO_SAVE_DATE           = formatDate($rows_form['PO_SAVE_DATE']);
            $PO_PROMISE_DATE        = formatDate($rows_form['PO_PROMISE_DATE']);
            $PO_REQUEST_DATE        = formatDate($rows_form['PO_REQUEST_DATE']);
            $PO_ORDERED_DATE        = formatDate($rows_form['PO_ORDERED_DATE']);

            $PO_MAIN_SAMPLE_LINE    = $rows_form['PO_MAIN_SAMPLE_LINE'];
            $PO_SAMPLE              = $rows_form['PO_SAMPLE']; //int
            $PO_SAMPLE_15PCS        = $rows_form['PO_SAMPLE_15PCS'];
            $PO_MATERIAL_REMARK     = $rows_form['PO_MATERIAL_REMARK'];
            $PO_INK_REMARK          = $rows_form['PO_INK_REMARK'];

            $PO_REMARK_1            = $rows_form['PO_REMARK_1'];
            $PO_REMARK_2            = $rows_form['PO_REMARK_2'];
            $PO_REMARK_3            = $rows_form['PO_REMARK_3'];
            $PO_REMARK_4            = $rows_form['PO_REMARK_4'];
            $PO_DATE_RECEIVED       = $rows_form['PO_DATE_RECEIVED'];
            
            $PO_CREATED_TIME       = $rows_form['PO_CREATED_TIME'];
            $PO_CREATED_BY       = $rows_form['PO_CREATED_BY'];

            $PO_ORDER_TYPE_NAME     = !empty($rows_form['PO_ORDER_TYPE_NAME'])?trim($rows_form['PO_ORDER_TYPE_NAME']):"";

            if ($PO_DATE_RECEIVED == '1970-01-01') {
                $PO_DATE_RECEIVED = '';
                $PO_FILE_DATE_RECEIVED = '';
            }
            
            $PO_GPM                 = $rows_form['PO_GPM'];
            $GHI_CHU_SO_GPM         = '';
            if ( !empty($PO_DATE_RECEIVED) ) {
                if ( !empty($PO_GPM) ) {
                        $GHI_CHU_SO_GPM = $PO_DATE_RECEIVED.'/'.$PO_GPM;
                } else  $GHI_CHU_SO_GPM = $PO_DATE_RECEIVED;
            } else {
                if ( !empty($PO_GPM) ) {
                        $GHI_CHU_SO_GPM = $PO_GPM;
                } else  $GHI_CHU_SO_GPM = '';
            }
            
            $PO_FILE_DATE_RECEIVED  = $rows_form['PO_FILE_DATE_RECEIVED'];
            if ($PO_FILE_DATE_RECEIVED == 1) {
                $NOTE_ITEM_1 = 'file 1';
            } else if ($PO_FILE_DATE_RECEIVED == 2) {
                $NOTE_ITEM_1 = 'file 2&3';
            } else if ($PO_FILE_DATE_RECEIVED == 4) {
                $NOTE_ITEM_1 = 'file 4';
            } else {
                $NOTE_ITEM_1 = '';
            }

            $query_ghi_chu_item = "SELECT ghi_chu_item FROM $table_no_cbs WHERE internal_item = '$PO_INTERNAL_ITEM' LIMIT 0,1 ";
            $result_ghi_chu_item    = mysqli_query($conn, $query_ghi_chu_item);
            if($result_ghi_chu_item === FALSE) { die(mysql_error()); }
            if ( mysqli_num_rows($result_ghi_chu_item)>0 ) {
                $result_ghi_chu_item    = mysqli_fetch_array($result_ghi_chu_item);
                $NOTE_ITEM_2     = $result_ghi_chu_item['ghi_chu_item'];
            } else {
                $NOTE_ITEM_2 = '';
            }

            if ( !empty($NOTE_ITEM_1) && !empty($NOTE_ITEM_2) ) {
                $NOTE_ITEM = $NOTE_ITEM_1.' & '.$NOTE_ITEM_2;
            } else if ( !empty($NOTE_ITEM_1) ) {
                $NOTE_ITEM = $NOTE_ITEM_1;
            } else if ( !empty($NOTE_ITEM_2) ) {
                $NOTE_ITEM = $NOTE_ITEM_2;
            } else {
                $NOTE_ITEM = '';
            }

            //get PO_NO từ getPO_NO_FI_conn.php (xử lý hiển thị PO_NO)
            $FR_SHOW = ''; //mac dinh
            $PO_NO_FI = $PO_NO;
            include_once ("getPO_NO_FI_conn.php");
            $PO_NO_FI = getPO_NO_FI($PO_NO);

            //I. FORM NO CBS

            /* ****** Tính tổng số lượng con nhãn trường hợp internal item =  CB1627627B(VN) */
                //Phần tính tổng số lượng: con nhãn CB
                $query_count_so   = "SELECT COUNT(*) as COUNT_LINE FROM $table_so WHERE SO_PO_NO = '$PO_NO' ";
                $result_count_so = mysqli_query($conn, $query_count_so);
                if($result_count_so === FALSE) { die(mysql_error()); }
                $result_count_so = mysqli_fetch_array($result_count_so);
                $COUNT_LINE_SHOW = $result_count_so["COUNT_LINE"];

                $CB_SO_TOTAL = $PO_SO_LINE . "-" . $COUNT_LINE_SHOW;

                //Phần tính tổng số lượng: con nhãn CB
                $query_sum_so   = "SELECT SUM(SO_PO_QTY) as TOTAL FROM $table_so WHERE SO_PO_NO = '$PO_NO' ";
                $result_sum_so = mysqli_query($conn, $query_sum_so);
                if($result_sum_so === FALSE) { die(mysql_error()); }
                $result_sum_so = mysqli_fetch_array($result_sum_so);
                $QTY_TOTAL = $result_sum_so["TOTAL"];

                //Phần tính tổng số lượng: material qty CB
                $query_sum_mn   = "SELECT SUM(MN_MATERIAL_QTY) as TOTAL FROM $table_mn WHERE MN_PO_NO = '$PO_NO' ";
                $result_sum_mn = mysqli_query($conn, $query_sum_mn);
                if($result_sum_mn === FALSE) { die(mysql_error()); }
                $result_sum_mn = mysqli_fetch_array($result_sum_mn);
                $MATERIAL_QTY_TOTAL = $result_sum_mn["TOTAL"];

                //Phần tính tổng số lượng: ink qty CB
                $query_sum_ink   = "SELECT SUM(INK_QTY) as TOTAL FROM $table_ink WHERE INK_PO_NO = '$PO_NO' ";
                $result_sum_ink = mysqli_query($conn, $query_sum_ink);
                if($result_sum_ink === FALSE) { die(mysql_error()); }
                $result_sum_ink = mysqli_fetch_array($result_sum_ink);
                $INK_QTY_TOTAL = $result_sum_ink["TOTAL"];


            //ink  WHERE INK_PO_NO = '$PO_NO'
            $fields_ink = "INK_PO_NO, INK_PO_SO_LINE, INK_PO_FORM_TYPE, INK_CODE, INK_QTY, INK_DES, INK_PO_CREATED_BY, INK_CREATED_DATE ";
            $query_ink   = "SELECT $fields_ink FROM $table_ink WHERE INK_PO_NO = '$PO_NO' ORDER BY INK_CREATED_DATE ASC";
            $result_ink = mysqli_query($conn, $query_ink);
            if($result_ink === FALSE) { die(mysql_error()); }
            $num_ink = mysqli_num_rows($result_ink);
            // echo "num ink ".$num_ink." ";
            if ($num_ink > 0) {
                $result_ink    = mysqli_fetch_all($result_ink, MYSQLI_ASSOC);
                //load all data: Lấy ink làm chuẩn, tất cả tìm dựa theo điều kiện ink
                $checkCB = 0;
                foreach ($result_ink as $key => $rows_ink) {
                    //ink
                    $FORM_NO           = $rows_ink['INK_PO_NO'];
                    $INK_PO_SO_LINE    = $rows_ink['INK_PO_SO_LINE']; 
                    $SO_LINE_CHECK     = $INK_PO_SO_LINE;
                    $INK_PO_FORM_TYPE  = $rows_ink['INK_PO_FORM_TYPE'];
                    $INK_CODE          = $rows_ink['INK_CODE'];
                    $INK_QTY           = $rows_ink['INK_QTY'];
                    $INK_DES           = $rows_ink['INK_DES'];
                    $CREATED_BY        = $rows_ink['INK_PO_CREATED_BY'];
                    //$CREATED_DATE      = formatDate($rows_ink['INK_CREATED_DATE']);//PO_SAVE_DATE
                    $CREATED_DATE      = formatDate( $PO_SAVE_DATE );
                    $CREATE_TIME       = formatTime($rows_ink['INK_CREATED_DATE']);

                    //material no cbs     LOAD material dựa theo ink_po_no và ink_soline       
                    $fields_mn = "MN_PO_NO, MN_PO_SO_LINE, MN_PO_FORM_TYPE, MN_MATERIAL_CODE, MN_MATERIAL_QTY, MN_MATERIAL_DES, MN_PO_CREATED_BY, MN_CREATED_DATE ";
                    $query_mn   = "SELECT $fields_mn FROM $table_mn WHERE MN_PO_NO = '$FORM_NO' AND MN_PO_SO_LINE = '$SO_LINE_CHECK' ORDER BY MN_CREATED_DATE ASC";
                    $result_mn = mysqli_query($conn, $query_mn);
                    if($result_mn === FALSE) { die(mysql_error()); }
                    $num_mn = mysqli_num_rows($result_mn);
                    //echo "Num mn: ".$num_mn;
                    if ($num_mn >0) {
                        //mn
                        $row_mn             = mysqli_fetch_array($result_mn);
                        $MN_PO_NO           = $row_mn['MN_PO_NO'];
                        $MN_PO_SO_LINE      = $row_mn['MN_PO_SO_LINE'];
                        $MN_PO_FORM_TYPE    = $row_mn['MN_PO_FORM_TYPE'];
                        $MATERIAL_CODE      = $row_mn['MN_MATERIAL_CODE'];
                        $MATERIAL_QTY       = $row_mn['MN_MATERIAL_QTY'];
                        $MATERIAL_DES       = $row_mn['MN_MATERIAL_DES'];
                        if (strpos($MATERIAL_DES,'&amp;')!==false) {
                            $MATERIAL_DES = str_replace('&amp;',' & ', $MATERIAL_DES);
                        }
                        $MN_PO_CREATED_BY   = $row_mn['MN_PO_CREATED_BY'];
                        $MN_CREATED_DATE    = $row_mn['MN_CREATED_DATE'];
                        
                    }//end if mn

                    //2. get and set data from SOLINE save
                    $fields_so = "SO_PO_NO, SO_LINE, SO_PO_QTY, SO_INTERNAL_ITEM, SO_ORDER_ITEM, SO_WIDTH, SO_HEIGHT, SO_CREATED_DATE, GPM, REMARK_SO_COMBINE ";
                    $query_so   = "SELECT $fields_so FROM $table_so WHERE SO_PO_NO = '$FORM_NO' AND SO_LINE = '$SO_LINE_CHECK' ORDER BY SO_CREATED_DATE ASC";
                    $result_so = mysqli_query($conn, $query_so);
                    if($result_so === FALSE) { die(mysql_error()); }
                    $num_so = mysqli_num_rows($result_so);
                    if ($num_so > 0) {
                        $row_so = mysqli_fetch_array($result_so);//sử dụng hàm này cho truy vấn data (không dùng hàm khác)
                        $SO_PO_NO           = $row_so['SO_PO_NO'];
                        $SO_LINE            = $row_so['SO_LINE'];
                        $SO_PO_QTY          = $row_so['SO_PO_QTY'];
                        $SO_PO_QTY          = (int)$SO_PO_QTY;
                        $SO_INTERNAL_ITEM   = $row_so['SO_INTERNAL_ITEM'];
                        $SO_ORDER_ITEM      = $row_so['SO_ORDER_ITEM'];
                        $SO_WIDTH           = $row_so['SO_WIDTH'];
                        $SO_WIDTH           = (float)$SO_WIDTH;
                        $SO_HEIGHT          = $row_so['SO_HEIGHT'];
                        $SO_HEIGHT           = (float)$SO_HEIGHT;
                        $GPM          = trim($row_so['GPM']);
                        $REMARK_SO_COMBINE          = trim($row_so['REMARK_SO_COMBINE']);

                        
                        if (!empty($REMARK_SO_COMBINE) ) {
                            $rapArr = array('Rap voi thermal SO#:', 'Rap voi thermal SO#: ');
                            foreach ($rapArr as $rap ) {
                                $REMARK_SO_COMBINE = (stripos($REMARK_SO_COMBINE, $rap) !== false ) ? str_replace($rap, '', $REMARK_SO_COMBINE) : $REMARK_SO_COMBINE;
                                
                            }

                            $NOTE_ITEM = $REMARK_SO_COMBINE;
                        } else {
                            if (!empty($PO_REMARK_2) ) {
                                $rapArr = array('Rap voi thermal', 'Rap voi thermal SO#: ');
                                foreach ($rapArr as $rap ) {
                                    if (stripos($PO_REMARK_2, $rap) !== false ) {
                                        
                                        $NOTE_ITEM = str_replace($rap, '', $PO_REMARK_2);
                                        break;
                                    }
                                    
                                }
                            }
                            
                        }
                        $NOTE_ITEM = str_replace('.', '', $NOTE_ITEM);
                        $NOTE_ITEM = str_replace('Rap voi thermal', '', $NOTE_ITEM);
                        $NOTE_ITEM = str_replace(' ', '', $NOTE_ITEM);
                        $NOTE_ITEM = str_replace('<br/>', '', $NOTE_ITEM);
                        $NOTE_ITEM = str_replace('<br />', '', $NOTE_ITEM);
                        $NOTE_ITEM = str_replace('<br/>\n', '', $NOTE_ITEM);
                        $NOTE_ITEM = str_replace('<br />\n', '', $NOTE_ITEM);
                        $NOTE_ITEM = str_replace("\n", '', $NOTE_ITEM);

                        if (strpos($PO_REMARK_2, 'NIKE-WORLDON') !== false && !empty($GPM) ) {
                            $GHI_CHU_SO_GPM = $GPM;
                        }
                    }//end if so

                    // // //Trường hợp internal item  = CB1627627B(VN)
                    // // if ($SO_INTERNAL_ITEM == 'CB1627627B(VN)' && $checkCB==0 && $COUNT_LINE_SHOW >1) {
                    // //     //GET AND SET DATA EXPORT
                    // //     $arrayOutputTMP = [$PO_FORM_TYPE,$CREATED_DATE,$PO_NO_FI,$CB_SO_TOTAL,$PO_PROMISE_DATE,$PO_REQUEST_DATE,$SO_INTERNAL_ITEM,$PO_RBO,$SO_ORDER_ITEM,$QTY_TOTAL,$MATERIAL_CODE,$MATERIAL_DES,$MATERIAL_QTY_TOTAL,"","",$SO_WIDTH,$SO_HEIGHT,"10H000146-MT"," HS1111 1-3/4 (45mmx500meter) 1.125P 1640FT THERMAL TRANSFER RIBBON",$INK_QTY_TOTAL,$GHI_CHU_SO_GPM,$NOTE_ITEM,"","","","","","","",$CREATED_BY,$CREATE_TIME];//,$PO_COUNT_SO_LINE,$PO_CS
                    // //     fputcsv($output, $arrayOutputTMP);

                    // //     $checkCB=1; //gán lại checkCB để k chạy trường hợp này nữa
                    // // }


                    //GET AND SET DATA EXPORT
                    $arrayOutputTMP = [$PO_FORM_TYPE,$CREATED_DATE,$PO_NO_FI,$SO_LINE,$PO_PROMISE_DATE,$PO_REQUEST_DATE,$SO_INTERNAL_ITEM,$PO_RBO,$SO_ORDER_ITEM,$SO_PO_QTY,$MATERIAL_CODE,$MATERIAL_DES,$MATERIAL_QTY,"","",$SO_WIDTH,$SO_HEIGHT,$INK_CODE,$INK_DES,$INK_QTY,$GHI_CHU_SO_GPM,$NOTE_ITEM,"","","","","","","",$CREATED_BY,$CREATE_TIME];//,$PO_COUNT_SO_LINE,$PO_CS
                    fputcsv($output, $arrayOutputTMP);


                }//for ink

                // // if ($SO_INTERNAL_ITEM == 'CB1627627B(VN)' && $checkCB==0 && $COUNT_LINE_SHOW ==1) {
                // //     //GET AND SET DATA EXPORT
                // //     $arrayOutputTMP = [$PO_FORM_TYPE,$CREATED_DATE,$PO_NO_FI,$SO_LINE,$PO_PROMISE_DATE,$PO_REQUEST_DATE,$SO_INTERNAL_ITEM,$PO_RBO,$SO_ORDER_ITEM,"0",$MATERIAL_CODE,$MATERIAL_DES,"0","","",$SO_WIDTH,$SO_HEIGHT,"10H000146-MT"," HS1111 1-3/4 (45mmx500meter) 1.125P 1640FT THERMAL TRANSFER RIBBON",$INK_QTY,$GHI_CHU_SO_GPM,$NOTE_ITEM,"","","","","","","",$CREATED_BY,$CREATE_TIME];//,$PO_COUNT_SO_LINE,$PO_CS
                // //     fputcsv($output, $arrayOutputTMP);
                // // }
            }//end if ink. Trường hợp no cbs kết thúc

            //II. FORM PVH, TRIM, TRIM MACY
            $fields_mi = "MI_PO_NO, MI_PO_SO_LINE, MI_PO_FORM_TYPE, MI_MATERIAL_CODE, MI_MATERIAL_QTY, MI_MATERIAL_DES, MI_INK_CODE, MI_INK_QTY,MI_INK_DES,MI_PO_CREATED_BY,MI_CREATED_DATE ";
            $query_mi   = "SELECT $fields_mi FROM $table_mi WHERE MI_PO_NO = '$PO_NO' ORDER BY MI_CREATED_DATE ASC";
            $result_mi = mysqli_query($conn, $query_mi);
            if($result_mi === FALSE) { die(mysql_error()); }
            $num_mi = mysqli_num_rows($result_mi);
            if ($num_mi > 0) {
                $result_mi = mysqli_fetch_all($result_mi, MYSQLI_ASSOC);
                foreach ($result_mi as $key => $rows_mi) {
                    $FORM_NO            = $rows_mi['MI_PO_NO'];
                    $SO_LINE_CHECK      = $rows_mi['MI_PO_SO_LINE'];//WHERE
                    $MI_PO_FORM_TYPE    = $rows_mi['MI_PO_FORM_TYPE'];
                    $MATERIAL_CODE      = $rows_mi['MI_MATERIAL_CODE'];
                    $MATERIAL_QTY       = $rows_mi['MI_MATERIAL_QTY'];
                    $MATERIAL_DES       = $rows_mi['MI_MATERIAL_DES'];
                    if (strpos($MATERIAL_DES,'&amp;')!==false) {
                        $MATERIAL_DES = str_replace('&amp;',' & ', $MATERIAL_DES);
                    }

                    //Ban Duyen yêu cầu nếu là form trim, trim_macy thì des material trống
                    //Lấy từ DB_1LINE (no_cbs): cột material_des
                    if ($PO_FORM_TYPE == 'trim_macy' || $PO_FORM_TYPE == 'trim' ) {
                        $MATERIAL_DES       = '';
                    }

                    $INK_CODE           = $rows_mi['MI_INK_CODE'];
                    $INK_QTY            = $rows_mi['MI_INK_QTY'];
                    $INK_DES            = $rows_mi['MI_INK_DES'];
                    $CREATED_BY         = $rows_mi['MI_PO_CREATED_BY'];
                    //$CREATED_DATE       = formatDate($rows_mi['MI_CREATED_DATE']);//get
                    $CREATED_DATE      = formatDate( $PO_SAVE_DATE );
                    $CREATE_TIME       = formatTime($rows_mi['MI_CREATED_DATE']);
                    //2. get and set data from SOLINE save
                    $fields_so = "SO_PO_NO, SO_LINE, SO_PO_QTY, SO_INTERNAL_ITEM, SO_ORDER_ITEM, SO_WIDTH, SO_HEIGHT, SO_CREATED_DATE ";
                    $query_so   = "SELECT $fields_so FROM $table_so WHERE SO_PO_NO = '$FORM_NO' AND SO_LINE = '$SO_LINE_CHECK' ORDER BY SO_CREATED_DATE ASC";
                    $result_so = mysqli_query($conn, $query_so);
                    if($result_so === FALSE) { die(mysql_error()); }
                    $num_so = mysqli_num_rows($result_so);
                    if ($num_so > 0) {
                        $row_so = mysqli_fetch_array($result_so);//sử dụng hàm này cho truy vấn data (không dùng hàm khác)
                        $SO_PO_NO           = $row_so['SO_PO_NO'];
                        $SO_LINE            = $row_so['SO_LINE'];
                        $SO_PO_QTY          = (int)$row_so['SO_PO_QTY'];
                        $SO_INTERNAL_ITEM   = $row_so['SO_INTERNAL_ITEM'];
                        $SO_ORDER_ITEM      = $row_so['SO_ORDER_ITEM'];
                        $SO_WIDTH           = (float)$row_so['SO_WIDTH'];
                        $SO_HEIGHT          = (float)$row_so['SO_HEIGHT'];
                    }//end if so

                    //GET AND SET DATA EXPORT
                    $arrayOutputTMP = [$PO_FORM_TYPE,$CREATED_DATE,$PO_NO_FI,$SO_LINE,$PO_PROMISE_DATE,$PO_REQUEST_DATE,$SO_INTERNAL_ITEM,$PO_RBO,$SO_ORDER_ITEM,$SO_PO_QTY,$MATERIAL_CODE,$MATERIAL_DES,$MATERIAL_QTY,"","",$SO_WIDTH,$SO_HEIGHT,$INK_CODE,$INK_DES,$INK_QTY,$GHI_CHU_SO_GPM,$NOTE_ITEM,"","","","","","","",$CREATED_BY,$CREATE_TIME];//$PO_COUNT_SO_LINE,$PO_CS,
                    fputcsv($output, $arrayOutputTMP);

                }//for
            
            
            }//end if mi

            /* *********** III. FORM CBS */
            
            //tính tổng số lượng size từng PO_NO
            $query_sum_s   = "SELECT SUM(S_QTY) as TOTAL FROM $table_s WHERE S_PO_NO = '$PO_NO' ";
            $result_sum_s = mysqli_query($conn, $query_sum_s);
            if($result_sum_s === FALSE) { die(mysql_error()); }
            $result_sum_s = mysqli_fetch_array($result_sum_s);
            $S_QTY_TOTAL = $result_sum_s["TOTAL"];

            //Size
            $M_ID_ARR = array();
            $fields_s = "S_ID, S_PO_NO, S_PO_SO_LINE, S_PO_FORM_TYPE, S_SIZE, S_LABEL_ITEM, S_BASE_ROLL, S_QTY,S_INK_QTY,S_PO_CREATED_BY,S_CREATED_DATE ";
            $query_s   = "SELECT $fields_s FROM $table_s WHERE S_PO_NO = '$PO_NO' ORDER BY S_CREATED_DATE ASC";
            $result_s = mysqli_query($conn, $query_s);
            if($result_s === FALSE) { die(mysql_error()); }
            if (mysqli_num_rows($result_s) >0) {
                
                $result_s = mysqli_fetch_all($result_s, MYSQLI_ASSOC);
                
                $check_row_sum = 0;
                foreach ($result_s as $key => $rows_s) {
                    $FORM_NO        = $rows_s['S_PO_NO'];
                    $SO_LINE_CHECK  = $rows_s['S_PO_SO_LINE'];
                    $S_PO_FORM_TYPE = $rows_s['S_PO_FORM_TYPE'];
                    $SIZE           = $rows_s['S_SIZE'];
                    $LABEL_ITEM     = $rows_s['S_LABEL_ITEM'];
                    $BASE_ROLL      = $rows_s['S_BASE_ROLL'];
                    $S_QTY          = (int)$rows_s['S_QTY'];
                    $S_INK_QTY      = $rows_s['S_INK_QTY'];
                    $CREATED_BY     = $rows_s['S_PO_CREATED_BY'];

                    //$CREATED_DATE     = formatDate($rows_s['S_CREATED_DATE']);
                    $CREATED_DATE      = formatDate( $PO_SAVE_DATE );
                    $CREATE_TIME      = formatTime($rows_s['S_CREATED_DATE']);
                    
                    //Material ua_cbs or ms color
                    $fields_m = "M_NO, M_ID, M_PO_NO, M_PO_SO_LINE, M_PO_FORM_TYPE, M_MATERIAL_CODE, M_MATERIAL_QTY, M_MATERIAL_DES, M_PO_CREATED_BY,M_CREATED_DATE ";

                    //Option: ms color (cbs) form
                    if ($PO_FORM_TYPE=='cbs') {
                        $query_cbs = " SELECT material_code FROM `ms_color` WHERE item_color='$BASE_ROLL' ";
                        $result_cbs = mysqli_query($conn, $query_cbs);
                        if($result_cbs === FALSE) { die(mysql_error()); }
                        if (mysqli_num_rows($result_cbs) > 0) {
                            $result_cbs = mysqli_fetch_array($result_cbs);
                            $MATERIAL_CODE = trim($result_cbs['material_code']);
                        }

                        if (!empty($MATERIAL_CODE)){
                            $query_m   = "SELECT $fields_m FROM $table_m WHERE M_PO_NO='$PO_NO' AND M_MATERIAL_CODE = '$MATERIAL_CODE' ORDER BY M_CREATED_DATE ASC";
                        }

                    } else {
                        //Option: ua_cbs form
                        $query_m   = "SELECT $fields_m FROM $table_m WHERE M_PO_NO='$PO_NO' AND M_MATERIAL_CODE = '$BASE_ROLL' ORDER BY M_CREATED_DATE ASC";
                    }

                   //excute
                    $result_m = mysqli_query($conn, $query_m);
                    if($result_m === FALSE) { die(mysql_error()); }
                    $num_m = mysqli_num_rows($result_m);
                    $MATERIAL_QTY_SHOW = 0;
                    if ($num_m > 0) {
                        $MATERIAL_QTY=0;

                        while ($row_m = mysqli_fetch_array($result_m)) {
                            $M_ID = $row_m['M_ID']; //M_NO
                            $M_NO = $row_m['M_NO']; //M_NO
                            $M_NO_ID = $M_NO . $M_ID;
                            if (in_array($M_NO_ID, $M_ID_ARR)) {
                                continue;
                            } else {
                                // Lưu M_ID cho vào mảng đã duyệt để so sánh với các lần sau
                                array_push($M_ID_ARR, $M_NO_ID);
                                $FROM_NO            = $row_m['M_PO_NO'];
                                $M_PO_SO_LINE       = $row_m['M_PO_SO_LINE'];
                                $M_PO_FORM_TYPE     = $row_m['M_PO_FORM_TYPE'];
                                $MATERIAL_CODE      = $row_m['M_MATERIAL_CODE'];
                                $MATERIAL_QTY       = $row_m['M_MATERIAL_QTY'];
                                $MATERIAL_DES       = $row_m['M_MATERIAL_DES'];
                                if (strpos($MATERIAL_DES,'&amp;')!==false) {
                                    $MATERIAL_DES = str_replace('&amp;',' & ', $MATERIAL_DES);
                                }
                                $M_PO_CREATED_BY    = $row_m['M_PO_CREATED_BY'];
                                $M_CREATED_DATE     = formatDate($row_m['M_CREATED_DATE']);
                                break;
                            }
                            
                        }

                    }

                    //2. get and set data from SOLINE save
                    $fields_so = "SO_PO_NO, SO_LINE, SO_PO_QTY, SO_INTERNAL_ITEM, SO_ORDER_ITEM, SO_WIDTH, SO_HEIGHT, SO_CREATED_DATE ";
                    $query_so   = "SELECT $fields_so FROM $table_so WHERE SO_PO_NO = '$FORM_NO' AND SO_LINE = '$SO_LINE_CHECK' ORDER BY SO_CREATED_DATE ASC";
                    $result_so = mysqli_query($conn, $query_so);
                    if($result_so === FALSE) { die(mysql_error()); }
                    $num_so = mysqli_num_rows($result_so);
                    if ($num_so > 0) {
                        $row_so = mysqli_fetch_array($result_so);//sử dụng hàm này cho truy vấn data (không dùng hàm khác)
                        $SO_PO_NO           = $row_so['SO_PO_NO'];
                        $SO_LINE            = $row_so['SO_LINE'];
                        $SO_PO_QTY          = $row_so['SO_PO_QTY'];
                        $SO_PO_QTY          = (int)$SO_PO_QTY;
                        $SO_INTERNAL_ITEM   = $row_so['SO_INTERNAL_ITEM'];
                        $SO_ORDER_ITEM      = $row_so['SO_ORDER_ITEM'];
                        $SO_WIDTH           = $row_so['SO_WIDTH'];
                        $SO_WIDTH           = (float)$SO_WIDTH;
                        $SO_HEIGHT          = $row_so['SO_HEIGHT'];
                        $SO_HEIGHT          = (float)$SO_HEIGHT;
                    }//end if so

                    if (!$check_row_sum) {
                         //GET AND SET DATA EXPORT: SUM ROW SIZE @@@
                        // // $SAVE_DATE_SUM      = formatDate( $PO_SAVE_DATE );
                        // // $CREATE_TIME_SUM    = formatTime($PO_CREATED_TIME);
                        $arrayOutputTMP = [$PO_FORM_TYPE,$CREATED_DATE,$PO_NO_FI,$SO_LINE,$PO_PROMISE_DATE,$PO_REQUEST_DATE,$SO_INTERNAL_ITEM,$PO_RBO,$SO_ORDER_ITEM,$S_QTY_TOTAL,"","",$PO_MATERIAL_QTY,"","",$SO_WIDTH,$SO_HEIGHT,$PO_INK_CODE,$PO_INK_DES,$PO_INK_QTY,$GHI_CHU_SO_GPM,$NOTE_ITEM,"","","","","","","",$CREATED_BY,$CREATE_TIME];//,$PO_COUNT_SO_LINE,$PO_CS
                        fputcsv($output, $arrayOutputTMP);

                        $check_row_sum = 1;

                    }

                    //GET AND SET DATA EXPORT
                    
                    $arrayOutputTMP = [$PO_FORM_TYPE,$CREATED_DATE,$PO_NO_FI,$SO_LINE,$PO_PROMISE_DATE,$PO_REQUEST_DATE,$SO_INTERNAL_ITEM,$PO_RBO,$SO_ORDER_ITEM,$S_QTY,$MATERIAL_CODE,$MATERIAL_DES,$MATERIAL_QTY,"","",$SO_WIDTH,$SO_HEIGHT,$PO_INK_CODE,$PO_INK_DES,$S_INK_QTY,$GHI_CHU_SO_GPM,$NOTE_ITEM,"","","","","","","",$CREATED_BY,$CREATE_TIME];//,$PO_COUNT_SO_LINE,$PO_CS
                    fputcsv($output, $arrayOutputTMP);

                    

                }//end for SIZE

               


            }//end if size

        }//end for form (BIG)
        

    }//end if form. THE END
    fclose($output);  
    
   