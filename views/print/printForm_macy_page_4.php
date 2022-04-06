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
    <tr style="width:100%; ">
      <th class="Col00" style="width:40px; ">No</th>
      <th class="Col00" style="width:12%; ">SO LINE &nbsp;</th>
      <th class="Col00" style="width:25%; ">SO# Barcode&nbsp;</th>
      <th class="Col00" style="width:auto; ">Material code (MC) &nbsp;</th>
      <th class="Col00" style="width:30%; ">MC Barcode &nbsp;</th>
      <!-- <th class="Col00" style="width:14%; ">Ink code (IC) &nbsp;</th>
      <th class="Col00" style="width:auto; ">IC Barcode &nbsp;</th> -->
    </tr>
    <?php 
    
    if ($num_mi4 > 0) {
      $i=0;
      while ($row_mi4 = mysqli_fetch_array($result_mi4) ) {
        
        //GET MI                      
        $MI_MATERIAL_CODE     = !empty($row_mi4['MI_MATERIAL_CODE'])?trim($row_mi4['MI_MATERIAL_CODE']):"";               
        $MI_PO_NO             = !empty($row_mi4['MI_PO_NO'])?trim($row_mi4['MI_PO_NO']):"";
        $MI_PO_SO_LINE        = !empty($row_mi4['MI_PO_SO_LINE'])?trim($row_mi4['MI_PO_SO_LINE']):"";
        
        $MI_MATERIAL_DES      = !empty($row_mi4['MI_MATERIAL_DES'])?trim($row_mi4['MI_MATERIAL_DES']):"";
        $MI_MATERIAL_QTY      = !empty($row_mi4['MI_MATERIAL_QTY'])?trim($row_mi4['MI_MATERIAL_QTY']):0;
        $MI_MATERIAL_QTY      = (int)$MI_MATERIAL_QTY;
        $MI_MATERIAL_QTY_TOTAL += $MI_MATERIAL_QTY;
        
        $MI_INK_CODE          = !empty($row_mi4['MI_INK_CODE'])?trim($row_mi4['MI_INK_CODE']):"";
        $MI_INK_DES           = !empty($row_mi4['MI_INK_DES'])?trim($row_mi4['MI_INK_DES']):"";
        $MI_INK_QTY           = !empty($row_mi4['MI_INK_QTY'])?trim($row_mi4['MI_INK_QTY']):0;
        $MI_INK_QTY           = (int)$MI_INK_QTY;
        $MI_INK_QTY_TOTAL     += $MI_INK_QTY;
          
        //GET INTERNAL_ITEM, SO_QTY, FROM SOGRID
        $query_soline4 = "SELECT SO_INTERNAL_ITEM,SO_ORDER_ITEM,SO_PO_QTY,SO_WIDTH,SO_HEIGHT  FROM $table_so WHERE SO_PO_NO = '$MI_PO_NO' AND SO_LINE = '$MI_PO_SO_LINE' ";
        $result_soline4 = mysqli_query($conn, $query_soline4);
        if (!$result_soline4) {
            echo "[ERROR V]. Query sai (SO)";
            return false;
        }
        $num_soline4 = mysqli_num_rows($result_soline4);
        if ($num_soline4 > 0) {
              
              $row_so4 = mysqli_fetch_array($result_soline4);
              ////GET DATA FROM SO GRID
              $SO_ORDER_ITEM    = !empty($row_so4['SO_ORDER_ITEM'])?trim($row_so4['SO_ORDER_ITEM']):"";

              $BARCODE_SOLINE           = '<img style="height:120%" width:90% src="../../data/barcode.php?text='.$MI_PO_SO_LINE.'" />';
              $BARCODE_MI_MATERIAL_CODE = '<img style="height:120%" src="../../data/barcode.php?text='.$MI_MATERIAL_CODE.'" />';
              $BARCODE_MI_INK_CODE = '<img style="height:120%" src="../../data/barcode.php?text='.$MI_INK_CODE.'" />';

          }//if soline

          $i++;
          ?>
          <tr >
            <td style="text-align:center; height:40px; "><?php echo $i; ?></td>
            <td style="text-align:center;font-size:16px;font-weight:bold;" ><?php echo $MI_PO_SO_LINE; ?></td>
            <td style="text-align:center;"><span style="width:80%; " ><?php echo $BARCODE_SOLINE; ?></span></td>
            <td style="text-align:center;"><?php echo $MI_MATERIAL_CODE; ?></td>
            <td style="text-align:center;"><span style="width:80%; " ><?php echo $BARCODE_MI_MATERIAL_CODE; ?></span></td>
            <!-- <td style=" "><?php //echo $MI_INK_CODE; ?></td>
            <td style=""><?php //echo $BARCODE_MI_INK_CODE; ?></td> -->
          </tr>
        <?php
          
      }//end while
    } //end if  
    ?>
  </table>
  <!-- style="color:red;font-size:20px;font-weight:bold;" -->
</div> 
<hr   width="99%" border:#f5f5f5 align="center" />