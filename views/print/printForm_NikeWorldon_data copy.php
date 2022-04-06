<?php
    function checkLine($array) {
        $lineCombine = '';
        $tmp = '';
        if (!empty($array) ) {
            $count = count($array) - 1;

            foreach ($array as $k => $value ) {
                if ($k==0) {
                    if ($count==0) $lineCombine = $value;
                    else $lineCombine = $value . ".";
                } else {
                    if ($k < $count) {
                        // Nếu liên tiếp thì bỏ qua, nếu không thì lấy line này hiển thị. dạng line từ 1 đến 3 (1.3)
                        if ((int)$tmp = ( (int)$value - 1) ) {
                            $tmp = $value; // gán lại biến tạm
                            continue;
                        } else {
                            $lineCombine .= $value . "+";
                        }
                    } else { // cuối cùng
                        $lineCombine .= $value; 
                    }
                }
    
                // trường hợp nếu không liên tiếp thì cũng gán lại biến tạm
                    $tmp = $value;
                
            }
        }
        
        return $lineCombine;
    }
    

    if ($num_so > 0 && $num_ink>0 && $num_mn>0 ) {
        $solineData = mysqli_fetch_all($result_so, MYSQLI_ASSOC);
        $inkData = mysqli_fetch_all($result_ink, MYSQLI_ASSOC);
        $materialData = mysqli_fetch_all($result_mn, MYSQLI_ASSOC);

        $index = 0;
        $QTY_TOTAL_NIKE_WORLDON = 0;
        $MATERIAL_QTY_TOTAL_NIKE_WORLDON = 0;

        // Biến giữ lấy nội dung để làm barcdoe cho đơn hàng NIKE + WORLDON này
            $SOCombine = '';
            $lineRFID = array();
            $lineThermal = array();

        // Data để làm barcode cho NIKE TINHLOI
            $SOCombine_TinhLoi = (count($solineData) > 1 ) ? explode('-', $solineData[0]['SO_LINE'])[0] : $solineData[0]['SO_LINE'];

        foreach ($solineData as $key => $value ) {
            
            $index++;

            $SO_PO_NO = trim($value['SO_PO_NO']);
            $SO_LINE = trim($value['SO_LINE']);
            $SO_PO_QTY = (int)$value['SO_PO_QTY'];
            $SO_INTERNAL_ITEM = trim($value['SO_INTERNAL_ITEM']);
            $SO_ORDER_ITEM = trim($value['SO_ORDER_ITEM']);
            $SO_WIDTH = trim($value['SO_WIDTH']);
            $SO_HEIGHT = trim($value['SO_HEIGHT']);
            $REMARK_SO_COMBINE = trim($value['REMARK_SO_COMBINE']);
            $GPM = trim($value['GPM']);
            // xử lý hiển thị barcode cho NIKE+WORLDON
            // dạng: SO# 12345678 line 1 2 3 combine với thermal line 9 10 11
            // Barcode hiển thị: 12345678-1.3+9.11
            // Đơn HANGLE thì không có các line của Thermal
                $lineRFIDDetached = !empty($SO_LINE) ? explode('-', $SO_LINE) : '';
                $lineThermalDetached = !empty($REMARK_SO_COMBINE) ? explode('-', $REMARK_SO_COMBINE) : '';

                if (!empty($lineRFIDDetached) && !empty($lineThermalDetached) ) {
                    if ($key==0 ) {
                        $SOCombine = $lineRFIDDetached[0] . "-";
                        $lineRFID[] = $lineRFIDDetached[1];
                        if (isset($lineThermalDetached[1]) )  $lineThermal[] = $lineThermalDetached[1] ;
                    } 
                    
                    else {
                        $lineRFID[] = $lineRFIDDetached[1];
                        if (isset($lineThermalDetached[1]) )  $lineThermal[] = $lineThermalDetached[1] ;
                    }
                }
                
                

            $BARCODE_GPM_NIKEWORKDON = '<img style="height:90%" src="../../data/barcode.php?text='.$GPM.'" />';

            $QTY_TOTAL_NIKE_WORLDON += $SO_PO_QTY;

            // ink info
            $inkInfo = inkNoCBSInfo($SO_PO_NO, $SO_LINE);
            if (empty($inkInfo) ) {
                $INK_CODE = '';
                $INK_DES = '';
                $INK_QTY = 0;
                $INK_REMARK = '';
            } else {
                $INK_CODE = isset($inkInfo['INK_CODE']) ? trim($inkInfo['INK_CODE']) : '';
                $INK_DES = isset($inkInfo['INK_DES']) ? trim($inkInfo['INK_DES']) : '';
                $INK_QTY = isset($inkInfo['INK_QTY']) ? (int)$inkInfo['INK_QTY'] : 0;
                $PO_INK_REMARK = isset($inkInfo['INK_REMARK']) ? trim($inkInfo['INK_REMARK']) : '';
            }
            
            // material info
            $materialInfo = materialNoCBSInfo($SO_PO_NO, $SO_LINE);
            if (empty($materialInfo) ) {
                $MN_MATERIAL_CODE = '';
                $INK_DES = '';
                $INK_QTY = 0;
                $INK_REMARK = '';
            } else {
                $MN_MATERIAL_CODE = isset($materialInfo['MN_MATERIAL_CODE']) ? trim($materialInfo['MN_MATERIAL_CODE']) : '';
                $MN_MATERIAL_DES = isset($materialInfo['MN_MATERIAL_DES']) ? trim($materialInfo['MN_MATERIAL_DES']) : '';
                $MN_MATERIAL_QTY = isset($materialInfo['MN_MATERIAL_QTY']) ? (int)$materialInfo['MN_MATERIAL_QTY'] : 0;
                $PO_MATERIAL_REMARK = isset($materialInfo['MN_MATERIAL_REMARK']) ? trim($materialInfo['MN_MATERIAL_REMARK']) : '';

                $MATERIAL_QTY_TOTAL_NIKE_WORLDON += $MN_MATERIAL_QTY;
            }

            // view
            echo '<tr>';
                // 10 cot
                echo '<td class="Col Col-1">'.$index.'</td>';
                echo '<td class="Col Col-2">'.$SO_LINE.'</td>';
                echo '<td class="Col Col-3">'.$SO_INTERNAL_ITEM.'</td>';
                echo '<td class="Col Col-4">'.number_format($SO_PO_QTY).'</td>';
                echo '<td class="Col Col-5">'.$MN_MATERIAL_CODE.'</td>';

                echo '<td class="Col Col-6">'.number_format($MN_MATERIAL_QTY).'</td>';
                echo '<td class="Col Col-7">'.$INK_CODE.'</td>';
                echo '<td class="Col Col-8">'.$GPM.'</td>';
                echo '<td class="Col Col-9">'.$BARCODE_GPM_NIKEWORKDON.'</td>';
                echo '<td class="Col Col-10" style="font-weight:bold;">'.$REMARK_SO_COMBINE.'</td>';
            echo '</tr>';


        }

        // xử lý barcode NIKE + WORLDON/ TINHLOI
        include_once "code128.class.php";
            $BARCODE_COMBINE = '';   
            if (strpos($PO_REMARK_2,'NIKE-WORLDON') !== false ) {

                if (!empty($SOCombine) && !empty($lineRFID)  ) {
                    $lineRFIDCombine = checkLine($lineRFID);
                    if (!empty($lineThermal) ) {
                        $lineThermalCombine = checkLine($lineThermal);
                        $SOCombine .= $lineRFIDCombine . "+" .  $lineThermalCombine;
                    } else {
                        // HANGLE
                        $SOCombine .= $lineRFIDCombine;
                    }
                    
    
                }

            } else if (strpos($PO_REMARK_2,'NIKE-TINHLOI') !== false ) {
                $SOCombine = $SOCombine_TinhLoi;
            }

            // barcode SO# NIKE
            if (!empty($SOCombine) ) {
                $barcode = new phpCode128(str_replace("-B","",$SOCombine), 50, 'c:\windows\fonts\verdana.ttf', 11);
                $barcode->setBorderWidth(0);
                $barcode->setBorderSpacing(2);
                $barcode->setPixelWidth(2);
                $barcode->setEanStyle(false);
                $barcode->setShowText(false);
                $barcode->setAutoAdjustFontSize(true);
                $barcode->setTextSpacing(2);
                $barcode->saveBarcode('Barcodes//'.$SOCombine.'.png');
    
                $BARCODE_COMBINE = '<img style="text-align:right;height:150%;width:90%;" src="Barcodes/' .$SOCombine. '.png" />';
            }
            
    }
    