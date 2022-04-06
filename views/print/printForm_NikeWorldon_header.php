<?php
    
    $REMARK_RBO = '';
    if (strpos($PO_RBO, 'RYOHIN KEIKAKU') !==false ) {
        $REMARK_RBO = '<td><span style="color:red;text-align:center;font-size:16px;font-weight:bold; "><span style="width:100px;"> MUJI </span></span>>/td>';
    } else if (strpos($PO_RBO, 'NIKE') !==false ) {
        $remarkShipRBO = remarkShipRBO($PO_RBO, $PO_SHIP_TO_CUSTOMER);
        if (!empty($remarkShipRBO)) {
            $REMARK_RBO = $remarkShipRBO['remark'];
            $remark_SHIP_RBO_2 = $remarkShipRBO['remark_2'];
        }
    } else {
        // Trường hợp RBO = FAST RETAILING hoặc UNIQLO hoac khac
        $REMARK_RBO = remarkRBO($PO_RBO, $PO_ORDER_ITEM);
    }
    
    
    $REMARK_MACHINE = '';
    if (!empty($DH_INKJET) ) {
        $REMARK_MACHINE = $DH_INKJET;
    } else if (!empty($REMARK_DH_EPSON) ) {
        $REMARK_MACHINE = $REMARK_DH_EPSON;
    }

    // remark 2
        $PO_REMARK_2 = (!empty($PO_REMARK_2) ) ? '<td style="text-align:right;"><span style="font-weight:bold;font-size:24px;">' . $PO_REMARK_2 . '</span></td>' : '';
    
    // barcode
        $BARCODE_H = 'RFID <br />' . $BARCODE_H;

    // view table header 1 
    echo '<table style="width:100%; ">';
        echo '<tr>';
            echo '<td><span style="color:blue;text-align:left;font-size:10px;">GROUP: THERMAL/RFID</span></td>';
            echo '<td><span style="font-weight:bold;text-align:left;font-size:30px;text-transform:uppercase;">' . $SHORT_LT . '</span></td>' ;
            // 1 td
            echo (!empty($REMARK_RBO) ) ? $REMARK_RBO : '';
            // echo '<td id="dh_inkjet"><span style="color:red;text-align:center;font-size:16px;font-weight:bold; ">' . $REMARK_MACHINE . '</span></td>';
            echo '<td ><span id="dh_test" style="color:red;text-align:center;font-size:16px;font-weight:bold; "> </span></td>';
            // 1 td
            // echo (!empty($PO_REMARK_2) ) ? $PO_REMARK_2 : '';
            echo '<td id="dh_inkjet"><span style="color:red;text-align:center;font-size:16px;font-weight:bold; "></span></td>';
        echo '</tr>';
        echo '<tr>';
            echo '<td colspan=4 style="height:15px;color:red;text-align:center; background-color:#66ffcc;"><h2 style="padding-top:5px;height:15px;">LỆNH SẢN XUẤT/PRODUCTION ORDER</h2></td>';
            echo '<td style="text-align:left; font-size:16px;background-color:#66ffcc;width:6%;"><span style="font-weight:bold;font-size:18px;" >'.$PO_COUNT_SO_LINE . '</span>&nbsp;line </td>';
        echo '</tr>';
    
    echo '</table>';

    echo '<div id="p-header">';
        echo '<table style="width:100%">';
            echo '<tr>';
                echo '<td class="header-0" class="No" style="width:10%;height:40px;font-weight:bold;font-size:20px; ">No: &nbsp;</td>';
                echo '<td class="header" style="text-align:left;width:32%;font-size:20px; ">' . $PO_NO_FI . '</td>';
                echo '<td class="header-0" style="width:10%;"> &nbsp;</td>';
                echo '<td style="font-size:14px;width:12%;">Create by: &nbsp;</td>';
                echo '<td style="font-weight:bold; font-size:14px;width:12%;color:red; ">' . $PO_CREATED_BY_OK . '</td>';
                // echo '<td id="barcode_combine" rowspan=2 class="header" style="text-align:center; font-size:24px; ">' . $BARCODE_H . '</span></td>';
                
                // load JS barcode combine NIKE + WORLDON
                echo '<td id="barcode_combine" class="barcode_combine" rowspan=2 class="header" style="text-align:center; font-size:24px;height:30px;"></td>';
            echo '</tr>';

            echo '<tr>';
                echo '<td class="header-0">Ngày làm lệnh:&nbsp;</td>';
                echo '<td class="header">'.$PO_SAVE_DATE.'</td>';
                echo '<td class="header-0"> &nbsp;</td>';
                echo '<td class="header-0">CS Name: &nbsp;</td>';
                echo '<td class="header">' . $PO_CS . '</td>';
            echo '</tr>';

            echo '<tr>';
                echo '<td class="header-0">Order date: &nbsp;</td>';
                echo '<td class="header">'.$PO_ORDERED_DATE.'</td>';
                echo '<td class="header-0">RBO: &nbsp;</td>';
                echo '<td colspan=3 class="header">'.$PO_RBO.'</td>';
            echo '</tr>';

            echo '<tr>';
                echo '<td class="header-0" style="font-size:16px;font-weight:bold;">Promise date:</td>';
                echo '<td class="header" style="font-size:15px;font-weight:bold;">'.$PO_PROMISE_DATE.'</td>';
                echo '<td class="header-0">Ship to: &nbsp;</td>';
                $font_Worldon = (strpos(strtoupper($PO_SHIP_TO_CUSTOMER), 'WORLDON')!==false) ? '20px;' : '';
                echo '<td colspan=3 class="header" style="font-size:' .$font_Worldon. '">' .$PO_SHIP_TO_CUSTOMER. '</td>';
            echo '</tr>';

            echo '<tr>';
                echo '<td class="header-0" style="font-size:16px;font-weight:bold;">Request date:</td>';
                echo '<td class="header" style="font-size:18px;font-weight:bold;">'.$PO_REQUEST_DATE.'</td>';
                echo '<td class="header-0"> &nbsp;</td>';
                $PO_DATE_RECEIVED = ($PO_DATE_RECEIVED != '1970-01-01') ? date('d-M-y',strtotime($PO_DATE_RECEIVED)) : '';
                if ($PO_FILE_DATE_RECEIVED == 1 ) {
                    $PO_FILE_DATE_RECEIVED = '1';
                } else if ($PO_FILE_DATE_RECEIVED == 2 ) {
                    $PO_FILE_DATE_RECEIVED = '2&3';
                } else if ($PO_FILE_DATE_RECEIVED == 4){
                    $PO_FILE_DATE_RECEIVED = '4';
                }
                
                echo '<td class="header">&nbsp;</td>';
                echo '<td class="header-0" style="width:10%; "  >Barcode NO:</td>';
                echo '<td colspan=1 class="header" style="text-align:center;height:20px;">'.$BARCODE_PO_NO.'</td>';

            echo '</tr>';

        echo '</table>';
    
    echo '</div>';

    echo '<hr  width="100%" align="center" />';

?>

