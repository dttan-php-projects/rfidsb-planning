<?php
  require_once ( "../../define_constant_system.php");
  require_once (PATH_MODEL . "/__connection.php");
  require_once (PATH_MODEL . "/printNO_conn.php");//load data print
?>

<!-- **************************************************************** -->
<!-- load top header -->
<?php 
  include ("printForm_Horizontal_header.php");
?>
<!-- **************************************************************** -->

<div id="container1"> 
  <table>
    <tr>
      <th class="Col0" rowspan=2>No &nbsp;</td>
      <th class="Col0" colspan=3>Order infos &nbsp;</td>
      <th class="Col0" colspan=2>Paper infos &nbsp;</td>
      <th class="Col0" colspan=2>Ink infos &nbsp;</td>
    </tr>
    <tr>
      <th class="Col0">SO LINE &nbsp;</th>
      <th class="Col0">Internal Item &nbsp;</th>
      <th class="Col0">QTY (PCS) &nbsp;</th>
      <th class="Col0">Material code &nbsp;</th>
      <th class="Col0">Quantity (EA) &nbsp;</th>
      <th class="Col0">Ink code &nbsp;</th>
      <th class="Col0">Ink Description &nbsp;</th>
    </tr>
    <?php 
    
    if ($num_mi3 > 0) {
      $i=0;
      $MI_MATERIAL_QTY_TOTAL = $MI_INK_QTY_TOTAL = 0;
      $SO_PO_QTY_TOTAL_3 = 0;
      while ($row_mi3 = mysqli_fetch_array($result_mi3) ) {
        
        //GET MI                      
        $MI_MATERIAL_CODE     = !empty($row_mi3['MI_MATERIAL_CODE'])?trim($row_mi3['MI_MATERIAL_CODE']):"";  
        // echo "material code: ".$MI_MATERIAL_CODE;             
        if (substr($MI_MATERIAL_CODE,0,2) == '6-' ) {
          continue;//chỉ lấy giá trị material code bắt đầu khác '6-'
        }
          $MI_PO_NO             = !empty($row_mi3['MI_PO_NO'])?trim($row_mi3['MI_PO_NO']):"";
          $MI_PO_SO_LINE        = !empty($row_mi3['MI_PO_SO_LINE'])?trim($row_mi3['MI_PO_SO_LINE']):"";
          
          $MI_MATERIAL_DES      = !empty($row_mi3['MI_MATERIAL_DES'])?trim($row_mi3['MI_MATERIAL_DES']):"";
          $MI_MATERIAL_QTY      = !empty($row_mi3['MI_MATERIAL_QTY'])?trim($row_mi3['MI_MATERIAL_QTY']):0;
          $MI_MATERIAL_QTY      = (int)$MI_MATERIAL_QTY;
          $MI_MATERIAL_QTY_TOTAL += $MI_MATERIAL_QTY;
          
          $MI_INK_CODE          = !empty($row_mi3['MI_INK_CODE'])?trim($row_mi3['MI_INK_CODE']):"";
          $MI_INK_DES           = !empty($row_mi3['MI_INK_DES'])?trim($row_mi3['MI_INK_DES']):"";
          $MI_INK_QTY           = !empty($row_mi3['MI_INK_QTY'])?trim($row_mi3['MI_INK_QTY']):0;
          $MI_INK_QTY           = (int)$MI_INK_QTY;
          $MI_INK_QTY_TOTAL     += $MI_INK_QTY;
          
          //GET INTERNAL_ITEM, SO_QTY, FROM SOGRID
          $query_soline3 = "SELECT SO_INTERNAL_ITEM,SO_ORDER_ITEM,SO_PO_QTY,SO_WIDTH,SO_HEIGHT  FROM $table_so WHERE SO_PO_NO = '$MI_PO_NO' AND SO_LINE = '$MI_PO_SO_LINE' ";
          $result_soline3 = mysqli_query($conn, $query_soline3);
          if (!$result_soline3) {
              echo "[ERROR V]. Query sai (SO)";
              return false;
          }
          $num_soline3 = mysqli_num_rows($result_soline3);
          if ($num_soline3 > 0) {
              
              $row_so3 = mysqli_fetch_array($result_soline3);
              ////GET DATA FROM SO GRID
              $SO_INTERNAL_ITEM = !empty($row_so3['SO_INTERNAL_ITEM'])?trim($row_so3['SO_INTERNAL_ITEM']):"";
              $SO_ORDER_ITEM    = !empty($row_so3['SO_ORDER_ITEM'])?trim($row_so3['SO_ORDER_ITEM']):"";
              $SO_PO_QTY        = !empty($row_so3['SO_PO_QTY'])?trim($row_so3['SO_PO_QTY']):0;
              $SO_PO_QTY        = (int)$SO_PO_QTY;
              $SO_PO_QTY_TOTAL_3  += $SO_PO_QTY;

              $BARCODE_PO_QTY_3         = '<img style="height:120%" src="../../data/barcode.php?text='.$SO_PO_QTY_TOTAL_3.'" />';
              // $SO_WIDTH         = !empty($row_so3['SO_WIDTH'])?trim($row_so3['SO_WIDTH']):"";
              // $SO_HEIGHT        = !empty($row_so3['SO_HEIGHT'])?trim($row_so3['SO_HEIGHT']):"";
          }//if soline

          $i++;
          ?>
          <tr>
            <td class="Col Col-1" ><?php echo $i; ?> &nbsp;</td>
            <td class="Col Col-2" style="font-size:15px;font-weight:bold;"><?php echo $MI_PO_SO_LINE; ?></td>
            <td class="Col Col-3"><?php echo $SO_INTERNAL_ITEM; ?></td>
            <td class="Col Col-4"><?php echo number_format($SO_PO_QTY); ?></td>
            <td class="Col Col-5" style="font-size:15px;font-weight:bold;"><?php echo $MI_MATERIAL_CODE; ?></td>
            <td class="Col Col-6" style="font-size:15px;font-weight:bold;"><?php echo number_format($MI_MATERIAL_QTY); ?></td>
            <td class="Col Col-7" style="font-size:15px;font-weight:bold;"><?php echo $MI_INK_CODE; ?></td>
            <td class="Col Col-8" style="font-size:15px;font-weight:bold;"><?php echo $MI_INK_DES; ?></td>
          </tr>
        <?php
          
      }//end while
    } //end if  
    ?>
    <tr>
      <td class="Col" colspan=3 style="font-weight:bold;color:red;  " >QTY TOTAL: &nbsp;</td>
      <td class="Col Col-4" style="font-weight:bold;color:red;font-size:16px;  "><?php echo number_format($SO_PO_QTY_TOTAL_3); ?></td>
      <td class="Col Col-5" style="font-weight:bold;color:red;  ">&nbsp;</td>
      <td class="Col Col-6" style="font-weight:bold;color:red;font-size:16px; "><?php //echo $MI_MATERIAL_QTY_TOTAL; ?></td>
      <td class="Col Col-7" style="font-weight:bold;color:red; ">&nbsp;</td>
      <td class="Col Col-8" style="font-weight:bold;color:red;font-size:16px; "><?php // echo $MI_INK_QTY_TOTAL; ?></td>
    </tr>
    <tr>
      <td class="" colspan=4 style="font-weight:bold;color:red;text-align:right;  " ><?php echo $BARCODE_PO_QTY_3; ?></td>
      <td class=" Col-5" style="font-weight:bold;color:red;background-color:while;  ">&nbsp;</td>
      <td class=" Col-6" style="font-weight:bold;color:red;background-color:while;   ">&nbsp;</td>
      <td class=" Col-7" style="font-weight:bold;color:red;background-color:while;  ">&nbsp;</td>
      <td class=" Col-8" style="font-weight:bold;color:red;background-color:while;  ">&nbsp;</td>
    </tr>
  </table>
  <div class="case-label" style="text-align:center;" >COLOR </div>
</div> 
<hr   width="99%" border:#f5f5f5 align="center" />
<div id="container2">
    <table>
      <tr>
        <td style="width:10%; ">Material remark:</td>
        <td style="font-weight:bold;color:blue; "><?php echo $PO_MATERIAL_REMARK; ?></td>
        <td style="width:10%; ">Ink remark:</td>
        <td style="width:30%;font-weight:bold; color:blue; " ><?php echo $PO_INK_REMARK; ?></td>
      </tr>
      <!-- <tr>
        <td style="width:10%; ">Other Remark:</td>
        <td colspan=3; style="font-weight:bold;color:blue; "><?php //echo //$PO_REMARK_1; ?></td>
      </tr> -->
    </table>
</div>