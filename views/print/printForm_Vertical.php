<!-- print_Vertical: FORM ĐỨNG (CHIỀU DỌC) -->
<?php 
    if (!isset($_COOKIE["VNRISIntranet"])) header('Location: login.php'); //check login 
    if ($PO_FORM_TYPE == 'trim') {
        $title_print = "TRIM PRINTER";
    } else if ($PO_FORM_TYPE == 'pvh_rfid') {
        $title_print = "PVH PRINTER";
    } else if ($PO_FORM_TYPE == 'trim_macy') {
        $title_print = "MACY PRINTER";
    } else if ($PO_FORM_TYPE == 'ua_cbs' || $PO_FORM_TYPE == 'cbs') {
        $title_print = "CBS PRINTER";
    } else if ($PO_FORM_TYPE == 'ua_no_cbs') {
        $title_print = "NO CBS PRINTER";
    } else if ($PO_FORM_TYPE == 'rfid') {
        $title_print = "RFID PRINTER";
    } else {
        $title_print = " ... PRINTER";
    }

    include_once ("../../models/getCustomerJob_conn.php");

    echo '<!DOCTYPE html>';
    echo '<html>';
    echo '<head>';
        echo '<title>'.$title_print.'</title>';
        echo '<meta name="google" content="notranslate" />';
        echo '<link rel="stylesheet" href="../../assets/css/print/print_vertical.css">';
        echo '<link rel="stylesheet" href="../../assets/css/print/all_style.css">';
        echo '<script src="../../assets/js/jquery-1.10.1.min.js"></script>';
    echo '</head>';
    echo '<body>'; 
        echo '<table style="width:100%; ">';
            echo '<tr style="height:20px;">';
                $mixGroupBarcodeShow = !empty($custPONumberFilter) ? $BARCODE_CUST_PO : "GROUP: THERMAL/RFID";
                echo '<td style="height:20px;"><span style="color:blue;text-align:left;font-size:10px;height:20px;">PO: '.$mixGroupBarcodeShow.'</span></td>';
                echo '<td><span style="color:red;text-align:center;font-size:16px;font-weight:bold; ">'.$SHORT_LT.'</span></td>';
                echo '<td>';
                    echo '<span style="color:red;text-align:center;font-size:16px;font-weight:bold; ">';
                        echo ($PO_RBO == "RYOHIN KEIKAKU") ? '<span style="width:100px;"> MUJI </span>' : '';
                    echo '</span>';
                echo '</td>';
                echo '<td id="dh_inkjet">';  
                    // echo '<span style="color:red;text-align:center;font-size:16px;font-weight:bold; ">';
                        // JS load lên REMARK_MACHINE
                    // echo '</span>';
                echo '</td>';
            
                $remark_SHIP_RBO = '';
                $remark_SHIP_RBO_2 = '';

                $remarkShipRBO = remarkShipRBO($PO_RBO, $PO_SHIP_TO_CUSTOMER);
                if (!empty($remarkShipRBO)) {
                $remark_SHIP_RBO = $remarkShipRBO['remark'];
                $remark_SHIP_RBO_2 = $remarkShipRBO['remark_2'];
                }
                // Hiển thị WORLDON hoặc KH TINH LOI (trường hợp RBO là NIKE)
                echo !empty($remark_SHIP_RBO) ? $remark_SHIP_RBO : '';

                // Trường hợp RBO = FAST RETAILING hoặc UNIQLO
                echo remarkRBO($PO_RBO, $PO_ORDER_ITEM);
                echo '<td style="text-align:right;">&nbsp;';
                    echo (!empty($PO_REMARK_2)) ? '<span style="font-weight:bold;font-size:24px;">' . $PO_REMARK_2 . '</span>' : '';
                echo '</td>';
            echo '</tr>';
            echo '<tr>';
                echo '<td colspan=6 style="height:20px;color:red;text-align:center; background-color:#66ffcc;">';
                    echo '<h2 style="padding-top:9px;height:15px; ">LỆNH SẢN XUẤT/PRODUCTION ORDER</h2>';
                echo '</td>';
            echo '</tr>';
        echo '</table>';
        
        /* p-header  */
        echo '<div id="p-header">';
            echo '<table style="width:100%">';
                echo '<tr>';
                    echo '<td style="width:15%; font-size:18px;">No:</th>';
                    echo '<td style="width:30%;font-size:20px; font-weight:bold;color:red;">';
                        echo $PO_NO_FI;
                    echo '</td>';
                    echo '<td colspan=3 style="font-weight:bold; font-size:16px;text-align:right; height:35px;">'.$BARCODE.'</td>';
                echo '</tr>';
                echo '<tr>';
                    echo '<td>Date:</td>';
                    echo '<td style="font-weight:bold;width:10%;height:1%;color:red;">' .$PO_SAVE_DATE. '</td>';
                    echo '<td style="width:80px;"> &nbsp; </td>';
                    echo '<td style="width:110px;">Người làm lệnh: </td>';
                    echo '<td style="font-weight:bold;color:red;">'.$PO_CREATED_BY_OK.'</td>';
                echo '</tr>';
                echo '<tr>';
                    echo '<td>Order date:</td>';
                    //Thêm 1 ngày với PO_ORDERED_DATE 
                    echo '<td style="font-weight:bold;color:red;">' .$PO_ORDERED_DATE. '&nbsp;</td>';
                    echo '<td style="width:80px;height:1%;"> &nbsp; </th>';
                    echo '<td>CS Name:</td>';
                    echo '<td style="font-weight:bold;color:red;">' .$PO_CS. '</td>';
                echo '</tr>';
                echo '<tr>';
                    echo '<td style="font-weight:bold;font-size:22px;height:20px;"> SO-LINE</td>';
                    echo '<td colspan=2 style="font-weight:bold;border: 1px solid #0066ff;color:red;font-size:22px;height:20px; text-align:center;">';
                        echo ($PO_COUNT_SO_LINE > 1) ? ($PO_SO_LINE . "-" . $PO_COUNT_SO_LINE) : $PO_SO_LINE;
                    echo '</td>';
        
                    // <!--NOTE REMAKE MLA vietphan -->
                    echo '<td style="color:black;font-weight:bold;font-size:23px">';
                        echo (strpos(strtoupper($PO_NO), 'FR') !== false || remarkMLA($PO_RBO) == 'MLA') ? '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;MLA' : '';
                    echo '</td>';
                    echo '<td colspan=2 style="font-size:14px;text-align:left;font-weight:bold; ">'.$PO_REMARK_1.'&nbsp;</td>';
                echo '</tr>';
                echo '<tr>';
                    echo '<td>ITEM</td>';
                    echo '<td colspan=2 style="font-weight:bold; font-size:12px;text-align:center;">'.$PO_INTERNAL_ITEM.'</td>';
                    echo '<td style="font-size:16px;font-weight:bold;"> Promise date:&nbsp; </td>';
                    echo '<td style="font-weight:bold;color:red;font-size:16px;">' .$PO_PROMISE_DATE. '</td>';

                echo '</tr>';
                echo '<tr>';
                    echo '<td>ORDER ITEM</td>';
                    echo '<td colspan=2 style="font-weight:bold; font-size:12px;width:10%;text-align:center;">'.$PO_ORDER_ITEM.'</td>';
                    echo '<td style="font-size:16px;font-weight:bold;"> Request date: &nbsp; </td>';
                    echo '<td style="font-weight:bold;color:red;font-size:16px;">'.$PO_REQUEST_DATE.'</td>';
                echo '</tr>';
                echo '<tr>';
                    echo '<td>RBO: </td>';
                    echo '<td colspan=2 style="font-size:18px;height:20px; text-align:center;font-weight:bold;color:red;">';
                        echo (strpos(strtoupper($PO_RBO), 'UNIQLO') !== FALSE) ? 'UNIQLO' : $PO_RBO;
                    echo '</td>';
                    echo '<td>'; echo isset($CRDICRemark['remark']) ? $CRDICRemark['remark'] : ''; echo '</td>';
                    echo '<td style="font-size:18px;height:20px; text-align:left;font-weight:bold;color:red;"> '; echo isset($CRDICRemark['request_date']) ? $CRDICRemark['request_date'] : ''; echo '</td>';
                    // echo '<td> &nbsp;</td>';
                echo '</tr>';
                echo '<tr>';
                    echo '<td>Ship to: </td>';
                    // <!-- Thêm điều kiện SHIP TO -->
                    echo '<td colspan=5 class="header" style="color:red;font-weight:bold;font-size:'; echo (strpos(strtoupper($PO_SHIP_TO_CUSTOMER), 'KWANG VIET') !== FALSE || strpos(strtoupper($PO_SHIP_TO_CUSTOMER), 'WORLDON') !== FALSE) ? '22px;' : '12px;'; echo '">';
                        echo (strpos(strtoupper($PO_SHIP_TO_CUSTOMER), 'KWANG VIET') !== FALSE) ? 'KWANG VIET &nbsp; <span style="font-size:12px;"> GARMENT CO,LTD</span>' : $PO_SHIP_TO_CUSTOMER;
                    echo '</td>';
                echo '</tr>';
            echo '</table>';
        echo '</div>';
        
        echo '<hr width="100%" align="center" />';
  
        /* <!-- container 1 --> */
        
        $customerJob = customerJob($PO_SO_LINE, $PO_RBO, $PO_INK_CODE );
        $customerJob_barcode = '<img style="" src="../../data/barcode.php?text=' . $customerJob . '" />';
    
        echo '<div id="container1">';
            if ($PO_FORM_TYPE == 'rfid' || $PO_FORM_TYPE == 'ua_no_cbs') {
                
                echo '<div id="wrap-left">';
                    echo '<table style=" border: 1px solid #003366;">';
                        echo '<tr>';
                            echo '<th class="material-des0" style="text-align:center; font-size:12px;">Material Description</th>';
                            echo '<th class="material-code0">Material Code</th>';
                            echo '<th class="material-qty0" style="text-align:center;">Qty</th>';
                        echo '</tr>';

                        if ($num_mn > 0) {
                            $MN_MATERIAL_QTY_TOTAL = 0;
                            $MN_MATERIAL_QTY = 0;
                            $MN_MATERIAL_CODE_TMP_1 = '';
                            $MN_MATERIAL_CODE_TMP_2 = '';

                            $MN_MATERIAL_QTY_TMP = 0;
                            while ($row_mn = mysqli_fetch_array($result_mn)) {
                                //lấy tmp_1 giữ material_code, so sánh với tmp_2, nếu giống chỉ lấy qty. bỏ qua code phía dưới
                                //tiếp tục while
                                $MN_MATERIAL_CODE_TMP_1 = !empty($row_mn['MN_MATERIAL_CODE']) ? trim($row_mn['MN_MATERIAL_CODE']) : "";
                                if ($MN_MATERIAL_CODE_TMP_1 == $MN_MATERIAL_CODE_TMP_2) {
                                    $MN_MATERIAL_QTY_TMP = !empty($row_mn['MN_MATERIAL_QTY']) ? trim($row_mn['MN_MATERIAL_QTY']) : 0;
                                    $MN_MATERIAL_QTY_TOTAL  += $MN_MATERIAL_QTY_TMP;
                                    continue;
                                }
                                $MN_MATERIAL_CODE       = $MN_MATERIAL_CODE_TMP_1;
                                $MN_MATERIAL_DES        = !empty($row_mn['MN_MATERIAL_DES']) ? trim($row_mn['MN_MATERIAL_DES']) : "";
                                $MN_MATERIAL_QTY_TMP    = !empty($row_mn['MN_MATERIAL_QTY']) ? trim($row_mn['MN_MATERIAL_QTY']) : 0;
                                $MN_MATERIAL_REMARK     = !empty($row_mn['MN_MATERIAL_REMARK']) ? trim($row_mn['MN_MATERIAL_REMARK']) : "";
                                $MN_MATERIAL_QTY_TOTAL  += $MN_MATERIAL_QTY_TMP;

                                $MN_MATERIAL_CODE_TMP_2 = $MN_MATERIAL_CODE_TMP_1; //giữ giá trị material_code từ lần trước đó để so sanh

                                echo '<tr>';
                                    echo '<td class="material-des" style="'; echo (strpos($PO_INTERNAL_ITEM, 'CB1627') !== false) ? 'height:37px;' : ''; echo '">';
                                        echo $MN_MATERIAL_DES;
                                    echo '</td>';
                                    echo '<td class="material-code" style="font-weight:bold;font-size:14px;">'.$MN_MATERIAL_CODE.'</td>';
                                    //load tu javascript
                                    echo '<td id="material_qty" class="material-qty" style="text-align:center;font-weight:bold;font-size:14px;">&nbsp;</td>';
                                echo '</tr>';
                            } //end while
                        } //end if 

                        // $MN_MATERIAL_QTY_TOTAL = ($MN_MATERIAL_QTY_TOTAL !=0) ? number_format($MN_MATERIAL_QTY_TOTAL) : 0;
                        
                        echo '<tr>';
                            echo '<td colspan=2 style="font-size:11px;border: 1px solid #99ccff; font-weight:bold; text-align:center;">TỔNG SỐ LƯỢNG:</td>';
                            echo '<td colspan=2 style="font-size:16px;border:1px solid #99ccff; font-weight:bold; text-align:center;color:red;">'.number_format($MN_MATERIAL_QTY_TOTAL).'</td>';
                        echo '</tr>';
                    echo '</table>';
                    echo '<div><span style="font-weight:bold;font-size:11px;">'.$PO_MATERIAL_REMARK.'</span></div>';
                echo '</div>';

                echo '<div id="wrap-right">';
                    echo '<table style=" border: 1px solid #003366;">';
                        echo '<tr style="">';
                            echo '<th class="material-des0" style="text-align:center; font-size:12px;">Ink Description</th>';
                            echo '<th class="material-code0">Ink Code</th>';
                            echo '<th class="material-qty0">Qty</th>';
                        echo '</tr>';
        
                        // <!-- cho while load data from database -->
        
                        //load ink
                        if ($num_ink > 0) {
                            $INK_QTY_TOTAL = 0;
                            $INK_CODE_TMP_1 = '';
                            $INK_CODE_TMP_2 = '';
                            while ($row_ink = mysqli_fetch_array($result_ink)) {
                                //lấy tmp_1 giữ ink_code, so sánh với tmp_2, nếu giống chỉ lấy qty. bỏ qua code phía dưới
                                //tiếp tục while
                                $INK_CODE_TMP_1 = !empty($row_ink['INK_CODE']) ? trim($row_ink['INK_CODE']) : "";
                                if ($INK_CODE_TMP_1 == $INK_CODE_TMP_2) {
                                    $INK_QTY_TMP = !empty($row_ink['INK_QTY']) ? trim($row_ink['INK_QTY']) : 0;
                                    $INK_QTY_TOTAL  += $INK_QTY_TMP;
                                    continue;
                                }

                                $INK_DES        = !empty($row_ink['INK_DES']) ? trim($row_ink['INK_DES']) : "";
                                $INK_CODE       = !empty($row_ink['INK_CODE']) ? trim($row_ink['INK_CODE']) : "";
                                $INK_QTY_TMP    = !empty($row_ink['INK_QTY']) ? trim($row_ink['INK_QTY']) : 0;
                                $INK_REMARK     = !empty($row_ink['INK_REMARK']) ? trim($row_ink['INK_REMARK']) : "";
                                $INK_QTY_TOTAL  += $INK_QTY_TMP;
                                $INK_CODE_TMP_2 = $INK_CODE_TMP_1;
                                
                                echo '<tr>';
                                    echo '<td class="material-des" style="height:37px;">'.$INK_DES.'</td>';
                                    echo '<td class="material-code" style="font-weight:bold;font-size:14px;">'.$INK_CODE.'</td>';
                                    // load tu js
                                    echo '<td id="ink_qty" class="material-qty" style="font-weight:bold;font-size:14px;">&nbsp;</td>';
                                echo '</tr>';

                            } //end while
                        } //end if 

                        echo '<tr>';
                            echo '<td colspan=2 style="font-size:11px;border: 1px solid #99ccff; font-weight:bold; text-align:center;">TỔNG SỐ LƯỢNG:</td>';
                            echo '<td style="font-size:16px;border: 1px solid #99ccff; font-weight:bold; text-align:center;color:red;">'.number_format($INK_QTY_TOTAL).'</td>';
                        echo '</tr>';
                    echo '</table>';

                    echo '<div> <span style="font-weight:bold;font-size:11px;">' .$PO_INK_REMARK. '</span></div>';
                echo '</div>';
    
            } else if ($PO_FORM_TYPE == 'ua_cbs' || $PO_FORM_TYPE == 'cbs') {
                echo '<div id="wrap-left">';
                    echo '<table style=" border: 1px solid #003366;">';
                        echo '<tr>';
                            echo '<th class="material-cbs-des0" style="text-align:center; font-size:12px;">Material Description</th>';
                            echo '<th class="material-cbs-code0">Material Code</th>';
                            echo '<th class="material-qty0">Qty</th>';
                        echo '</tr>';

                        if ($num_m > 0) {
                            $M_MATERIAL_QTY_TOTAL = 0;
                            $MATERIAL_TMP_1 = '';
                            $MATERIAL_TMP_2 = '';
                            while ($row_m = mysqli_fetch_array($result_m)) {
                                if ($PO_FORM_TYPE == 'ua_cbs') {
                                    $M_MATERIAL_CODE        = !empty($row_m['M_MATERIAL_CODE']) ? trim($row_m['M_MATERIAL_CODE']) : "";
                                    $M_MATERIAL_DES        = !empty($row_m['M_MATERIAL_DES']) ? trim($row_m['M_MATERIAL_DES']) : "";
                                    $M_MATERIAL_QTY    = !empty($row_m['M_MATERIAL_QTY']) ? trim($row_m['M_MATERIAL_QTY']) : 0;
                                    $M_MATERIAL_QTY = (int) $M_MATERIAL_QTY;
              
                                    $M_MATERIAL_REMARK     = !empty($row_m['M_MATERIAL_REMARK']) ? trim($row_m['M_MATERIAL_REMARK']) : "";
                                    $M_MATERIAL_QTY_TOTAL  += (int) $M_MATERIAL_QTY; //Không lấy qty này nữa mà lấy qty đã tính ngoài làm lệnh, duyen yêu cầu 20191218

                                    echo '<tr>';
                                        echo '<td class="material-des">'.substr($M_MATERIAL_DES, 0, 31).'</td>';
                                        echo '<td class="material-code" style="font-weight:bold;font-size:14px;">'.$M_MATERIAL_CODE.'</td>';
                                        echo '<td class="material-qty" style="font-weight:bold;font-size:14px;">'.number_format($M_MATERIAL_QTY).'</td>';
                                    echo '</tr>';

                                } else {
                                    //sử dụng biến tmp_1 giữ giá trị material_code, so sánh với tmp_2. Nếu giống nhau thì chỉ lấy ra
                                    //giá trị qty, bỏ qua code phía dưới, tiếp tục while
                                    $MATERIAL_TMP_1        = !empty($row_m['M_MATERIAL_CODE']) ? trim($row_m['M_MATERIAL_CODE']) : "";
                                    if ($MATERIAL_TMP_1 == $MATERIAL_TMP_2) {
                                        $M_MATERIAL_QTY_TMP = !empty($row_m['M_MATERIAL_QTY']) ? trim($row_m['M_MATERIAL_QTY']) : 0;
                                        continue;
                                    }
                                    $M_MATERIAL_CODE       = $MATERIAL_TMP_1;
              
                                    $M_MATERIAL_DES        = !empty($row_m['M_MATERIAL_DES']) ? trim($row_m['M_MATERIAL_DES']) : "";
                                    $M_MATERIAL_QTY_TMP    = !empty($row_m['M_MATERIAL_QTY']) ? trim($row_m['M_MATERIAL_QTY']) : 0;
                                    $M_MATERIAL_QTY        = (int) $M_MATERIAL_QTY_TMP;
              
                                    $M_MATERIAL_REMARK     = !empty($row_m['M_MATERIAL_REMARK']) ? trim($row_m['M_MATERIAL_REMARK']) : "";
                                    $M_MATERIAL_QTY_TOTAL  += (int) $M_MATERIAL_QTY_TMP; //Không lấy qty này nữa mà lấy qty đã tính ngoài làm lệnh, duyen yêu cầu 20191218
                                    //
                                    $MATERIAL_TMP_2  = $MATERIAL_TMP_1;

                                    echo '<tr>';
                                        echo '<td class="material-des">'.substr($M_MATERIAL_DES, 0, 31).'</td>';
                                        echo '<td class="material-code" style="font-weight:bold;font-size:14px;">'.$M_MATERIAL_CODE.'</td>';
                                        echo '<td class="material-qty" style="font-weight:bold;font-size:14px;">'.number_format($M_MATERIAL_QTY).'</td>';
                                    echo '</tr>';
                                }
                            }
                        }

                        echo '<tr>';
                            echo '<td colspan=2 style="font-size:11px;border: 1px solid #99ccff; font-weight:bold; text-align:center;">TỔNG SỐ LƯỢNG:</td>';
                            echo '<td colspan=2 style="font-size:16px;border: 1px solid #99ccff; font-weight:bold; text-align:center;color:red;">'.number_format($PO_MATERIAL_QTY).'</td>';
                        echo '</tr>';

                    echo '</table>';

                    echo '<div><span style="font-weight:bold;font-size:11px;">' .$PO_MATERIAL_REMARK. '</span></div>';
                echo '</div>';

                echo '<div id="wrap-right">';
                    echo '<table style=" border: 1px solid #003366;">';
                        echo '<tr style="height:20px;">';
                        echo '<th class="size0" style="text-align:center; font-size:12px;">Size</th>';
                        echo '<th class="label-item0" style="text-align:center; font-size:12px;">Label Item</th>';
                        echo '<th class="base-roll0">Base roll</th>';
                        echo '<th class="material-qty0">Qty</th>';
                        echo '</tr>';

                        if ($num_s > 0) {
                            $S_QTY_TOTAL = 0;
                            while ($row_s = mysqli_fetch_array($result_s)) {
                                $S_SIZE        = !empty($row_s['S_SIZE']) ? trim($row_s['S_SIZE']) : "";
                                $S_LABEL_ITEM  = !empty($row_s['S_LABEL_ITEM']) ? trim($row_s['S_LABEL_ITEM']) : "";
                                $S_BASE_ROLL   = !empty($row_s['S_BASE_ROLL']) ? trim($row_s['S_BASE_ROLL']) : 0;
                                $S_QTY         = !empty($row_s['S_QTY']) ? trim($row_s['S_QTY']) : "";
                                $S_QTY_TOTAL   += $S_QTY;

                                echo '<tr>';
                                    echo '<td class="size">'.$S_SIZE.'</td>';
                                    echo '<td class="label-item">'.substr($S_LABEL_ITEM, 0, 30).'</td>';
                                    echo '<td class="base-roll" style="font-weight:bold;font-size:14px;">'.$S_BASE_ROLL.'</td>';
                                    echo '<td class="material-unit" style="font-weight:bold;font-size:14px;">'.number_format($S_QTY).'</td>';
                                echo '</tr>';
                            }
                        }

                        echo '<tr>';
                            echo '<td colspan=3 style="font-size:11px;border: 1px solid #99ccff; font-weight:bold; text-align:center;">TỔNG SỐ LƯỢNG:</td>';
                            echo '<td style="font-size:16px;border: 1px solid #99ccff; font-weight:bold; text-align:center;color:red;">'.number_format($S_QTY_TOTAL).'</td>';
                        echo '</tr>';
                    echo '</table>';
                    
                    echo '<br />';
                echo '</div>';  

                echo '<div id="wrap-left";>';
                    echo '<table style=" border: 1px solid #003366;">';
                        echo '<tr style="height:1%;">';
                            echo '<th class="material-cbs-des0" style="text-align:center; font-size:11px;">Ink Description</th>';
                            echo '<th class="material-cbs-code0" style="font-weight:bold;font-size:14px;">Ink Code</th>';
                            echo '<th class="material-qty0" style="font-weight:bold;font-size:14px;">Qty</th>';
                            echo '<th class="material-unit0"> Unit</th>';
                        echo '</tr>';
                        echo '<tr style="height:1%;">';
                            echo '<td class="material-des">'.$PO_INK_DES.'</td>';
                            echo '<td class="material-code" style="font-weight:bold;font-size:14px;">'.$PO_INK_CODE.'</td>';
                            echo '<td class="material-qty" style="font-weight:bold;font-size:14px;">'.number_format($PO_INK_QTY).'</td>';
                            echo '<td class="material-unit">MT</td>';
                        echo '</tr>';
                        echo '<tr style="height:1%;">';
                            echo '<td colspan=4 class="material-des"><span style="font-weight:bold;height:1%; ">'.$PO_INK_REMARK.'</span></td>';
                        echo '</tr>';
                    echo '</table>';

            }
        echo '</div>';

        echo '<hr width="99%" border:#f5f5f5 align="center" />';
        
        //view GPM if GPM !empty
        if ($PO_GPM != "&nbsp;") {
            $BARCODE_GPM = '<img style="" src="../../data/barcode.php?text=' . $PO_GPM . '" />';
            echo '<div style="height:20px;" >GPM: <span style="color:red;font-size:18px;font-weight:bold;">' . $PO_GPM . ' &nbsp;&nbsp;' . $BARCODE_GPM . ' </span></div>';
        } else  $BARCODE_GPM = '';
        
        echo '<div>';
            echo '<table>';
                echo '<tr>';
                    echo '<td style="height:1%;width:15%; font-weight:bold; font-size:16px;">PO QTY: </td>';
                    echo '<td style="width:15%; font-weight:bold; font-size:26px; color:red;">'.number_format($PO_QTY).'</td>';
                    echo '<td style="width:10%">PCS &nbsp; </td>';
                    echo '<td colspan=2 style="font-weight:bold; font-size:12px; text-align:right;">Kích thước in: '.$PO_LABEL_SIZE.'</td>';
                echo '</tr>';
                echo '<tr>';
                    echo '<td style="width:15%; font-weight:bold; font-size:16px;">&nbsp; </td>';
                    echo '<td colspan=2 style="width:25%; font-weight:bold; color:red; text-align:left;height:20px;">'.$BARCODE_PO_QTY.'</td>';
                    echo '<td style="width:25%; font-weight:bold; font-size:18px;text-align:right;">&nbsp; </td>';
                    echo '<td style="'.$styleDIT.'">&nbsp; '.$remarkDIT.'</td>';
                echo '</tr>';
            echo '</table>';
        echo '</div>';

        // echo '<div> Chi tiết in/Printting Detail</div>';
        echo '<div id="container2">';
            echo '<table style="width:50%;float:left; ">';
                echo '<tr>';
                    echo '<td colspan="2" style="height:1%;width:13%;border:0.1px solid #99ccff;text-align:center;text-transform:uppercase;"> Chi tiết in/Printting Detail</td>';
                    // echo '<td style="width:auto;border: 0.1px solid #99ccff; "> &nbsp;</td>';
                    echo '<td style="width:25%;border: 0.1px solid #99ccff;text-transform:uppercase; ">Packing &nbsp;</td>';
                    echo '</tr>';
                echo '<tr>';
                    echo '<td style="height:1%;width:13%;border: 0.1px solid #99ccff; ">Q\'ty &nbsp;</td>';
                    echo '<td style="width:auto;border: 0.1px solid #99ccff; "> &nbsp;</td>';
                    echo '<td style="width:25%;border: 0.1px solid #99ccff; "> &nbsp;</td>';
                echo '</tr>';
                echo '<tr>';
                    echo '<td style="height:1%;width:13%;border: 0.1px solid #99ccff; ">Date</td>';
                    echo '<td style="width:auto;border: 0.1px solid #99ccff; "> &nbsp;</td>';
                    echo '<td style="width:25%;border: 0.1px solid #99ccff; "> &nbsp;</td>';
                echo '</tr>';
            echo '</table>';

            echo '<table style="width:47%; float:right;border:0; ">';
                echo '<tr>';
                    echo '<td style="height:1%;width:50%;">SO MAY: ....................</td>';
                    echo '<td style="">'; 
                        echo !empty($REMARK_KKL) ? '<div style="font-weight:bold;color:blue;border:1px solid;padding:1px;text-align:center;">'.$REMARK_KKL.'</div>' : ''; 
                    echo '</td>';
                echo '</tr>';
                echo '<tr>';
                    echo '<td style="width:50%; font-weight:bold; height:1%;">Customer Job: </td>';
                    echo '<td style="text-align:center;font-weight:bold;">';
                        echo !empty($customerJob) ? $customerJob : '';
                    echo '</td>';
                echo '</tr>';
                echo '<tr>';
                    echo '<td style="">&nbsp;</td>';
                    echo '<td style="font-weight:bold;text-align:center;height:1%;">';
                        echo !empty($customerJob) ? $customerJob_barcode : '';
                    echo '</td>';
                echo '</tr>';
            echo '</table>';
        echo '</div>';
        
        echo '<hr width="99%" border:#f5f5f5 align="center" />';
        
        echo '<div id="p-bottom" style="width:100%;">';
            echo '<table>';
                echo '<tr style="height:1%;">';
                    echo '<td style="height:1%;width:45%;">LẤY MẪU</td>';
                    echo '<td style="width:10%;">P <input style="width:20px;height:15px;" type="checkbox" name="sample" value="sample"></td>';
                    echo '<td style="width:20%;">.................... pcs</td>';
                    echo '<td style="width:30%; text-align:right;">CS <input style="width:20px;height:15px;" type="checkbox" name="cs" value="cs"></td>';
                    echo '<td style="font-weight:bold;">.................... pcs</td>';
                echo '</tr>';
                echo '<tr style="height:1%;">';  
                    echo '<td style="width:30%;">SAMPLING</td>';
                    echo '<td style="width:10%;">S <input style="width:20px;height:15px;" type="checkbox" name="sample" value="sample"></td>';
                    echo '<td style="width:20%;">.................... pcs</td>';
                    echo '<td style="width:30%; text-align:right;">PD <input style="width:20px;height:15px;" type="checkbox" name="cs" value="cs"></td>';
                    echo '<td style="font-weight:bold;">.................... pcs</td>';
                echo '</tr>';
                echo '<tr style="height:1%;">';
                    echo '<td style="width:30%;">Nhân viên in (printed by):</td>';
                    echo '<td colspan=4 style="width:auto;">&nbsp;</td>';
                echo '</tr>';
                echo '<tr style="height:1%;">';
                    echo '<td style="width:30%;">Nhân viên QC (Quality checked by): </td>';
                    echo '<td style="width:10%;">&nbsp;</td>';
                    echo '<td style="width:20%;">&nbsp;</td>';
                    echo '<td style="width:30%; text-align:right;">Ký tên (singnature):</td>';
                    echo '<td style="font-weight:bold;">........................................................</td>';
                echo '</tr>';
                echo '<tr style="height:1%;">';
                    echo '<td style="width:30%;">Nhân viên đóng gói (Packing by):</td>';
                    echo '<td style="width:10%;">&nbsp;</td>';
                    echo '<td style="width:20%;">&nbsp;</td>';
                    echo '<td style="width:30%; text-align:right;">Ký tên (singnature):</td>';
                    echo '<td style="font-weight:bold;">........................................................</td>';
                echo '</tr>';
            echo '</table>';
            
            echo '<hr width="99%" border:#f5f5f5 align="center" />';

            echo '<div style="width:100%;" >';
                echo '<div class="" style="float:left;width:49.5%;min-height:100px; ">';
                    // remark sample
                    echo (!empty($REMARK_SAMPLE)) ? '<span style="width:100%; font-weight:bold;font-size:20px;color:red;">' . $REMARK_SAMPLE . '</span>' : '';

                    // remark Kiem EPC 100
                    if (!empty(remarkKiemEPC100($PO_RBO))) {
                    echo '<div style="width:100%;font-weight:bold;font-size:23px;margin-top:1px;" >' . remarkKiemEPC100($PO_RBO) . '</div>';
                    }

                    // remark sample line
                    echo (!empty($PO_MAIN_SAMPLE_LINE)) ? "<div style='width:100%;margin-top:1px;'> $PO_MAIN_SAMPLE_LINE </div>" : "";

                    // remark URGENT
                    $URGENT = '<div style="color:blue;font-weight:bold;font-size:26px;background-color: #e6ffff;text-align:center;border: 1px solid #99ccff;margin-top:1px;">URGENT</div>';
                    echo (strtotime($PO_REQUEST_DATE) <= strtotime($PO_SAVE_DATE)) ? $URGENT : "";

                    // remark Dán 1 pcs lên tờ cap NIKE, ship to CCH TOP
                    if (strpos($PO_RBO, 'NIKE') !== false && strpos($PO_SHIP_TO_CUSTOMER, 'CCH TOP') !== false) {
                        echo '<div><span style="width:100%;font-weight:bold;font-size:20px;margin-top:1px; ">Dán 1pcs lên tờ Cap</span></div>';
                    }

                    // Xử lý hiện thị remark liên quan Bill to
                    echo (!empty($remarkBillto)) ? "<div style='width:100%;margin-top:1px;'> $remarkBillto </div> " : "";

                    // remarkUniqloEPC
                    echo (!empty($remarkUniqloEPC)) ? "<div style='width:100%;margin-top:3px;color:red;font-size:20px;font-weight:bold;'> $remarkUniqloEPC </div> " : "";

                    // remarkPacking
                        echo (!empty($remarkPacking)) ? "<div style='width:100%;margin-top:1px;'> $remarkPacking </div> " : "";

                    // remarkShipto
                        echo (!empty($remarkShipto)) ? "<div style='width:100%;margin-top:1px;'> $remarkShipto </div> " : "";

                    // remarkBillRBO
                        echo (!empty($remarkBillRBO)) ? "<div style='width:100%;margin-top:1px;'> $remarkBillRBO </div> " : "";

                        
                    
                    

                echo '</div>';

                echo '<div class="" style="float:right; width:49.3%; min-height:100px; ">';

                    echo (!empty($PO_REMARK_3)) ? "<div style='width:100%;margin-top:1px;' > $PO_REMARK_3 </div> " : "";
                    echo (!empty($PO_REMARK_4)) ? "<div class='remark-4' > $PO_REMARK_4 </div> " : "";
                    echo (!empty($PO_REMARK_5)) ? "<div style='width:100%;margin-top:1px;' > $PO_REMARK_5 </div> " : "";
                    echo (!empty($OTHER_REMARK_1)) ? "<div style='width:100%;margin-top:1px;font-weight:bold;font-size:22px;' > $OTHER_REMARK_1 </div> " : "";
                    echo (!empty($OTHER_REMARK_2)) ? "<div style='width:100%;margin-top:1px;font-weight:bold;font-size:22px;' > $OTHER_REMARK_2 </div> " : "";
                    echo (!empty($OTHER_REMARK_3)) ? "<div style='width:100%;margin-top:1px;font-weight:bold;font-size:22px;' > $OTHER_REMARK_3 </div> " : "";
                    echo (!empty($OTHER_REMARK_4)) ? "<div style='width:100%;margin-top:1px;font-weight:bold;font-size:22px;' > $OTHER_REMARK_4 </div> " : "";

                    echo (!empty($remark_material_Special_Bill_To)) ? "<div style='width:100%;margin-top:1px;'> $remark_material_Special_Bill_To </div> " : "";

                    // Hiển thị remark Giao hàng dạng cuộn trường hợp KH TINH LOI và RBO là NIKE
                    // echo (!empty($remark_SHIP_RBO_2)) ? "<div style='width:100%;margin-top:1px;'> $remark_SHIP_RBO_2 </div> " : "";
                    echo (!empty($remarkBillRBONike)) ? "<div style='width:100%;margin-top:1px;'> $remarkBillRBONike </div> " : "";
                    echo (!empty($remarkEAS)) ? "<div style='width:100%;margin-top:1px;font-weight:bold;font-size:20px;'> $remarkEAS </div> " : "";
                    // ORDERED_ITEM: DEC029
                    echo (!empty($remarkDEC029)) ? "<div class='DEC029'> $remarkDEC029 </div> " : "";

                    echo (!empty($remarkPUMANA)) ? "<div style='width:100%;text-align:center;'> $remarkPUMANA </div> " : "";

                    echo (!empty($material_code_barcode)) ? "<div style=''> $material_code_barcode </div> " : "";

                    echo (!empty($remarkFRUIC)) ? "<div style='width:100%;text-align:center;'> $remarkFRUIC </div> " : "";
                    
                    // 20211013- remark: RIP. mail: [RFID_SB] Thêm thông tin lên trên lệnh SX
                    echo (!empty($remarkInternalItem2)) ? "<div class='large-remark'> $remarkInternalItem2 </div> " : "";
                    
                
                echo '</div>';
            echo '</div>';
    
            // packing instr
            if (!empty($PACKING_INSTRUCTIONS)) {
                echo "<div style='width:100%;float:left;'>" . $PACKING_INSTRUCTIONS . "</div>";
            }
        
        echo '</div>';
            
        require_once('printForm_macy_page_5.php');

        // Hiển thị trang check list
        require_once('printForm_check_list.php');

    echo '</body>';
    echo '</html>';
?>
<script>
    //reload material_qty INK_QTY_10F000146
    document.getElementById("material_qty").innerHTML = "<span style='text-align:center;'><?php echo number_format($MN_MATERIAL_QTY_TOTAL); ?></span>";
    document.getElementById("ink_qty").innerHTML = "<?php echo number_format($INK_QTY_TOTAL); ?>";
    // document.getElementById("ink_qty_10F000146").innerHTML = "<?php // echo number_format($INK_QTY_10F000146); ?>";
    var REMARK_MACHINE = '<?php echo $REMARK_MACHINE; ?>';
    
    var remarkMachineShow;
    if (REMARK_MACHINE) {
        remarkMachineShow = "<div style='border-radius:8%;text-align:center;border:1px solid red;heigth:25px;padding:3px;font-size:20px;font-weight:bold;background-color:#e6ffff;color:red;width:70%;'>"+REMARK_MACHINE+"</div>";
        // show remark machine (position)
        document.getElementById("dh_inkjet").innerHTML = remarkMachineShow;
    }
    
    

</script>


