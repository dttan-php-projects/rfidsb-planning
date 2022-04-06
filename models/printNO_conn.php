<?php
// require_once ( "../define_constant_system.php");
// require_once (PATH_MODEL . "/__connection.php");
// require_once (PATH_MODEL . "/automail_conn.php");

//require_once ( "../../data/formatDate.php");

if (!isset($_COOKIE["VNRISIntranet"])) {
    header('Location: login.php');
} else { //get print by
    $PRINT_BY = $_COOKIE["VNRISIntranet"];
}

function remarkShipRBO($rbo, $ship_to_customer)
{
    $rbo = trim(strtoupper($rbo));
    $ship_to_customer = trim(strtoupper($ship_to_customer));

    $remark = '';
    $remark_2 = '';
    $start_td = '<td style="background-color: #e6ffff;text-align:center;border: 2px solid #99ccff; width:200px;" >';
    $end_td = '</td>';
    if (strpos($ship_to_customer, 'WORLDON') !== false) {
        $remark = $start_td . '<span class="speech-worldon"> WORLDON <br /> PNK RIÊNG </span>' . $end_td;
    }
    if (strpos($ship_to_customer, 'TNHH MAY TINH LOI') !== false) {
        if (strpos($rbo, 'NIKE') !== false) {
            $remark = $start_td . '<span class="speech-worldon"> KH TINH LOI</span>' . $end_td;
            $remark_2 = '<div style="color:blue;font-weight:bold;font-size:26px;background-color: #e6ffff;text-align:center;border: 1px solid #99ccff;margin-top:1px;">Giao hàng dạng cuộn</div>';
        }
    } else if (strpos($ship_to_customer, 'CCH TOP (VN) CO LTD') !== false) {
        if (strpos($rbo, 'NIKE') !== false) {
            $remark = $start_td . '<span class="speech-worldon"> KH CCH TOP</span>' . $end_td;
            $remark_2 = '';
        }
    } else if (strpos($ship_to_customer, 'CONG TY TNHH, LIEN DOANH VINH HUNG') !== false) {
        if (strpos($rbo, 'NIKE') !== false) {
            $remark = $start_td . '<span class="speech-worldon"> KH VINH HUNG</span>' . $end_td;
            $remark_2 = '<div style="color:blue;font-weight:bold;font-size:26px;background-color: #e6ffff;text-align:center;border: 1px solid #99ccff;margin-top:1px;">Giao hàng dạng xếp</div>';
        }
    }

    // results
    $results = array(
        'remark' => $remark,
        'remark_2' => $remark_2
    );

    return $results;
}

function remarkRBO($rbo, $ordered_item)
{

    $rbo = trim(strtoupper($rbo));
    $remark = '';

    $ordered_item_arr = array('BS00131ADQRU_65_IJ', 'BS00121ADQRU_65_V2_IJ', 'BS00124ADQRU_80_IJ');

    if (strpos($rbo, 'FAST RETAILING') !== false || strpos($rbo, 'UNIQLO') !== false) {
        $remark = '<td style="background-color: #e6ffff;text-align:center;border: 2px solid #99ccff;" ><span class="speech-worldon"> UNIQLO </span></td>';
        foreach ($ordered_item_arr as $itemCheck) {
            if (strtoupper($ordered_item) == $itemCheck) {
                $remark = '<td style="background-color: #e6ffff;text-align:center;border: 2px solid #99ccff;" ><span class="speech-worldon"> UNIQLO RUSSIA </span></td>';
            }
        }
    }

    return $remark;
}

function remarkUniqloEPC($rbo, $ordered_item)
{

    $remark = '';

    $ordered_item_arr = array('BS00131ADQRU_65_IJ', 'BS00121ADQRU_65_V2_IJ', 'BS00124ADQRU_80_IJ');

    if (strpos($rbo, 'FAST RETAILING') !== false || strpos($rbo, 'UNIQLO') !== false) {

        foreach ($ordered_item_arr as $itemCheck) {
            if (strtoupper($ordered_item) == $itemCheck) {
                $remark = '<span stype="color:red;">EPC vs Marking Code matching</span>';
            }
        }
    }

    return $remark;
}

function remarkMLA($rbo)
{

    $rbo = trim(strtoupper($rbo));

    $array = array(
        'ADIDAS', 'NIKE', 'UNDER ARMOUR', 'PUMA', 'DECATHLON', 'H&M', 'INDITEX', 'UNIQLO', 'GU', 'FAST RETAILING',
        'PRIMARK', 'AMAZON', 'TARGET', 'WALMART', 'VF GROUP', 'VICTORIA', 'SECRET'
    );

    $remark = '';

    foreach ($array as $item) {

        if (strpos($rbo, $item) !== false) {
            $remark = 'MLA';
            break;
        }
    }

    return $remark;
}

function remarkKiemEPC100($rbo)
{

    $rbo = trim(strtoupper($rbo));
    $remark = '';
    $array = array('ADIDAS', 'WORLDON', 'NIKE', 'MUJI', 'UNIQLO', 'MACY');
    foreach ($array as $item) {
        if (strpos($rbo, $item) !== false) {
            $remark = 'KIỂM EPC 100%';
            break;
        }
    }

    return $remark;
}

function inkNoCBSInfo($PO_NO, $SO_LINE)
{

    $results = array();
    $table = "rfid_po_ink_no_cbs_save";

    $conn = getConnection();
    $sql = "SELECT * FROM $table WHERE `INK_PO_NO` = '$PO_NO' AND `INK_PO_SO_LINE` = '$SO_LINE'; ";
    $query = mysqli_query($conn, $sql);
    if (!$query) {
        return $results = array();
    } else {
        $results = mysqli_fetch_array($query, MYSQLI_ASSOC);
    }

    return $results;
}

function materialNoCBSInfo($PO_NO, $SO_LINE)
{

    $results = array();

    $conn = getConnection();
    $table = "rfid_po_material_no_cbs_save";

    $sql = "SELECT * FROM $table WHERE `MN_PO_NO` = '$PO_NO' AND `MN_PO_SO_LINE` = '$SO_LINE'; ";
    $query = mysqli_query($conn, $sql);
    if (!$query) {
        return $results = array();
    } else {
        $results = mysqli_fetch_array($query, MYSQLI_ASSOC);
    }

    return $results;
}

function BillToCustomer($PO_INTERNAL_ITEM, $PO_SO_LINE)
{

    $BILL_TO_CUSTOMER = '';

    $conn = getConnection138();
    $table_vnso = "vnso";
    $table_vnso_total = "vnso_total";

    $PO_SO_LINE_ARR = explode('-', $PO_SO_LINE); //tách soline thành so & line
    $ORDER_NUMBER = $PO_SO_LINE_ARR[0];
    $LINE_NUMBER = $PO_SO_LINE_ARR[1];

    $sql_suffix = "  ITEM='$PO_INTERNAL_ITEM' AND ORDER_NUMBER='$ORDER_NUMBER' AND LINE_NUMBER='$LINE_NUMBER' LIMIT 0,1 ";
    $sql = "SELECT BILL_TO_CUSTOMER FROM $table_vnso WHERE $sql_suffix ";
    $result_bill_to = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result_bill_to) > 0) {
        $row_bill_to = mysqli_fetch_array($result_bill_to);
    } else {
        $sql = "SELECT BILL_TO_CUSTOMER FROM $table_vnso_total WHERE $sql_suffix ";
        $result_bill_to = mysqli_query($conn, $sql);
        $row_bill_to = mysqli_fetch_array($result_bill_to);
    }

    if (!empty($row_bill_to)) {
        $BILL_TO_CUSTOMER = !empty($row_bill_to['BILL_TO_CUSTOMER']) ? $row_bill_to['BILL_TO_CUSTOMER'] : '';
    }

    return $BILL_TO_CUSTOMER;
}

