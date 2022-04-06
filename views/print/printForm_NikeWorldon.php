<?php
    
    if( !isset($_COOKIE["VNRISIntranet"]) ) header('Location: login.php',false);//check login
    if ($PO_FORM_TYPE == 'trim') {$title_print = "TRIM PRINTER";} 
    else if ($PO_FORM_TYPE == 'pvh_rfid') {$title_print = "PVH PRINTER";} 
    else if ($PO_FORM_TYPE == 'trim_macy') {$title_print = "MACY PRINTER";} 
    else if ($PO_FORM_TYPE == 'ua_cbs' || $PO_FORM_TYPE == 'cbs' ) {$title_print = "CBS PRINTER";} 
    else if ($PO_FORM_TYPE == 'ua_no_cbs' ) {$title_print = "NO CBS PRINTER";} 
    else if ($PO_FORM_TYPE == 'rfid' ) {$title_print = "RFID PRINTER";} else {$title_print = " ... PRINTER";} 

    // html 
    echo '<!DOCTYPE html>';
    echo '<html>';
    echo '<head>';
        echo '<title>' . $title_print . '</title>';
        echo '<meta name="google" content="notranslate" />';
        echo '<link rel="stylesheet" href="../../assets/css/print/print_NikeWorldon.css">';
        echo '<link rel="stylesheet" href="../../assets/css/print/all_style.css">';
        echo '<script src="../../assets/JS/jquery-1.10.1.min.js"></script>';
        echo '<script src="jquery.rotate.js"></script>';
    echo '</head>';
    echo '<body>';

        // load header 1
        include_once ("printForm_NikeWorldon_header.php");
        
        // load table data  show
        echo '<div id="container1">';
            echo '<table style="width:100%;">';
                echo '<tr>';
                    // 10 cot
                    echo '<th class="Col0" rowspan=2>No &nbsp;</th>';
                    echo '<th class="Col0" colspan=3>Order infos &nbsp;</th>';
                    echo '<th class="Col0" colspan=2>Paper infos &nbsp;</th>';
                    echo '<th class="Col0" colspan=1>Ink infos &nbsp;</th>';
                    echo '<th class="Col0" colspan=3>Others</th>';
                echo '</tr>';

                echo '<tr>';
                    // 10 cot
                    echo '<th class="Col0">SO LINE &nbsp;</th>';
                    echo '<th class="Col0">Internal Item &nbsp;</th>';
                    echo '<th class="Col0">QTY (PCS) &nbsp;</th>';
                    echo '<th class="Col0">Material code &nbsp;</th>';

                    echo '<th class="Col0">Quantity<br/>(Paper-EA) &nbsp;</th>';
                    echo '<th class="Col0">Ink code &nbsp;</th>';
                    echo '<th class="Col0">GPM</th>';
                    echo '<th class="Col0">Barcode GPM</th>';
                    echo '<th class="Col0">Note</th>';
                echo '</tr>';

                // load data 
                    include_once ("printForm_NikeWorldon_data.php");

                // check qty total
                    if ($QTY_TOTAL_NIKE_WORLDON !== (int)$PO_QTY ) {
                        $QTY_TOTAL_NIKE_WORLDON = '????';
                    } else {
                        $QTY_TOTAL_NIKE_WORLDON = number_format($QTY_TOTAL_NIKE_WORLDON);
                    }


                echo '<tr>';
                    echo '<td class="Col" colspan=3 style="font-weight:bold;color:red;  " >QTY TOTAL: &nbsp;</td>';
                    echo '<td class="Col Col-4" style="font-weight:bold;color:red;font-size:16px;  ">' . $QTY_TOTAL_NIKE_WORLDON . '</td>';
                    echo '<td class="Col Col-5" style="font-weight:bold;color:red;  ">&nbsp;</td>';
                    echo '<td class="Col Col-5" style="font-weight:bold;color:red;">'.number_format($MATERIAL_QTY_TOTAL_NIKE_WORLDON).'</td>';
                    echo '<td class="Col Col-5" colspan=4 style="font-weight:bold;color:red;  ">&nbsp;</td>';
                echo '</tr>';
        
                echo '<tr>';
                    echo '<td class="" colspan=4 style="font-weight:bold;color:red;text-align:right;  " >' . $BARCODE_PO_QTY . '</td>';
                    echo '<td class=" Col-5" colspan=4 style="font-weight:bold;color:red;background-color:while;  ">&nbsp;</td>';
                    // echo '<td class=" Col-5" colspan=1 style="font-weight:bold;color:red;background-color:while;  ">Barcode NO:</td>';
                    // echo '<td class=" Col-5" colspan=1 style="font-weight:bold;color:red;background-color:while;  ">Barcode đây</td>';
                echo '</tr>';

            echo '</table>';
        echo '</div>';

        echo '<hr   width="99%" border:#f5f5f5 align="center" />';

        echo '<div id="container2" style="width:99%;font-weight:bold;color:blue; " >';
            echo '<div class="" style="float:left; width:50%; ">';
                echo !empty($PO_MATERIAL_REMARK) ? "<div style='width:90%;'> $PO_MATERIAL_REMARK </div> " : '';
                echo !empty($PO_INK_REMARK) ? "<div style='width:90%;'> $PO_INK_REMARK </div> " : '';
            echo '</div>';
            echo '<div class="" style="float:right; width:49.5%;">';
                echo (!empty($PO_REMARK_3)) ? "<div style='width:100%;margin-top:1px;' > $PO_REMARK_3 </div> " : "";
                echo (!empty($PO_REMARK_4)) ? "<div class='remark-4'> $PO_REMARK_4 </div> " : "";
                echo (!empty($PO_REMARK_5)) ? "<div style='width:100%;margin-top:1px;' > $PO_REMARK_5 </div> " : "";
            echo '</div>';

            echo (!empty($PACKING_INSTRUCTIONS)) ? "<div style='width:100%;margin-top:1px;' > $PACKING_INSTRUCTIONS </div> " : "";
            echo (!empty($remarkFRUIC)) ? "<div style='text-align:center;border:1px solid red;heigth:30px;padding:2px;font-size:20px;font-weight:bold;background-color:#e6ffe6;color:red;'> $remarkFRUIC </div> " : "";
            
        echo '</div>';

        require_once ('printForm_macy_page_5.php');

        // check list page
        require_once ('printForm_check_list.php');

    echo '</body>';
    echo '</html>';
            
?>

<script>
    $("#barcode_combine").html('<?php echo $BARCODE_COMBINE ; ?>');
</script>