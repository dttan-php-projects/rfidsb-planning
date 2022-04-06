<!-- print_Vertical: FORM NGANG (CHIỀU NGANG) -->
<?php if( !isset($_COOKIE["VNRISIntranet"]) ) header('Location: login.php',false);//check login ?>
<?php 
  if ($PO_FORM_TYPE == 'trim') {$title_print = "TRIM PRINTER";} 
  else if ($PO_FORM_TYPE == 'pvh_rfid') {$title_print = "PVH PRINTER";} 
  else if ($PO_FORM_TYPE == 'trim_macy') {$title_print = "MACY PRINTER";} 
  else if ($PO_FORM_TYPE == 'ua_cbs' || $PO_FORM_TYPE == 'cbs' ) {$title_print = "CBS PRINTER";} 
  else if ($PO_FORM_TYPE == 'ua_no_cbs' ) {$title_print = "NO CBS PRINTER";} 
  else if ($PO_FORM_TYPE == 'rfid' ) {$title_print = "RFID PRINTER";} else {$title_print = " ... PRINTER";} 

?>   

<!DOCTYPE html>
<html>
<head>
  <title><?php echo $title_print; ?></title>
  <meta name="google" content="notranslate" />
  <link rel="stylesheet" href="../../assets/css/print/print_Horizontal.css">
  <link rel="stylesheet" href="../../assets/css/print/all_style.css">
  <script src="../../assets/JS/jquery-1.10.1.min.js"></script>
  <script src="jquery.rotate.js"></script>
</head>
<body>
<!-- **************************************************************** -->
<!-- load top header -->
<?php 
  include ("printForm_Horizontal_header.php");
?>
<!-- **************************************************************** -->