function remarkBillRBONike($rbo, $bill_to_customer, $internal_item )
{

    $remark = '';

    $array[] = array(
        'remark' => 'Dạng Cuộn',
        'rbo' => 'NIKE',
        'bill_to_customer' => array(
            'WORLDON',
            'QUANG VIET ENTERPRISE',
            'TINH LOI',
            'CCH TOP',
            'CRYSTAL SL GLOBAL'
        )
    );

    $array[] = array(
        'remark' => 'Dạng Sheet',
        'rbo' => 'NIKE',
        'bill_to_customer' => array(
            'UNITED SWEETHEARTS VIET NAM',
            'UNIPAX',
            'SKY LEADER LIMITED',
            'SNOGEN GREEN CO.,LTD',
            'DELTA GALIL VIETNAM',
            'GREENTECH HEADGEAR',
            'ESQUEL GARMENT MANUFACTURING (VIET NAM)'
        )
    );

    $array[] = array(
        'remark' => 'Dạng Xếp (Fanfold)',
        'rbo' => 'NIKE',
        'bill_to_customer' => array(
            'LIEN DOANH VINH HUNG'
        )
    );

    // 20220224 - email: Request add thêm thông tin remark vào tờ LSX RBO NIKE - RFID SB 2022
    $array2[] = array( 
        'internal_item' => array('4-230270-000-00','4-230271-000-00','4-230272-000-00','4-230273-000-00','4-230274-000-00','4-230276-000-00','4-230277-000-00','4-230278-000-00','4-230280-000-00','4-230281-000-00','4-230282-000-00','4-230283-000-00','4-230284-000-00','4-230285-000-00','4-230286-000-00','4-230288-000-00','4-230289-000-00','4-230290-000-00','4-230292-000-00','4-230293-000-00','4-230291-000-00','4-230297-000-00','4-230298-000-00','4-230299-000-00','4-230300-000-00','4-230302-000-00','4-230303-000-00','4-230304-000-00','4-230306-000-00','4-230307-000-00','4-230308-000-00','4-230309-000-00','4-230311-000-00','4-230312-000-00','4-230596-000-00','4-230597-000-00','4-230299-000-00','4-230301-000-00','4-230294-000-00','4-230310-000-00'),
        'remark' => 'Dạng Cuộn'
    );
    
    foreach ($array as $key => $value) {
        $rbo_check = $value['rbo'];
        if (strpos(strtoupper($rbo), $rbo_check) !== false) {

            $bill_to_customer_check = $value['bill_to_customer'];
            if (!empty($bill_to_customer_check)) {
                foreach ($bill_to_customer_check as $bill_to) {
                    if (strpos(strtoupper($bill_to_customer), $bill_to) !== false) {
                        $remark = $value['remark'];
                        break;
                    }
                }
            }

            // Kiểm tra lại điều kiện nếu có Item nằm trong danh sách ==> Dạng Cuộn
            foreach ($array2 as $value2 ) {
                $internal_item_arr = $value2['internal_item'];
                foreach ($internal_item_arr as $internal_item_check ) {
                    if ($internal_item == $internal_item_check ) {
                        $remark = $value2['remark'];
                        break;
                    }
                }
                
            }
        }


        if (!empty($remark)) {
            $remark = '<div style="color:blue;font-weight:bold;font-size:26px;background-color: #e6ffff;text-align:center;height:35px;border: 1px solid #99ccff;padding-top:2px;margin-bottom:5px;">' . $remark . '</div>';
        }
        
    }


    return $remark;
}

// 20220214 - email: Request add thêm thông tin remark vào tờ lệnh sản xuất - RFID SB
function remarkBillRBO($rbo, $bill_to_customer, $internal_item = null )
{
    $remark = '';
    $array[] = array(
        'rbo' => 'UNDER ARMOUR',
        'bill_to_customer' => 'CONG TY TNHH PEONY',
        'internal_item' => '4-232275-312-00',
        'remark' => 'Đóng 1 SKU 1 Roll với SL>=1000 pcs, SL<1000 pcs sẽ giao dạng Sheet hoạc Fanfold'
    );

    $array[] = array(
        'rbo' => 'UNDER ARMOUR',
        'bill_to_customer' => 'CONG TY TNHH PEONY',
        'internal_item' => '4-232274-312-00',
        'remark' => 'Đóng 1 SKU 1 Roll với SL>=1000 pcs, SL<1000 pcs sẽ giao dạng Sheet hoạc Fanfold'
    );
    
    foreach ($array as $value) {
        $rbo_check = $value['rbo'];
        if (strpos(strtoupper($rbo), $rbo_check) !== false) {
            $bill_to_customer_check = $value['bill_to_customer'];
            $internal_item_check = isset($value['internal_item']) ? $value['internal_item'] : null;

            if (strpos(strtoupper($bill_to_customer), $bill_to_customer_check) !== false) {
                if ($internal_item == $internal_item_check ) {
                    $remark = $value['remark'];
                    break;
                }
                
            }
            
        }
        

        
    }


    return $remark;
}

function remarkEAS($rbo, $ordered_item)
{

    $remark = '';
    // $rbo = htmlspecialchars_decode($rbo, ENT_QUOTES );
    if (strpos(strtoupper($rbo), 'H&M') !== false || strpos(strtoupper($rbo), 'H& M') !== false) {
        if (stripos($ordered_item, 'RT-01-DT') !== false) {
            $remark = '100% KIỂM EAS';
        } else if (stripos($ordered_item, 'RT-50-DT') !== false) {
            $remark = 'KIỂM EAS (5 Pcs ĐẦU + 5 Pcs CUỐI)';
        }
    }

    return $remark;
}

// sử dụng hiện thị thông tin remark DIT 
function remarkDITRefer($PO_FORM_TYPE, $internal_item)
{
    $conn = getConnection();
    $remark = '';
    if ($PO_FORM_TYPE == 'trim' || $PO_FORM_TYPE == 'trim_macy' || $PO_FORM_TYPE == 'pvh_rfid') {
        $sql = "SELECT OTHER_REMARK_2 FROM database_trim WHERE INTERNAL_ITEM = '$internal_item'; ";
        $result = toQueryArr($conn, $sql);
    } else {
        $sql = "SELECT OTHER_REMARK_2 FROM no_cbs WHERE INTERNAL_ITEM = '$internal_item'; ";
        $result = toQueryArr($conn, $sql);
    }

    if (!empty($result)) {
        $OTHER_REMARK_2 = trim(strtoupper($result['OTHER_REMARK_2']));

        // Nếu Other remark 2 có chữ DIT thì hiển thị other remark 2 lên tờ lệnh
        if (strpos($OTHER_REMARK_2, 'DIT') !== false) {
            $remark = !empty($OTHER_REMARK_2) ? $OTHER_REMARK_2 : '';
        }
    }

    return $remark;
}

function custPONumberFilter($CUST_PO_NUMBER, $PO_RBO)
{
    $result = 0;
    $RBOArr = array('DECATHLON', 'UNIQLO');

    if (!empty($CUST_PO_NUMBER)) {

        $custPO = trim($CUST_PO_NUMBER);
        $custPO = (strpos($custPO, ' ') !== false) ? str_replace(' ', '', $custPO) : $custPO;

        foreach ($RBOArr as $RBOCheck) {
            if (stripos($PO_RBO, $RBOCheck) !== false) {
                // Mặc định gán cho result = cột CUST_PO_NUMBER trong automail
                $result = $custPO;

                //Lấy số ký tự số, Nếu có ký tự / thì tách ra thành mảng từ ký tự /.  Lấy vị trí đầu tiên
                if (strpos($custPO, '/') !== false) {
                    // Trường hợp có dấu /
                    $custPODetached = explode('/', $custPO);
                    $result = $custPODetached[0];
                }
            }
        }




        // // Loại bỏ các ký tự khác, chỉ giữ lại ký tự số
        //     $result = preg_replace('/[^0-9]/', '', $result);
        // // check is number    
        //     $result = is_numeric($result)? $result : 0;

    }

    return $result;
}

function remarkDEC029($ordered_item)
{
    $result = '';
    if (strtoupper($ordered_item) == 'DEC029') {
        $result = $ordered_item;
    }

    return $result;
}

function remarkBillto($bill_to_customer)
{

    $remark = '';

    $array[] = array(
        'remark' => "ĐH WORLDON",
        'bill_to_customer' => array(
            'CONG TY TNHH GAIN LUCKY (VIET NAM)',
            'TOP ALWAYS (HK) INVESTMENTS',
            'DAQIAN TEXTILE(CAMBODIA)',
        )
    );

    foreach ($array as $key => $value) {
        $bill_to_customer_check = $value['bill_to_customer'];
        $remark_check = $value['remark'];

        foreach ($bill_to_customer_check as $bill_to) {
            if (strpos(strtoupper($bill_to_customer), $bill_to) !== false) {
                $remark = $remark_check;
                break;
            }
        }


        if (!empty($remark)) {
            $remark = '<div style="color:blue;font-weight:bold;font-size:24px;background-color: #e6ffff;text-align:center;height:35px;border: 1px solid #99ccff;padding-top:7px;">' . $remark . '</div>';
            break;
        }
    }


    return $remark;
}

function remarkPUMANA($rbo)
{
    $remark = '';
    $rbo = trim(strtoupper($rbo));

    if (strpos($rbo, 'PUMA AG') !== false) {
        $remark = '<div class="puma-na"> 1D,2D & EPC matching. Đầu và cuối mỗi SKU </div>';
    }

    return $remark;
}

function barcodeMaterial($rbo, $material_code)
{
    $material_code_barcode = '';

    $rboArr = array('PUMA AG', 'UNDER ARMOUR', 'NIKE', 'UNIQLO');
    // $rbo (strpos('&amp;', $rbo) ) ? str_replace('&amp;', '&', $rbo ) : $rbo;

    $rbo = trim(strtoupper($rbo));
    foreach ($rboArr as $rboCheck) {
        if (strpos($rbo, $rboCheck) !== false) {
            $material_code_barcode = !empty($material_code) ? '<div ><img style="height:35px;width:95%;" src="../../data/barcode.php?text=' . $material_code . '" /></div>' : '';
            break;
        }
    }

    return $material_code_barcode;
}

function remarkFRUIC($ORDER_TYPE_NAME)
{
    $remark = '';
    if (!empty($ORDER_TYPE_NAME)) {
        if (stripos($ORDER_TYPE_NAME, 'BNH') !== false) {
            $remark = '<div class="fru-ic-lh">FRU IC LH </div>';
        }
    }

    return $remark;
}

function remarkDangCuonOff($rbo, $bill_to_customer, $internal_item, $qty, $ship_to_customer )
{
    // init 
    $remark = '';
    // puma ag
    $data[] = array('rbo' => 'PUMA AG', 'bill_to_customer' => 'ALLIANCE ONE', 'internal_item' => '4-229280-000-00', 'qty' => 200, 'remark' => 'DẠNG CUỘN');
    // Beo.nguyen yêu cầu bỏ điều kiện này. mail: Re: PUMA RFID STICKER - ROLL FORM OR CUT SINGLE
    // // $data[] = array('rbo' => 'PUMA AG', 'bill_to_customer' => 'BIHQ PTE', 'internal_item' => '4-229280-000-00', 'qty' => 200, 'remark' => 'DẠNG CUỘN');

    // mail: RFID-SB Request add thêm thông tin vào lệnh sản xuất Đơn Hàng Puma
    $data[] = array('rbo' => 'PUMA AG', 'bill_to_customer' => 'QIYOU FOOTWEAR', 'internal_item' => '4-229281-000-00', 'qty' => 400, 'remark' => 'DẠNG CUỘN');
    // under armour
    $data[] = array('rbo' => 'UNDER ARMOUR', 'bill_to_customer' => '', 'internal_item' => '4-225603-310-00', 'qty' => 300, 'remark' => 'DẠNG CUỘN');
    $data[] = array('rbo' => 'UNDER ARMOUR', 'bill_to_customer' => '', 'internal_item' => '4-225712-310-00', 'qty' => 300, 'remark' => 'DẠNG CUỘN');

    // 20220222 - email: Request add thêm thông tin remark vào tờ lệnh sản xuất - RFID SB
    $data[] = array('rbo' => 'UNDER ARMOUR', 'bill_to_customer' => '', 'internal_item' => '4-232275-312-00', 'qty' => 300, 'remark' => 'DẠNG CUỘN');
    $data[] = array('rbo' => 'UNDER ARMOUR', 'bill_to_customer' => '', 'internal_item' => '4-232274-312-00', 'qty' => 300, 'remark' => 'DẠNG CUỘN');

    // check 
    foreach ($data as $value) {
        if (strpos($rbo, 'PUMA AG') !== false) {
            if ((strpos($rbo, $value['rbo']) !== false) && (strpos($bill_to_customer, $value['bill_to_customer']) !== false)
                && (strpos($internal_item, $value['internal_item']) !== false) && ($qty >= $value['qty'])
            ) {
                $remark = $value['remark'];
                break;
                
            }
        } else if (strpos($rbo, 'UNDER ARMOUR') !== false) {
            if ((strpos($rbo, $value['rbo']) !== false) && (strpos($internal_item, $value['internal_item']) !== false) && ($qty >= $value['qty'])) {
                $remark = $value['remark'];
                break;
            }
        }
    }

    // style
    if (!empty($remark)) {
        $remark = '<div style="color:blue;font-weight:bold;font-size:22px;background-color: #e6ffff;text-align:center;height:25px;border: 1px solid #99ccff;padding-top:3px;">' . $remark . '</div>';
    }

    // result
    return $remark;
}

function remarkPacking($rbo, $bill_to_customer, $internal_item, $qty )
{
    // init 
    $remark = '';
    // puma ag
    // Beo.nguyen yêu cầu bỏ điều kiện này. mail: [RFID-SB Remark Lệnh Sản Xuất 2022]
    // $data[] = array('rbo' => 'PUMA AG', 'bill_to_customer' => 'ALLIANCE ONE', 'internal_item' => '4-229280-000-00', 'qty' => 200, 'remark' => 'DẠNG CUỘN');
    // Beo.nguyen yêu cầu bỏ điều kiện này. mail: Re: PUMA RFID STICKER - ROLL FORM OR CUT SINGLE
    // // $data[] = array('rbo' => 'PUMA AG', 'bill_to_customer' => 'BIHQ PTE', 'internal_item' => '4-229280-000-00', 'qty' => 200, 'remark' => 'DẠNG CUỘN');

    // mail: RFID-SB Request add thêm thông tin vào lệnh sản xuất Đơn Hàng Puma
    $data[] = array('rbo' => 'PUMA AG', 'bill_to_customer' => 'QIYOU FOOTWEAR', 'internal_item' => '4-229281-000-00', 'qty' => 400, 'remark' => 'DẠNG CUỘN');
    // under armour
    $data[] = array('rbo' => 'UNDER ARMOUR', 'bill_to_customer' => '', 'internal_item' => '4-225603-310-00', 'qty' => 300, 'remark' => 'DẠNG CUỘN');
    $data[] = array('rbo' => 'UNDER ARMOUR', 'bill_to_customer' => '', 'internal_item' => '4-225712-310-00', 'qty' => 300, 'remark' => 'DẠNG CUỘN');

    // 20220222 - email: Request add thêm thông tin remark vào tờ lệnh sản xuất - RFID SB
    $data[] = array('rbo' => 'UNDER ARMOUR', 'bill_to_customer' => '', 'internal_item' => '4-232275-312-00', 'qty' => 300, 'remark' => 'DẠNG CUỘN');
    $data[] = array('rbo' => 'UNDER ARMOUR', 'bill_to_customer' => '', 'internal_item' => '4-232274-312-00', 'qty' => 300, 'remark' => 'DẠNG CUỘN');

    // 20220321 - email: [RFID-SB Remark Lệnh Sản Xuất 2022]
    $data[] = array('rbo' => 'PUMA AG', 'bill_to_customer' => '', 'internal_item' => '4-229280-000-00', 'qty' => 0, 'remark' => 'DẠNG SHEET');

    // check 
    foreach ($data as $value) {

        if (strpos($rbo, $value['rbo']) !== false ) {

            // Trường hợp KHÔNG có Bill to là điều kiện
            if (empty($value['bill_to_customer']) ) {
                if ( ($internal_item == $value['internal_item']) && ($qty >= $value['qty']) ) {
                    $remark = $value['remark'];
                    break;
                }

            } else {
                // 3 điều kiện cần kiểm tra: bill to, internal item, qty (trường hợp qty kiểm tra = 0 tức là k cần kiểm tra qty)
                if ( (strpos($bill_to_customer, $value['bill_to_customer']) !== false) && ($internal_item == $value['internal_item']) && ($qty >= $value['qty']) ) {
                    $remark = $value['remark'];
                    break;
                }
            }
            
        }

        
    }

    // style
    if (!empty($remark)) {
        $remark = '<div style="color:blue;font-weight:bold;font-size:22px;background-color: #e6ffff;text-align:center;height:25px;border: 1px solid #99ccff;padding-top:3px;">' . $remark . '</div>';
    }

    // result
    return $remark;
}

function remarkShipto($ship_to_customer)
{

    $remark = '';

    $array[] = array(
        'remark' => "Đóng riêng từng SOL mỗi Kiện",
        'ship_to_customer' => array(
            'POU YUEN'
        )
    );

    foreach ($array as $key => $value) {
        $ship_to_customer_check = $value['ship_to_customer'];
        $remark_check = $value['remark'];

        foreach ($ship_to_customer_check as $ship_to) {
            if (strpos(strtoupper($ship_to_customer), $ship_to) !== false) {
                $remark = $remark_check;
                break;
            }
        }


        if (!empty($remark)) {
            $remark = '<div style="color:blue;font-weight:bold;font-size:22px;background-color: #e6ffff;text-align:center;height:25px;border: 1px solid #99ccff;padding-top:3px;">' . $remark . '</div>';
            break;
        }
    }

    return $remark;
}