<!-- container 1 -->
<?php 
  //1. ************** macy
  if ($PO_FORM_TYPE == 'trim' || $PO_FORM_TYPE == 'pvh_rfid' ) {
    echo '<div id="container1">';
      echo '<table>';
        echo '<tr>';
          // 9 cot
          echo '<th class="Col0" rowspan=2>No &nbsp;</th>';
          echo '<th class="Col0" colspan=3>Order infos &nbsp;</th>';
          echo '<th class="Col0" colspan=2>Paper infos &nbsp;</th>';
          echo '<th class="Col0" colspan=2>Ink infos &nbsp;</th>';

        echo '</tr>';

        echo '<tr>';
          // 9 cot
          echo '<th class="Col0">SO LINE &nbsp;</th>';
          echo '<th class="Col0">Internal Item &nbsp;</th>';
          echo '<th class="Col0">QTY (PCS) &nbsp;</th>';
          echo '<th class="Col0">Material code &nbsp;</th>';

          echo '<th class="Col0">Quantity (EA) &nbsp;</th>';
          echo '<th class="Col0">Ink code &nbsp;</th>';
          echo '<th class="Col0">Ink Description &nbsp;</th>';


        echo '</tr>';

        if ($num_mi > 0) {
          $i=0;
          $MI_MATERIAL_QTY_TOTAL = $MI_INK_QTY_TOTAL = 0;
          $SO_PO_QTY_TOTAL_TRIM_PVH = 0;
          while ($row_mi = mysqli_fetch_array($result_mi) ) {

            //GET MI                      
              $MI_PO_NO    = !empty($row_mi['MI_PO_NO'])?trim($row_mi['MI_PO_NO']):"";
              $MI_PO_SO_LINE        = !empty($row_mi['MI_PO_SO_LINE'])?trim($row_mi['MI_PO_SO_LINE']):"";
              $MI_MATERIAL_CODE     = !empty($row_mi['MI_MATERIAL_CODE'])?trim($row_mi['MI_MATERIAL_CODE']):"";               
              $MI_MATERIAL_DES      = !empty($row_mi['MI_MATERIAL_DES'])?trim($row_mi['MI_MATERIAL_DES']):"";
              $MI_MATERIAL_QTY      = !empty($row_mi['MI_MATERIAL_QTY'])?trim($row_mi['MI_MATERIAL_QTY']):0;
              $MI_MATERIAL_QTY      = (int)$MI_MATERIAL_QTY;

              $MI_MATERIAL_QTY_TOTAL += $MI_MATERIAL_QTY;
              
              $MI_INK_CODE          = !empty($row_mi['MI_INK_CODE'])?trim($row_mi['MI_INK_CODE']):"";
              $MI_INK_DES           = !empty($row_mi['MI_INK_DES'])?trim($row_mi['MI_INK_DES']):"";
              $MI_INK_QTY           = !empty($row_mi['MI_INK_QTY'])?trim($row_mi['MI_INK_QTY']):0;
              $MI_INK_QTY           = (int)$MI_INK_QTY;
              
              $MI_INK_QTY_TOTAL     += $MI_INK_QTY;

              //GET INTERNAL_ITEM, SO_QTY, FROM SOGRID
              $query_soline = "SELECT SO_INTERNAL_ITEM,SO_ORDER_ITEM,SO_PO_QTY,SO_WIDTH,SO_HEIGHT  FROM $table_so WHERE SO_PO_NO = '$MI_PO_NO' AND SO_LINE = '$MI_PO_SO_LINE' ";
              
              $result_soline = mysqli_query($conn, $query_soline);
              if (!$result_soline) {
                  echo "[ERROR V]. Query sai (SO)";
                  return false;
              }
              $num_soline = mysqli_num_rows($result_soline);
              if ($num_soline > 0) {
                  
                  $row_so = mysqli_fetch_array($result_soline);
                  ////GET DATA FROM SO GRID
                  $SO_INTERNAL_ITEM = !empty($row_so['SO_INTERNAL_ITEM'])?trim($row_so['SO_INTERNAL_ITEM']):"";

                  // remark theo internal item
                  if (empty($remarkInternalItem) ) {
                    $remarkInternalItem = remarkInternalItem($SO_INTERNAL_ITEM);
                  }

                  $SO_ORDER_ITEM    = !empty($row_so['SO_ORDER_ITEM'])?trim($row_so['SO_ORDER_ITEM']):"";
                  $SO_PO_QTY        = !empty($row_so['SO_PO_QTY'])?trim($row_so['SO_PO_QTY']):0;
                  $SO_PO_QTY        = (int)$SO_PO_QTY;
                  
                  $SO_PO_QTY_TOTAL_TRIM_PVH  += $SO_PO_QTY;
                  // $SO_WIDTH         = !empty($row_so['SO_WIDTH'])?trim($row_so['SO_WIDTH']):"";
                  // $SO_HEIGHT        = !empty($row_so['SO_HEIGHT'])?trim($row_so['SO_HEIGHT']):"";
              }//if soline

              $i++;

              echo '<tr>';
                echo '<td class="Col Col-1">' . $i . '</td>' ;
                echo '<td class="Col Col-2" style="font-size:15px;font-weight:bold;">' . $MI_PO_SO_LINE . '</td>';
                echo '<td class="Col Col-3">' . $SO_INTERNAL_ITEM . '</td>';
                echo '<td class="Col Col-4" style="font-size:15px;">'  . number_format($SO_PO_QTY) . '</td>';
                echo '<td class="Col Col-5" style="font-size:15px;font-weight:bold;">' . $MI_MATERIAL_CODE . '</td>' ;
                echo '<td class="Col Col-6" style="font-size:15px;font-weight:bold;">' . number_format($MI_MATERIAL_QTY) . '</td>';
                echo '<td class="Col Col-7" style="font-size:15px;font-weight:bold;">' . $MI_INK_CODE . '</td>';
                echo '<td class="Col Col-8" style="font-size:10px;font-weight:normal;">' . $MI_INK_DES . '</td>';

              echo '</tr>';
              
          }//end while
        
        } //end if 

        echo '<tr>';

          echo '<td class="Col" colspan=3 style="font-weight:bold;color:red;  " >QTY TOTAL: &nbsp;</td>';
          echo '<td class="Col Col-4" style="font-weight:bold;color:red;font-size:16px;  ">' . number_format($SO_PO_QTY_TOTAL_TRIM_PVH) . '</td>';
          echo '<td class="Col Col-5" style="font-weight:bold;color:red;  ">&nbsp;</td>';
          echo '<td class="Col Col-6" style="font-weight:bold;color:red;font-size:16px;  "><!-- $MI_MATERIAL_QTY_TOTAL; --> </td>';
          echo '<td class="Col Col-7" style="font-weight:bold;color:red; ">&nbsp;</td>';
          echo '<td class="Col Col-8" style="font-weight:bold;color:red;font-size:16px; "><!-- $MI_INK_QTY_TOTAL; --></td>';

        echo '</tr>';

        echo '<tr>';

          echo '<td class="" colspan=4 style="font-weight:bold;color:red;text-align:right; height:20px; " >' . $BARCODE_PO_QTY . '</td>';
          echo '<td class=" Col-5" style="font-weight:bold;color:red;background-color:while;  ">&nbsp;</td>';
          echo '<td class=" Col-6" style="font-weight:bold;color:red;background-color:while;   ">&nbsp;</td>';
          echo '<td class=" Col-7" style="font-weight:bold;color:red;background-color:while;  ">&nbsp;</td>';
          echo '<td class=" Col-8" style="'.$styleDIT.'">&nbsp; '.$remarkDIT.'</td>';

        echo '</tr>';
      echo '</table>';
    echo '</div>';

    echo '<hr   width="99%" border:#f5f5f5 align="center" />';    

    echo '<div id="container2" style="width:100%;font-weight:bold;color:blue;min-height:100px; " >';
      echo '<div style="float:left;width:49.5%;" > ';
      
        echo !empty($PO_MATERIAL_REMARK) ? "<div style='width:90%;'> $PO_MATERIAL_REMARK </div> " : '';
        echo !empty($PO_INK_REMARK) ? "<div style='width:90%;'> $PO_INK_REMARK </div> " : '';
        echo !empty($PO_REMARK_1) ? "<div style='width:90%;'> $PO_REMARK_1 </div> " : '';
        echo !empty($REMARK_SAMPLE) ? "<div style='width:90%;font-size:20px;color:red;'> $REMARK_SAMPLE </div> " : '';

        // Xử lý hiện thị remark liên quan Bill to
        echo (!empty($remarkBillto)) ? "<div style='width:100%;margin-top:1px;'> $remarkBillto </div> " : "";

        // xử lý remark liên quan chỉ item
          echo (!empty($remarkInternalItem)) ? "<div style='width:100%;margin-top:1px;'> $remarkInternalItem </div> " : "";
        
        
      echo '</div>';

      echo '<div style="float:right;width:49.5%;" > ';

        echo !empty($PO_MAIN_SAMPLE_LINE) ? "<div style='width:90%;font-size:16px;color:red;'> $PO_MAIN_SAMPLE_LINE </div> " : '';
        
        $remarkKiemEPC100 = remarkKiemEPC100($PO_RBO);
        echo !empty($remarkKiemEPC100) ? "<div style='width:90%;font-size:16px;color:red;'> $remarkKiemEPC100 </div> " : '';
        
        if ($PO_FORM_TYPE == 'trim' && strpos($PO_REMARK_2,'JC PENNEY')!==false) {
          echo !empty($PO_REMARK_2) ? "<div style='width:90%;'> $PO_REMARK_2 </div> " : '';
        }

        echo !empty($REMARK_KKL1) ? "<div style='width:90%;'> $REMARK_KKL1 </div> " : '';
        
        if (strpos(strtoupper($PO_SHIP_TO_CUSTOMER), 'WORLDON')!==false ) {
          echo '<div style="font-size:28px;background-color:#e6ffff;text-align:center;border:3px solid #99ccff;" >WORLDON <br /> PNK RIÊNG</div>';
        }

        // remark URGENT
        $URGENT = '<div style="font-size:26px;background-color:#e6ffff;text-align:center;border:1px solid #99ccff;margin-top:1px;">URGENT</div>';
        echo (strtotime($PO_REQUEST_DATE) <= strtotime($PO_SAVE_DATE)) ? $URGENT : "";
        
        echo (!empty($remarkFRUIC)) ? "<div style='width:90%;text-align:center;'> $remarkFRUIC </div> " : "";

      echo '</div>';

    echo '</div>';
    
    //  packing instr
    echo !empty($PACKING_INSTRUCTIONS) ? "<div style='width:90%; background-color:red;'> $PACKING_INSTRUCTIONS </div> " : '';
            
  } else if ( $PO_FORM_TYPE == 'trim_macy' ) { 

    echo '<div id="container1">';
      echo '<table>';
        echo '<tr>';
          
          echo '<th class="Col0" rowspan=2>No &nbsp;</th>';
          echo '<th class="Col0" colspan=3>Order infos &nbsp;</th>';
          echo '<th class="Col0" colspan=2>Paper infos &nbsp;</th>';
          echo '<th class="Col0" colspan=2>Ink infos &nbsp;</th>';

        echo '</tr>';

        echo '<tr>';
          
          echo '<th class="Col0">SO LINE &nbsp;</th>';
          echo '<th class="Col0">Internal Item &nbsp;</th>';
          echo '<th class="Col0">QTY (PCS) &nbsp;</th>';
          echo '<th class="Col0">Material code &nbsp;</th>';

          echo '<th class="Col0">Quantity (EA) &nbsp;</th>';
          echo '<th class="Col0">Ink code &nbsp;</th>';
          echo '<th class="Col0">Ink Description &nbsp;</th>';

        echo '</tr>';
  
        if ($num_mi > 0) {
          $i=0;
          $MI_MATERIAL_QTY_TOTAL = $MI_INK_QTY_TOTAL = 0;
          $SO_PO_QTY_TOTAL = 0;
          $MI_MATERIAL_CODE_ARR = array();
          $MI_MATERIAL_CODE_TMP = array();
          while ($row_mi = mysqli_fetch_array($result_mi) ) {

            //GET MI                      
              $MI_PO_NO    = !empty($row_mi['MI_PO_NO'])?trim($row_mi['MI_PO_NO']):"";
              $MI_PO_SO_LINE        = !empty($row_mi['MI_PO_SO_LINE'])?trim($row_mi['MI_PO_SO_LINE']):"";
              $MI_MATERIAL_CODE     = !empty($row_mi['MI_MATERIAL_CODE'])?trim($row_mi['MI_MATERIAL_CODE']):"";               
              $MI_MATERIAL_DES      = !empty($row_mi['MI_MATERIAL_DES'])?trim($row_mi['MI_MATERIAL_DES']):"";
              $MI_MATERIAL_QTY      = !empty($row_mi['MI_MATERIAL_QTY'])?trim($row_mi['MI_MATERIAL_QTY']):0;
              $MI_MATERIAL_QTY      = (int)$MI_MATERIAL_QTY;
              $MI_MATERIAL_QTY_TOTAL += $MI_MATERIAL_QTY;
              
              $MI_INK_CODE          = !empty($row_mi['MI_INK_CODE'])?trim($row_mi['MI_INK_CODE']):"";
              $MI_INK_DES           = !empty($row_mi['MI_INK_DES'])?trim($row_mi['MI_INK_DES']):"";
              $MI_INK_QTY           = !empty($row_mi['MI_INK_QTY'])?trim($row_mi['MI_INK_QTY']):0;
              $MI_INK_QTY           = (int)$MI_INK_QTY;
              $MI_INK_QTY_TOTAL     += $MI_INK_QTY;
              
              //DÙng để kiểm tra có page 2,3 không
              $MI_MATERIAL_CODE_TMP = array (
                'MI_MATERIAL_CODE' => $MI_MATERIAL_CODE
              );
              array_push($MI_MATERIAL_CODE_ARR, $MI_MATERIAL_CODE_TMP) ; //add MI_MATERIAL_CODE to array
              //GET INTERNAL_ITEM, SO_QTY, FROM SOGRID
              $query_soline = "SELECT SO_INTERNAL_ITEM,SO_ORDER_ITEM,SO_PO_QTY,SO_WIDTH,SO_HEIGHT FROM $table_so WHERE SO_PO_NO = '$MI_PO_NO' AND SO_LINE = '$MI_PO_SO_LINE' ";
              
              $result_soline = mysqli_query($conn, $query_soline);
              if (!$result_soline) {
                  echo "[ERROR V]. Query sai (SO)";
                  return false;
              }
              $num_soline = mysqli_num_rows($result_soline);
              if ($num_soline > 0) {
                  
                  $row_so = mysqli_fetch_array($result_so);
                  ////GET DATA FROM SO GRID
                  $SO_INTERNAL_ITEM = !empty($row_so['SO_INTERNAL_ITEM'])?trim($row_so['SO_INTERNAL_ITEM']):"";

                  // remark theo internal item
                  if (empty($remarkInternalItem) ) {
                    $remarkInternalItem = remarkInternalItem($SO_INTERNAL_ITEM);
                  }
                  

                  $SO_ORDER_ITEM    = !empty($row_so['SO_ORDER_ITEM'])?trim($row_so['SO_ORDER_ITEM']):"";
                  $SO_PO_QTY        = !empty($row_so['SO_PO_QTY'])?trim($row_so['SO_PO_QTY']):0;
                  $SO_PO_QTY        = (int)$SO_PO_QTY;
                  $SO_PO_QTY_TOTAL  += $SO_PO_QTY;
                  // $SO_WIDTH         = !empty($row_so['SO_WIDTH'])?trim($row_so['SO_WIDTH']):"";
                  // $SO_HEIGHT        = !empty($row_so['SO_HEIGHT'])?trim($row_so['SO_HEIGHT']):"";
              }//if soline

              $i++;

              echo '<tr>';

                echo '<td class="Col Col-1" >' . $i . '</td>';
                echo '<td class="Col Col-2" style="font-size:15px;font-weight:bold;">' . $MI_PO_SO_LINE . '</td>';
                echo '<td class="Col Col-3">' . $SO_INTERNAL_ITEM . '</td>';
                echo '<td class="Col Col-4" style="font-size:15px;">' . number_format($SO_PO_QTY) . '</td>';
                echo '<td class="Col Col-5" style="font-size:15px;font-weight:bold;">' . $MI_MATERIAL_CODE . '</td>';
                echo '<td class="Col Col-6" style="font-size:15px;font-weight:bold;">' . number_format($MI_MATERIAL_QTY) . '</td>';
                echo '<td class="Col Col-7" style="font-size:15px;font-weight:bold;">' . $MI_INK_CODE . '</td>';
                echo '<td class="Col Col-8" style="font-size:10px;font-weight:normal;">' . $MI_INK_DES . '</td>';
              
              echo '</tr>';

          }//end while
        } //end if 

        // total
        echo '<tr>';

          echo '<td class="Col" colspan=3 style="font-weight:bold;color:red;  " >QTY TOTAL: &nbsp;</td>';
          echo '<td class="Col Col-4" style="font-weight:bold;color:red;font-size:16px;  ">' . number_format($SO_PO_QTY_TOTAL) . '</td>';
          echo '<td class="Col Col-5" style="font-weight:bold;color:red;  ">&nbsp;</td>';
          echo '<td class="Col Col-6" style="font-weight:bold;color:red;font-size:16px;  "><!-- MI_MATERIAL_QTY_TOTAL --></td>';
          echo '<td class="Col Col-7" style="font-weight:bold;color:red; ">&nbsp;</td>';
          echo '<td class="Col Col-6" style="font-weight:bold;color:red;font-size:16px;  "><!-- MI_INK_QTY_TOTAL --></td>';
          

        echo '</tr>';

        echo '<tr>';

          echo '<td class="" colspan=4 style="font-weight:bold;color:red;text-align:right; height:20px; " >' . $BARCODE_PO_QTY .  '</td>';
          echo '<td class=" Col-5" style="font-weight:bold;color:red;background-color:while;  ">&nbsp;</td>';
          echo '<td class=" Col-6" style="font-weight:bold;color:red;background-color:while;   ">&nbsp;</td>';
          echo '<td class=" Col-7" style="font-weight:bold;color:red;background-color:while;  ">&nbsp;</td>';
          echo '<td class=" Col-8" style="font-weight:bold;color:red;background-color:while;  ">&nbsp;</td>';
          
        echo '</tr>';
      echo '</table>';
    echo '</div>';

    echo '<hr   width="99%" border:#f5f5f5 align="center" />';

    echo '<div id="container2" style="width:99%;font-weight:bold;color:blue; " >';
      echo !empty($PO_MATERIAL_REMARK) ? "<div style='width:90%;'> $PO_MATERIAL_REMARK </div> " : '';
      echo !empty($PO_INK_REMARK) ? "<div style='width:90%;'> $PO_INK_REMARK </div> " : '';
      echo (!empty($remarkFRUIC)) ? "<div style='width:90%;text-align:center;'> $remarkFRUIC </div> " : "";
    echo '</div>';
  
    // <!-- BREAK PAGE 1, to page 2 -->

    //kiểm tra nếu tồn tại material code có 6- thì hiện trang 2, 3
    $header_6  = 0;
    $header_other  = 0;
    $count_header_6 = 0;
    $count_header_other = 0;
    foreach ($MI_MATERIAL_CODE_ARR as $key => $value) {
      if (substr($value['MI_MATERIAL_CODE'],0,2) == '6-') {
        $header_6  = 1;
        $count_header_6++;
      }
      
      if (substr($value['MI_MATERIAL_CODE'],0,2) !== '6-') {
        $header_other  = 1;
        $count_header_other++;
      }


    }
    // echo "page_next: ".$page_next;

    //load page 2, 3
    if ($header_6 == 1) {
      if ($header_other == 1) { //code material có tiền tố 6- và 4- thì hiển thị đầy đủ
        echo '<p style="page-break-after:always;">&nbsp;</p>';
        require_once ('printForm_macy_page_2.php');
        echo '<p style="page-break-after:always;">&nbsp;</p>';
        require_once ('printForm_macy_page_3.php');
        echo '<p style="page-break-after:always;">&nbsp;</p>';
        require_once ('printForm_macy_page_4.php');
      } else { //Trường hợp chỉ có 6-, không có 4-
        // echo '<p style="page-break-after:always;">&nbsp;</p>';
        // require_once ('printForm_macy_page_2.php');
        echo '<p style="page-break-after:always;">&nbsp;</p>';
        require_once ('printForm_macy_page_4.php');
      }
      
      
    } else { //Trường hợp chỉ có 4-
      //require_once ('printForm_macy_page_3.php');
      echo '<p style="page-break-after:always;">&nbsp;</p>';
      require_once ('printForm_macy_page_4.php');

    }
  

  } //end if form type

  // check list page
  require_once ('printForm_check_list.php');

?>

            



</body>
</html>