// Xử lý ngày CRD IC 
function getCRDICRemark($SOLine)
{
    $res = array();
    // check SOLine
    if (!empty($SOLine) && (strpos($SOLine, '-') !== false)) {
        // detach SOLine
        $SOLineArr = explode('-', trim($SOLine));
        $order_number = $SOLineArr[0];
        $line_number = $SOLineArr[1];

        // connect avery db
        $conn = getConnection('au_avery_lh_planning');
        // table
        $table = "planning_request_date_ic";

        // get data from 
        $result = mysqli_query($conn, "SELECT * FROM $table WHERE `order_number` ='$order_number' AND `line_number` ='$line_number';");
        if (mysqli_num_rows($result) > 0) {
            $result = mysqli_fetch_array($result, MYSQLI_ASSOC);
            $request_date = trim($result['request_date']);
            $remark = trim($result['remark']);

            $res = array(
                'remark' => $remark,
                'request_date' => $request_date
            );
        }

        // close db
        mysqli_close($conn);
    }
    // result
    return $res;
}

// 20211006 - mail: [RFID-SB] ADIDAS FOR CHINA MARKET REMARK - dành cho form ngang
function remarkInternalItem($internal_item ) 
{
    // init
        $remark = '';
    // array
        $array[] = array( 'internal_item' => '4-230413-000-00', 'remark' => "Kiểm tra 1D, 2D vs EPC. Đầu và cuối mỗi SKU <br> Kiểm tra số EPC vs dữ liệu encode. Đầu và cuối mỗi SKU");
        $array[] = array( 'internal_item' => '4-230416-000-00', 'remark' => "Kiểm tra 1D, 2D vs EPC. Đầu và cuối mỗi SKU <br> Kiểm tra số EPC vs dữ liệu encode. Đầu và cuối mỗi SKU");
        $array[] = array( 'internal_item' => '4-230495-000-00', 'remark' => "Kiểm tra 1D, 2D vs EPC. Đầu và cuối mỗi SKU <br> Kiểm tra số EPC vs dữ liệu encode. Đầu và cuối mỗi SKU");

        $array[] = array( 'internal_item' => '4-230816-000-00', 'remark' => "Kiểm tra 1D, 2D vs EPC. Đầu và cuối mỗi SKU <br> Kiểm tra số EPC vs dữ liệu encode. Đầu và cuối mỗi SKU");
        $array[] = array( 'internal_item' => '4-230815-000-00', 'remark' => "Kiểm tra 1D, 2D vs EPC. Đầu và cuối mỗi SKU <br> Kiểm tra số EPC vs dữ liệu encode. Đầu và cuối mỗi SKU");
        $array[] = array( 'internal_item' => '4-230817-000-00', 'remark' => "Kiểm tra 1D, 2D vs EPC. Đầu và cuối mỗi SKU <br> Kiểm tra số EPC vs dữ liệu encode. Đầu và cuối mỗi SKU");

    // check 
        foreach ($array as $key => $value ) {
            
            if ($internal_item == $value['internal_item'] ) {
                $remark = $value['remark'];
                break;
            }
        }

    // result
        return $remark;

}

// 20211013 - mail: [RFID_SB] Thêm thông tin lên trên lệnh SX - dành cho form đứng
function remarkInternalItem2($internal_item ) 
{
    // init
        $remark = '';

    // array
        $array[] = array( 'internal_item' => '4-231096-237-00', 'remark' => 'In Bởi RIP');
        $array[] = array( 'internal_item' => '4-231096-237-01', 'remark' => 'In Bởi RIP');
        $array[] = array( 'internal_item' => '4-231097-237-00', 'remark' => 'In Bởi RIP');
        $array[] = array( 'internal_item' => '4-231098-237-00', 'remark' => 'In Bởi RIP');
        $array[] = array( 'internal_item' => '4-231098-237-01', 'remark' => 'In Bởi RIP');

        $array[] = array( 'internal_item' => '4-231099-237-00', 'remark' => 'In Bởi RIP');
        $array[] = array( 'internal_item' => '4-231110-237-00', 'remark' => 'In Bởi RIP');
        $array[] = array( 'internal_item' => '4-231111-237-00', 'remark' => 'In Bởi RIP');
        $array[] = array( 'internal_item' => '4-231111-237-01', 'remark' => 'In Bởi RIP');
        $array[] = array( 'internal_item' => '4-231112-237-00', 'remark' => 'In Bởi RIP');

        $array[] = array( 'internal_item' => '4-231112-237-01', 'remark' => 'In Bởi RIP');
        $array[] = array( 'internal_item' => '4-231113-237-00', 'remark' => 'In Bởi RIP');

        // 20220124
        $array[] = array( 'internal_item' => '4-232577-23X-00', 'remark' => 'In Bởi RIP');
        $array[] = array( 'internal_item' => '4-232578-23X-00', 'remark' => 'In Bởi RIP');
        $array[] = array( 'internal_item' => '4-232578-23X-01', 'remark' => 'In Bởi RIP');
        $array[] = array( 'internal_item' => '4-232579-23X-00', 'remark' => 'In Bởi RIP');
        $array[] = array( 'internal_item' => '4-232580-23X-00', 'remark' => 'In Bởi RIP');

        $array[] = array( 'internal_item' => '4-232580-23X-01', 'remark' => 'In Bởi RIP');
        $array[] = array( 'internal_item' => '4-232581-23X-00', 'remark' => 'In Bởi RIP');
        $array[] = array( 'internal_item' => '4-232582-23X-00', 'remark' => 'In Bởi RIP');
        $array[] = array( 'internal_item' => '4-232582-23X-01', 'remark' => 'In Bởi RIP');
        $array[] = array( 'internal_item' => '4-232583-23X-00', 'remark' => 'In Bởi RIP');

        $array[] = array( 'internal_item' => '4-232584-23X-00', 'remark' => 'In Bởi RIP');
        $array[] = array( 'internal_item' => '4-232584-23X-01', 'remark' => 'In Bởi RIP');

    // check 
        foreach ($array as $key => $value ) {
            
            if ($internal_item == $value['internal_item'] ) {
                $remark = $value['remark'];
                break;
            }
        }

    // result
        return $remark;

}

function remarkRBOBillShip($rbo, $bill_to_customer, $ship_to_customer )
{
    $result = '';

    $data[] = array(
        'rbo' => 'WALMART',
        'bill_to_customer' => 'AE-A TRADING',
        'ship_to_customer' => 'PT.YOUNG WON INDONESIA',
        'remark' => 'Không Đóng FOC'
    );

    $data[] = array(
        'rbo' => 'UNDER ARMOUR',
        'bill_to_customer' => 'CTY TNHH PEONY',
        'ship_to_customer' => 'MAPLE CO LTD',
        'remark' => 'Đóng 1 SKU 1 Roll với SL >=1000 pcs, số lượng <1000 sẽ giao dạng sheet hoạc fanfold nhỏ hơn 1000pcs giao dạng sheet'
    );

    foreach ($data as $key => $value ) {
        $rbo_check = $value['rbo'];
        $bill_check = $value['bill_to_customer'];
        $ship_check = $value['ship_to_customer'];
        

        // Trường hợp này tách các If ra để sau này dễ quản lý
        if ( stripos($rbo, $rbo_check) !== false ) {
            if ( (stripos($bill_to_customer, $bill_check) !== false)  ) {
                if ( (stripos($ship_to_customer, $ship_check) !== false) ) {
                    $result = $value['remark'];
                    break;
                }
            }
        }


    }

    return $result;

}


//connect host
$conn       = getConnection();
$table      = "rfid_po_save";
$table_so = "rfid_po_soline_save";
$table_ink = "rfid_po_ink_no_cbs_save";
$table_mn = "rfid_po_material_no_cbs_save";
$table_mi = "rfid_po_material_ink_save";
$table_s = "rfid_po_size_cbs_save";
$table_m = "rfid_po_material_cbs_save";

//1. get data from database
$query_po = "SELECT PO_NO, PO_SO_LINE,PO_FORM_TYPE,PO_INTERNAL_ITEM,PO_ORDER_ITEM,PO_GPM,PO_RBO,PO_SHIP_TO_CUSTOMER,PO_CS,PO_QTY,PO_LABEL_SIZE,
                    PO_MATERIAL_CODE,PO_MATERIAL_DES,PO_MATERIAL_QTY,PO_INK_CODE,PO_INK_DES,PO_INK_QTY,PO_COUNT_SO_LINE,PO_SAVE_DATE,PO_PROMISE_DATE,
                    PO_REQUEST_DATE,PO_ORDERED_DATE,PO_MAIN_SAMPLE_LINE,PO_SAMPLE,PO_SAMPLE_15PCS,PO_MATERIAL_REMARK,PO_INK_REMARK,PO_PRINTED,PO_CREATED_BY,
                    PO_REMARK_1,PO_REMARK_2,PO_REMARK_3,PO_REMARK_4,PO_DATE_RECEIVED,PO_FILE_DATE_RECEIVED,PO_ORDER_TYPE_NAME,CUST_PO_NUMBER

            FROM $table WHERE PO_NO = '$PRINT_PO_NO' ORDER BY LENGTH(PO_SO_LINE),PO_SO_LINE ASC ";
$result_po = mysqli_query($conn, $query_po);
if (!$query_po) {
    echo "[ERROR 01]. Query sai";
    return false;
}

if (mysqli_num_rows($result_po) > 0) {
    //update print column
    $query_update = "UPDATE $table SET PO_PRINTED = 1 WHERE PO_NO ='$PRINT_PO_NO' ";
    $result_update = mysqli_query($conn, $query_update);

    $row_po = mysqli_fetch_array($result_po);
    //set data
    $PO_NO                  = !empty($row_po['PO_NO']) ? trim($row_po['PO_NO']) : "";
    $PO_SO_LINE             = !empty($row_po['PO_SO_LINE']) ? trim($row_po['PO_SO_LINE']) : "";
    $PO_FORM_TYPE           = !empty($row_po['PO_FORM_TYPE']) ? trim($row_po['PO_FORM_TYPE']) : "";
    $PO_INTERNAL_ITEM       = !empty($row_po['PO_INTERNAL_ITEM']) ? trim($row_po['PO_INTERNAL_ITEM']) : "";
    $PO_ORDER_ITEM          = !empty($row_po['PO_ORDER_ITEM']) ? trim($row_po['PO_ORDER_ITEM']) : "";
    $PO_RBO                 = !empty($row_po['PO_RBO']) ? trim($row_po['PO_RBO']) : "";
    $PO_RBO = htmlspecialchars_decode($PO_RBO, ENT_QUOTES);
    $PO_SHIP_TO_CUSTOMER    = !empty($row_po['PO_SHIP_TO_CUSTOMER']) ? trim($row_po['PO_SHIP_TO_CUSTOMER']) : "";
    $PO_CS                  = !empty($row_po['PO_CS']) ? trim($row_po['PO_CS']) : "";

    $PO_QTY                 = !empty($row_po['PO_QTY']) ? trim($row_po['PO_QTY']) : 0; //int

    $BARCODE_PO_QTY         = '<img style="height:120%" src="../../data/barcode.php?text=' . $PO_QTY . '" />';

    $PO_LABEL_SIZE          = !empty($row_po['PO_LABEL_SIZE']) ? trim($row_po['PO_LABEL_SIZE']) : "";
    $PO_MATERIAL_CODE       = !empty($row_po['PO_MATERIAL_CODE']) ? trim($row_po['PO_MATERIAL_CODE']) : "";
    $PO_MATERIAL_DES        = !empty($row_po['PO_MATERIAL_DES']) ? trim($row_po['PO_MATERIAL_DES']) : "";
    $PO_MATERIAL_QTY        = !empty($row_po['PO_MATERIAL_QTY']) ? (int)$row_po['PO_MATERIAL_QTY'] : 0; //int


    $PO_INK_CODE            = !empty($row_po['PO_INK_CODE']) ? trim($row_po['PO_INK_CODE']) : "";
    $PO_INK_DES             = !empty($row_po['PO_INK_DES']) ? trim($row_po['PO_INK_DES']) : "";
    $PO_INK_QTY             = !empty($row_po['PO_INK_QTY']) ? $row_po['PO_INK_QTY'] : 0; //int

    $CUST_PO_NUMBER    = !empty($row_po['CUST_PO_NUMBER']) ? trim($row_po['CUST_PO_NUMBER']) : "";
    $custPONumberFilter = custPONumberFilter($CUST_PO_NUMBER, $PO_RBO);
    $BARCODE_CUST_PO = !empty($custPONumberFilter) ? '<img style="height:95%" src="../../data/barcode.php?text=' . $custPONumberFilter . '" />' : '';

    $material_code_barcode = barcodeMaterial($PO_RBO, $PO_MATERIAL_CODE);
    $remarkPUMANA = remarkPUMANA($PO_RBO);

    // GET PACKING_INSTRUCTIONS
    $PACKING_INSTRUCTIONS = '';
    $SO_LINE_PAC_ARR = explode('-', $PO_SO_LINE);
    $PACKING_INSTRUCTIONS = getPACKING_INSTRUCTIONS($SO_LINE_PAC_ARR[0], $SO_LINE_PAC_ARR[1]);

    //NOTE: KHONG KIM LOAI
    $REMARK_KKL = '';
    $REMARK_KKL1 = '';

    if (strpos(strtoupper($PACKING_INSTRUCTIONS), 'KHONG KIM LOAI') !== false) {
        $REMARK_KKL = '<div style="float:right;color:blue;font-weight:bold;font-size:23px;background-color: #e6ffff;text-align:center;border: 1px solid #99ccff;" >&nbsp; KHÔNG KIM LOẠI </div>';
        $REMARK_KKL1 = '<div style="color:blue;font-weight:bold;font-size:23px;background-color: #e6ffff;text-align:center;border: 1px solid #99ccff;" >&nbsp; KHÔNG KIM LOẠI </div>';
    }

    // $remark_kkl_arr = remarkRBO($PO_RBO);

    if (strpos(strtoupper($PO_RBO), 'ADIDAS') !== false) {
        $REMARK_KKL = '<div style="float:right;color:blue;font-weight:bold;font-size:23px;background-color: #e6ffff;text-align:center;border: 1px solid #99ccff;" >&nbsp; KHÔNG KIM LOẠI </div>';
        $REMARK_KKL1 = '<div style="color:blue;font-weight:bold;font-size:23px;background-color: #e6ffff;text-align:center;border: 1px solid #99ccff;" >&nbsp; KHÔNG KIM LOẠI </div>';
    }

    // ĐH INKJET
    $DH_INKJET = '';
    $ink_code_check = array('KIARO D', 'QL800', 'INKJET');
    foreach ($ink_code_check as $checkInk) {
        if (strpos(strtoupper($PO_INK_CODE), $checkInk) !== false) {
            $DH_INKJET = 'DH_INKJET';
            $PO_INK_QTY = 0;
            break;
        }
    }


    // Remark DH EPSON: NIKE
    $DH_EPSON_ARR = [
        '4-221825-310-01',
        '4-221830-310-01',
        '4-221835-310-01',
        '4-221837-310-01',
        '4-221826-310-01',
        '4-221836-310-01',
        '4-221838-310-01',
        '4-221839-310-01',
        '4-221840-310-01',
        '4-221851-310-01',
        '4-221850-310-01',
        '4-221849-310-01',
        '4-221841-310-01',
        '4-221842-310-01',
        '4-221843-310-01',
        '4-221844-310-01',
        '4-221845-310-01'
    ];

    $REMARK_DH_EPSON = '';
    if (strpos(strtoupper($PO_INK_CODE), 'EPSON') !== false) {
        $REMARK_DH_EPSON = 'ĐH EPSON';
    } else {
        foreach ($DH_EPSON_ARR as $key => $DH_EPSON_VAL) {
            if ($PO_INTERNAL_ITEM == $DH_EPSON_VAL) {
                $REMARK_DH_EPSON = 'ĐH EPSON';
                break;
            }
        }
    }

    $REMARK_MACHINE = '';
    if (!empty($DH_INKJET)) {
        $REMARK_MACHINE = $DH_INKJET;
    } else if (!empty($REMARK_DH_EPSON)) {
        $REMARK_MACHINE = $REMARK_DH_EPSON;
    }

    // $REMARK_MACHINE = !empty($REMARK_MACHINE) ? "<div style='border-radius:8%;text-align:center;border:1px solid red;heigth:25px;padding:3px;font-size:20px;font-weight:bold;background-color:#e6ffff;color:red;width:70%;'>" . $REMARK_MACHINE . "</div>" : '';


    // Remark DH EPSON: RBO khác nên không trùng

    // ADIDAS FW 02-Dec-19
    $DH_ADIDAS_FW_ARR = [
        '1-202137-000-00',
        '4-217129-000-00',
        '4-215756-000-00',
        '1-255129-000-00',
        '1-243063-000-00',
        '1-243065-000-00',
        '1-242896-000-00',
        '1-242946-000-00',
        '1-243067-000-00',
        '1-243068-000-00',
        '1-259532-000-00',
        '1-240698-000-00',
        '1-231978-000-00',
        '1-259534-000-00'

    ];
    foreach ($DH_ADIDAS_FW_ARR as $key => $DH_ADIDAS_FW) {
        if ($PO_INTERNAL_ITEM == $DH_ADIDAS_FW) {
            $REMARK_DH_ADIDAS = 'ADIDAS FW';
            break;
        }
    }
    $REMARK_DH_ADIDAS = !empty($REMARK_DH_ADIDAS) ? $REMARK_DH_ADIDAS : '';
    // RFID RUSSIA 02-Dec-19
    $DH_RFID_RUSSIA_ARR = [
        '1-255128-000-01',
        '1-255129-000-01',
        '4-226571-000-00',
        '4-226571-000-01',
        '4-227918-000-00',
        // 2 item mới yêu cầu từ Beo.Nguyen (20220317 - email: "[RFID-SB Remark Lệnh Sản Xuất 2022]")
        '4-232630-000-00',
        '4-232743-000-00'

    ];
    foreach ($DH_RFID_RUSSIA_ARR as $key => $DH_RFID_RUSSIA) {
        if ($PO_INTERNAL_ITEM == $DH_RFID_RUSSIA) {
            $REMARK_DH_RFID_RUSSIA = 'RFID RUSSIA - DÁN STICKER';
            break;
        }
    }
    $REMARK_DH_RFID_RUSSIA = !empty($REMARK_DH_RFID_RUSSIA) ? $REMARK_DH_RFID_RUSSIA : '';
    // Đóng hàng 6000pcs/1 kiện 02-Dec-19
    $DONG_HANG_6000PCS_ARR = [
        'Myanmar Pou Chen Company Limited',
        'TSANG YIH COMPANY LIMITED',
        'Shyang Peng Cheng Co., Ltd',
        'SHYANG JHUO YUE CO.,LTD'

    ];
    $REMARK_DONG_HANG_6000PCS = '';
    foreach ($DONG_HANG_6000PCS_ARR as $key => $DONG_HANG_6000PCS) {
        if (strpos(strtoupper($PO_SHIP_TO_CUSTOMER), strtoupper($DONG_HANG_6000PCS)) !== false) {
            $REMARK_DONG_HANG_6000PCS = 'Đóng hàng 6000pcs/1 kiện';
            break;
        }
    }

    // remark theo internal item
        $remarkInternalItem = remarkInternalItem($PO_INTERNAL_ITEM);

    // 20211013- remark: RIP. mail: [RFID_SB] Thêm thông tin lên trên lệnh SX
        $remarkInternalItem2 = remarkInternalItem2($PO_INTERNAL_ITEM);

    


    // $REMARK_DONG_HANG_6000PCS = !empty($REMARK_DONG_HANG_6000PCS)?$REMARK_DONG_HANG_6000PCS:'';

    $PO_COUNT_SO_LINE       = !empty($row_po['PO_COUNT_SO_LINE']) ? $row_po['PO_COUNT_SO_LINE'] : 1; //int

    $PO_SAVE_DATE           = !empty($row_po['PO_SAVE_DATE']) ? trim($row_po['PO_SAVE_DATE']) : "";
    $PO_SAVE_DATE           = date('d-M-y', strtotime($PO_SAVE_DATE));
    $PO_PROMISE_DATE        = !empty($row_po['PO_PROMISE_DATE']) ? trim($row_po['PO_PROMISE_DATE']) : "";
    if ($PO_PROMISE_DATE == '1970-01-01' || empty($PO_PROMISE_DATE) ) {
        $PO_PROMISE_DATE = 'BLANK';
    } else {
        $PO_PROMISE_DATE        = date('d-M-y', strtotime($PO_PROMISE_DATE));
    }
    
    $PO_REQUEST_DATE        = !empty($row_po['PO_REQUEST_DATE']) ? trim($row_po['PO_REQUEST_DATE']) : "";
    $PO_REQUEST_DATE        = date('d-M-y', strtotime($PO_REQUEST_DATE));
    $PO_ORDERED_DATE        = !empty($row_po['PO_ORDERED_DATE']) ? trim($row_po['PO_ORDERED_DATE']) : "";
    $PO_ORDERED_DATE        = date('d-M-y', strtotime($PO_ORDERED_DATE));


    $PO_ORDER_TYPE_NAME     = !empty($row_po['PO_ORDER_TYPE_NAME']) ? trim($row_po['PO_ORDER_TYPE_NAME']) : "";

    $PO_MAIN_SAMPLE_LINE    = !empty($row_po['PO_MAIN_SAMPLE_LINE']) ? trim($row_po['PO_MAIN_SAMPLE_LINE']) : "";
    $PO_SAMPLE              = !empty($row_po['PO_SAMPLE']) ? $row_po['PO_SAMPLE'] : 0; //int
    //remark sample
    $PO_SAMPLE_TMP = (int)$PO_SAMPLE;
    if ($PO_SAMPLE_TMP == 1) {
        $REMARK_SAMPLE = 'ĐƠN CÓ MẪU';
    } else if ($PO_SAMPLE_TMP == 2) {
        $REMARK_SAMPLE = 'ĐƠN MẪU';
    } else {
        $REMARK_SAMPLE = 'ĐƠN KHÔNG CÓ MẪU';
    }

    $PO_SAMPLE_15PCS        = !empty($row_po['PO_SAMPLE_15PCS']) ? trim($row_po['PO_SAMPLE_15PCS']) : "";

    $PO_MATERIAL_REMARK     = !empty($row_po['PO_MATERIAL_REMARK']) ? trim($row_po['PO_MATERIAL_REMARK']) : "";
    $PO_INK_REMARK          = !empty($row_po['PO_INK_REMARK']) ? trim($row_po['PO_INK_REMARK']) : "";
    $PO_PRINTED             = !empty($row_po['PO_PRINTED']) ? trim($row_po['PO_PRINTED']) : "";

    //Order_type_name REPLACEMENT / FAST TRACK
    $PO_REMARK_1            = !empty($row_po['PO_REMARK_1']) ? trim($row_po['PO_REMARK_1']) : "";
    //packing instruction: Option1: NIKE => HÀNG LẺ / Rap voi thermal SO# -2.
    //Option2: RBO: POLO & Shipto May Tinh Loi=> Đóng gói theo màu cho KH Tinh Lợi
    // RBO: JC PENNEY => Mỗi SKU +1 pcs( Nhãn in), cuối cuộn cho thêm 10pcs( Nhãn trắng). JC PENNEY
    $PO_REMARK_2            = !empty($row_po['PO_REMARK_2']) ? trim($row_po['PO_REMARK_2']) : "";
    //packing instruction:  DONG HANG COMBINE CHUNG
    $PO_REMARK_3            = !empty($row_po['PO_REMARK_3']) ? trim($row_po['PO_REMARK_3']) : "";
    //Chua them vao
    $PO_REMARK_4            = !empty($row_po['PO_REMARK_4']) ? trim($row_po['PO_REMARK_4']) : "";
    $PO_CREATED_BY_OK          = !empty($row_po['PO_CREATED_BY']) ? trim($row_po['PO_CREATED_BY']) : "";

    $PO_DATE_RECEIVED       = !empty($row_po['PO_DATE_RECEIVED']) ? trim($row_po['PO_DATE_RECEIVED']) : "";
    $PO_FILE_DATE_RECEIVED  = !empty($row_po['PO_FILE_DATE_RECEIVED']) ? trim($row_po['PO_FILE_DATE_RECEIVED']) : "";

    //get PO_NO từ getPO_NO_FI_conn.php (xử lý hiển thị PO_NO)
    $FR_SHOW = ''; //mac dinh
    $PO_NO_FI = $PO_NO;
    include_once("getPO_NO_FI_conn.php");
    $PO_NO_FI = getPO_NO_FI($PO_NO);

    /* CHECK LIST
        | 1. Check list INKJET:     - Nếu Code mực =  Kiaro D hoặc QL800 hoặc số lượng mực = 0
        |                           - Nếu RBO = NIKE
        |                           - Nếu Code mực = INKJET hoặc EPSON 
        | 2. Check list khác: Tất cả trường hợp còn lại
    */
    // mặc định check list
    $check_list = false;

    // Check các đơn hàng inkjet và epson, Nếu đúng thì check_list = true để hiển check_list inkjet
    $ink_code_check_2 = array(
        'KIARO D',
        'QL800',
        'INKJET',
        'EPSON'
    );
    foreach ($ink_code_check_2 as $ink_check) {
        if (strpos(strtoupper($PO_INK_CODE), $ink_check) !== false) {
            $check_list = true;
            break;
        }
    }

    // Hoặc Số lượng mực = 0 hoặc RBO là NIKE thì check_list true
    if (strpos(strtoupper($PO_RBO), 'NIKE') !== false || $PO_INK_QTY == 0) {
        $check_list = true;
    }

    // FRU IC LH
    $remarkFRUIC = remarkFRUIC($PO_ORDER_TYPE_NAME);


    /* ********* SHORT LT: Hiển thị remark nếu thỏa đk (từng form) **************/
    //form ngang
    if ($PO_FORM_TYPE == 'trim' || $PO_FORM_TYPE == 'trim_macy' || $PO_FORM_TYPE == 'pvh_rfid') {
        $SHORT_LT = '';
        $SHORT_LT_COUNT = abs(strtotime($PO_REQUEST_DATE) - strtotime($PO_SAVE_DATE));
        $SHORT_LT_Y = floor($SHORT_LT_COUNT / (365 * 60 * 60 * 24));
        $SHORT_LT_M = floor(($SHORT_LT_COUNT - $SHORT_LT_Y * 365 * 60 * 60 * 24) / (30 * 60 * 60 * 24));
        $SHORT_LT_D = floor(($SHORT_LT_COUNT - $SHORT_LT_Y * 365 * 60 * 60 * 24 - $SHORT_LT_M * 30 * 60 * 60 * 24) / (60 * 60 * 24));

        //Kiểm tra số tháng //Số tháng cộng cho số ngày
        if ($SHORT_LT_M > 0) {
            $SHORT_LT_D = $SHORT_LT_M * 30 + $SHORT_LT_D;
        }

        //Check điều kiện short lt //ngày trong oracle là < 5
        if ($SHORT_LT_D < 5) {
            $SHORT_LT = 'SHORT LT';
        } else {
            $SHORT_LT = '';
        }
    } else { //form đứng: tat ca form dung

        //GET STANDARD_LT
        $query_no_cbs = "SELECT * FROM `no_cbs` WHERE `internal_item`='$PO_INTERNAL_ITEM' ORDER BY `CREATED_DATE_TIME` DESC LIMIT 0,1 ";
        $result_no_cbs = mysqli_query($conn, $query_no_cbs);
        if ($result_no_cbs === FALSE) {
            die(mysql_error());
        }
        $STANDARD_LT = 0;
        $OTHER_REMARK_1 = '';
        $OTHER_REMARK_2 = '';
        $OTHER_REMARK_3 = '';
        $OTHER_REMARK_4 = '';
        if (mysqli_num_rows($result_no_cbs) > 0) {
            $result_no_cbs = mysqli_fetch_array($result_no_cbs);
            $STANDARD_LT = !empty($result_no_cbs['STANDARD_LT']) ? $result_no_cbs["STANDARD_LT"] : 0;
            $STANDARD_LT = (int)$STANDARD_LT;

            $OTHER_REMARK_1 = trim($result_no_cbs['OTHER_REMARK_1']);
            $OTHER_REMARK_2 = trim($result_no_cbs['OTHER_REMARK_2']);
            $OTHER_REMARK_3 = trim($result_no_cbs['OTHER_REMARK_3']);
            $OTHER_REMARK_4 = trim($result_no_cbs['OTHER_REMARK_4']);
        }

        $SHORT_LT = '';
        $SHORT_LT_COUNT = abs(strtotime($PO_REQUEST_DATE) - strtotime($PO_SAVE_DATE));
        $SHORT_LT_Y = floor($SHORT_LT_COUNT / (365 * 60 * 60 * 24));
        $SHORT_LT_M = floor(($SHORT_LT_COUNT - $SHORT_LT_Y * 365 * 60 * 60 * 24) / (30 * 60 * 60 * 24));
        $SHORT_LT_D = floor(($SHORT_LT_COUNT - (($SHORT_LT_Y * 365 * 60 * 60 * 24) + ($SHORT_LT_M * 30 * 60 * 60 * 24))) / (60 * 60 * 24));

        //Kiểm tra số tháng .  //Số tháng cộng cho số ngày
        if ($SHORT_LT_M > 0) {
            $SHORT_LT_D = $SHORT_LT_M * 30 + $SHORT_LT_D;
        }

        //Check điều kiện short lt. //Kiểm tra STANDARD_LT (trong db no_cbs)
        if ($SHORT_LT_D < $STANDARD_LT) {
            $SHORT_LT = 'SHORT LT';
        }
    }

    if ($PO_COUNT_SO_LINE > 1) {
        $PO_SO_LINE_BAR = explode("-", $PO_SO_LINE);
        $SO_LINE_BARCODE  = $PO_SO_LINE_BAR[0];
        // Under Armour
        if (stripos($PO_RBO, 'UNDER ARMOUR') !== false) {
            $SO_LINE_BARCODE = $PO_SO_LINE . '.' . $PO_COUNT_SO_LINE;
        }
    } else {
        $SO_LINE_BARCODE = $PO_SO_LINE;
    }

    $BILL_TO_CUSTOMER = BillToCustomer($PO_INTERNAL_ITEM, $PO_SO_LINE);

    $remarkBillRBONike = remarkBillRBONike($PO_RBO, $BILL_TO_CUSTOMER, $PO_INTERNAL_ITEM );
    $remarkBillRBO = remarkBillRBO($PO_RBO, $BILL_TO_CUSTOMER, $PO_INTERNAL_ITEM);

    $remarkEAS = remarkEAS($PO_RBO, $PO_ORDER_ITEM);

    $remarkDEC029 = remarkDEC029($PO_ORDER_ITEM);

    $remarkBillto = remarkBillto($BILL_TO_CUSTOMER);

    $remarkUniqloEPC = remarkUniqloEPC($PO_RBO, $PO_ORDER_ITEM);

    $remarkPacking = remarkPacking($PO_RBO, $BILL_TO_CUSTOMER, $PO_INTERNAL_ITEM, $PO_QTY);


    $remarkShipto = remarkShipto($PO_SHIP_TO_CUSTOMER);

    // remark DIT 
    $remarkDIT = remarkDITRefer($PO_FORM_TYPE, $PO_INTERNAL_ITEM);
    $styleDIT = !empty($remarkDIT) ? "font-weight:bold;font-size:20px; text-align:left;color:red; border:1px solid blue; text-align:center;border-radius:10%;background-color:#e6ffff;" : "";

    // Trường hợp Binh nhắn, xử lý theo mail: Fwd: Invitation: R115 material consumption in Vn (nike) @ Thu May 21, 2020 10am - 11am (HKT) (coco.qiu1@ap.averydennison.com)
    $Material_Code_Special_Bill_To = array(
        '5-601865-310-00',
        '5-601865-310-00',
        '5-601865-310-00',
        '5-601865-310-00',
        '5-601863-310-00',
        '5-601866-310-00',
        '5-601865-310-00',
        '5-601865-310-00',
        '5-601864-310-00',
        '5-601864-310-00',
        '5-601864-310-00',
        '5-601864-310-00',
        '5-601864-310-00',
        '5-601864-310-00',
        '5-601863-310-00',
        '5-601865-310-00',
        '5-601864-310-00',
        '5-601864-310-00'
    );

    $internal_item_Special_Bill_To = array(
        '4-221835-310-01',
        '4-221837-310-01',
        '4-221826-310-01',
        '4-221836-310-01',
        '4-221825-310-01',
        '4-221840-310-01',
        '4-221838-310-01',
        '4-221838-310-01',
        '4-221851-310-01',
        '4-221850-310-01',
        '4-221849-310-01',
        '4-221841-310-01',
        '4-221842-310-01',
        '4-221843-310-01',
        '4-221830-310-01',
        '4-221839-310-01',
        '4-221844-310-01',
        '4-221845-310-01'
    );

    $internal_item_data_check = array();
    foreach ($internal_item_Special_Bill_To as $internal_item_Special_Bill_To_Check) {
        $internal_item_data_check['internal_item'][] = $internal_item_Special_Bill_To_Check;
    }

    foreach ($Material_Code_Special_Bill_To as $Material_Code_Special_Bill_To_Check) {
        $internal_item_data_check['material_code'][] = $Material_Code_Special_Bill_To_Check;
    }

    // print_r($internal_item_data_check); exit();
    $Special_Bill_To_array_2020 = array(
        'SNOGEN GREEN CO.,LTD',
        'Cong ty TNHH May Mac United Sweethearts',
        'SKY LEADER LIMITED'
    );
    $remark_material_Special_Bill_To = '';
    foreach ($Special_Bill_To_array_2020 as $Special_Bill_To_value) {
        if ($BILL_TO_CUSTOMER == $Special_Bill_To_value) {
            foreach ($internal_item_data_check['internal_item'] as $keyCheck => $internal_item_data_check_value) {
                if ($PO_INTERNAL_ITEM == $internal_item_data_check_value) {
                    $remark_material_Special_Bill_To = '<span style="color:blue;font-weight:bold;">Vật tư có thể sử dụng thay thế: ' . $internal_item_data_check['material_code'][$keyCheck] . '</span>';
                }
            }
        }
    }


    // CRD IC. Lưu ý đối với đơn hàng TRIM, TRIM MACY thì lấy line đầu tiên
    $CRDICRemark = getCRDICRemark($PO_SO_LINE);

    // 20220127 - mail: Request add thêm thông tin remark vào tờ lệnh sản xuất - RFID SB
    $remarkRBOBillShip = remarkRBOBillShip($PO_RBO, $BILL_TO_CUSTOMER, $PO_SHIP_TO_CUSTOMER);

    

    //set Barcode
    $BARCODE = '<img style="text-align:right;height:90%;width:50%;"  src="../../data/barcode.php?text=' . $SO_LINE_BARCODE . '" />';
    $BARCODE_H = '<img style="text-align:right;height:150%;width:90%;"  src="../../data/barcode.php?text=' . $SO_LINE_BARCODE . '" />';

    $BARCODE_PO_NO = '<img style="text-align:right;height:110%;width:70%;"  src="../../data/barcode.php?text=' . $PO_NO . '" />';

    $PO_GPM = !empty($row_po['PO_GPM']) ? trim($row_po['PO_GPM']) : "&nbsp;";

    //2. GET SOLINE:    CAST(SUBSTRING(SO_LINE, 10, 3) AS SIGNED )    ORDER BY CAST(SUBSTRING(SO_LINE, 10, 3) AS SIGNED ) ASC
    $query_so = "SELECT SO_PO_NO, SO_LINE, SO_INTERNAL_ITEM,SO_ORDER_ITEM,SO_PO_QTY,SO_WIDTH,SO_HEIGHT,REMARK_SO_COMBINE,GPM FROM $table_so WHERE SO_PO_NO = '$PO_NO' ORDER BY LENGTH(SO_LINE),SO_LINE ASC ";
    $result_so = mysqli_query($conn, $query_so);
    if (!$result_so) {
        echo "[ERROR 02]. Query sai (SO)";
        return false;
    }
    $num_so = mysqli_num_rows($result_po);


    //3. Dựa vào form_type, get data
    if ($PO_FORM_TYPE == 'rfid' || $PO_FORM_TYPE == 'ua_no_cbs') {
        $query_ink = "SELECT INK_NO, INK_ID, INK_PO_NO, INK_PO_SO_LINE, INK_CODE, INK_DES, INK_QTY, INK_REMARK FROM $table_ink WHERE INK_PO_NO = '$PO_NO' ORDER BY LENGTH(INK_PO_SO_LINE),INK_PO_SO_LINE  ";
        $result_ink = mysqli_query($conn, $query_ink);
        if (!$result_ink) {
            echo "[ERROR 03]. Query sai (INK)";
        }
        $num_ink = mysqli_num_rows($result_ink); //sử dụng cho while view data

        $query_mn = "SELECT MN_NO, MN_ID, MN_PO_NO, MN_PO_SO_LINE, MN_MATERIAL_CODE, MN_MATERIAL_DES, MN_MATERIAL_QTY, MN_MATERIAL_REMARK FROM $table_mn WHERE MN_PO_NO = '$PO_NO'  ";
        $result_mn = mysqli_query($conn, $query_mn);
        if (!$result_mn) {
            echo "[ERROR 04]. Query sai (MN)";
        }
        $num_mn = mysqli_num_rows($result_mn);

        // mail: RFID SB - Under armour - packing combine size
        //remark: if RBO is UNDER ARMOUR and bill_to_customer
        $PO_REMARK_5 = '';
        if (strpos(strtoupper($PO_RBO), 'UNDER ARMOUR') !== FALSE) {
            $BILL_TO_CUSTOMER_ARR = [
                'VICTORY FOOTWEAR LIMITED',
                'PURE RICH INTERNATIONAL CO., LTD',
                'Cong ty TNHH Starite International Vietnam'
            ];

            // Trường hợp các item này thì không remark
            $internal_item_ua_check = array('4-227346-238-00', '4-228081-238-00', '4-227407-238-00', '4-227404-238-00', '4-227405-238-00');
            if (!in_array($PO_INTERNAL_ITEM, $internal_item_ua_check)) {
                foreach ($BILL_TO_CUSTOMER_ARR as $key => $value) {
                    if (strpos(strtoupper($BILL_TO_CUSTOMER), strtoupper($value)) !== FALSE) {
                        $PO_REMARK_5 = 'KHÔNG CÂN TÁCH SIZE';
                    }
                }
            }
        }

        // check Form
        if (strpos($PO_REMARK_2, 'NIKE-WORLDON') !== false) {
            require_once(PATH_VIEW . "/print/printForm_NikeWorldon.php"); //PRINT

        } else if (strpos($PO_REMARK_2, 'NIKE-TINHLOI') !== false) {
            require_once(PATH_VIEW . "/print/printForm_NikeWorldon.php"); //PRINT
        } else {
            //view form đứng
            require_once(PATH_VIEW . "/print/printForm_Vertical.php"); //PRINT
        }
    } else if ($PO_FORM_TYPE == 'trim' || $PO_FORM_TYPE == 'trim_macy' || $PO_FORM_TYPE == 'pvh_rfid') {
        $query_mi_0 = "SELECT MI_NO, MI_ID, MI_PO_NO, MI_PO_SO_LINE, MI_MATERIAL_CODE, MI_MATERIAL_DES, MI_MATERIAL_QTY, MI_INK_CODE, MI_INK_DES, MI_INK_QTY FROM $table_mi WHERE MI_PO_NO = '$PO_NO' ORDER BY LENGTH(MI_PO_SO_LINE),MI_PO_SO_LINE ASC ";
        $result_mi = mysqli_query($conn, $query_mi_0);
        $result_mi2 = mysqli_query($conn, $query_mi_0); //load page 2
        $result_mi3 = mysqli_query($conn, $query_mi_0); //load page 3
        $result_mi4 = mysqli_query($conn, $query_mi_0); //load page 4
        if (!$result_mi) {
            echo "[ERROR 05]. Query sai (MI)";
        }
        $num_mi = mysqli_num_rows($result_mi);
        $num_mi2 = mysqli_num_rows($result_mi2);
        $num_mi3 = mysqli_num_rows($result_mi3);
        $num_mi4 = mysqli_num_rows($result_mi4);

        //view
        require_once(PATH_VIEW . "/print/printForm_Horizontal.php"); //PRINT

    } else if ($PO_FORM_TYPE == 'ua_cbs' || $PO_FORM_TYPE == 'cbs') {
        //get data size cbs
        $query_s = "SELECT S_NO, S_ID, S_PO_NO, S_PO_SO_LINE, S_SIZE, S_LABEL_ITEM, S_BASE_ROLL, S_QTY FROM $table_s WHERE S_PO_NO = '$PO_NO'  ";
        $result_s = mysqli_query($conn, $query_s);
        if (!$result_s) {
            echo "[ERROR 06]. Query sai (S)";
        }
        $num_s = mysqli_num_rows($result_s);

        //get data material cbs
        $query_m = "SELECT M_NO, M_ID, M_PO_NO, M_PO_SO_LINE, M_MATERIAL_CODE, M_MATERIAL_DES, M_MATERIAL_QTY, M_MATERIAL_REMARK FROM $table_m WHERE M_PO_NO = '$PO_NO'  ";
        $result_m = mysqli_query($conn, $query_m);
        if (!$result_m) {
            echo "[ERROR 07]. Query sai (M)";
        }
        $num_m = mysqli_num_rows($result_m);
        //view
        require_once(PATH_VIEW . "/print/printForm_Vertical.php"); //PRINT

    } else {
        echo "KHÔNG CÓ LOẠI FORM. VUI LÒNG KIỂM TRA LẠI ĐỂ PRINT";
    }
} //end if $num_po
else {
    echo "LỆNH SẢN XUẤT KHÔNG TỒN TẠI HOẶC CHƯA SAVE";
}
