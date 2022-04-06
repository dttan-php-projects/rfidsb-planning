<table style="width:100%; ">
    <tr>
      <td><span style="color:blue;text-align:left;font-size:10px;">GROUP: THERMAL/RFID</span></td>
      <td><span style="font-weight:bold;text-align:left;font-size:30px;text-transform:uppercase;">
            <?php 
              echo $REMARK_DH_ADIDAS; 
              if ($PO_RBO=="JC PENNEY") {
                echo "JC PENNEY COMBO";
              }
              echo $REMARK_DONG_HANG_6000PCS;
            ?>
          </span>
      </td>
      <td><span style="width:15%;color:red;text-align:center;font-size:16px;font-weight:bold; ">
          <?php echo $SHORT_LT; ?>
          </span>
      </td>
      <td style="text-align:center; font-size:12px;" >
        <?php 
          if ($PO_FORM_TYPE == 'trim_macy') {
            echo 'ĐƠN HÀNG RÁP RFID & NON RFID'; 
          }
          //RFID RUSSIA
          echo '<span style="text-align:center;font-size:25px;text-transform:uppercase;">'.$REMARK_DH_RFID_RUSSIA.'</span>';
        ?>
      </td>
      <td colspan=3 style="width:40%;text-align:right;font-size:13px;font-weight:bold;" >
          <?php if ($PO_FORM_TYPE == 'trim_macy') echo 'ĐƠN HÀNG NHẬP KHO CHUNG PHIẾU CHO TẤT CẢ CÁC LINE'; ?>
      </td>
    </tr>
    <tr>
      <td colspan=6 style="height:20px;color:red;text-align:center; background-color:#66ffcc;" ><h2 style="padding-top:9px;height:20px; " >LỆNH SẢN XUẤT/PRODUCTION ORDER</h2></td>
      <td style="text-align:left; font-size:16px;background-color:#66ffcc;width:6%;"><?php echo '<span style="font-weight:bold;font-size:18px;" >'.$PO_COUNT_SO_LINE . '</span>' ; ?>&nbsp;line </td>
    </tr>
</table>
<!-- <div id="p-top-1" >
  <h2 style="padding:1px;color:red;text-align:center; background-color:#66ffcc;">LỆNH SẢN XUẤT/PRODUCTION ORDER</h2>
</div> -->

<div id="p-header">
    <table  style="width:100%">
        <tr>
          <td class="header-0" class="No" style="width:10%;height:40px;font-weight:bold;font-size:20px; ">No: &nbsp;</td>
          <td class="header" style="text-align:left;width:32%;font-size:20px; "><?php  echo $PO_NO_FI;?>&nbsp;</td>
          <td class="header-0" style="width:10%; " >  &nbsp;</td>
          <td style="font-size:14px;width:12%; ">Create by: &nbsp;</td>
          <td style="font-weight:bold; font-size:14px;width:12%;color:red; "><?php echo $PO_CREATED_BY_OK; ?></td>
          <td rowspan=2 class="header" style="text-align:center; font-size:24px; height:20px;  ">
          <?php if ($PO_FORM_TYPE == 'trim') { 
                  echo "TRIM <br />";
                } else if ($PO_FORM_TYPE == 'trim_macy') {
                  echo "MACY'S <br />";
                } else if ($PO_FORM_TYPE == 'pvh_rfid') {
                  echo "PVH <br />";
                }
                echo $BARCODE_H; ?></td>
          
        </tr>
        <tr>
          <td class="header-0">Ngày làm lệnh:&nbsp;</td>
          <td class="header"><?php echo $PO_SAVE_DATE; ?> &nbsp;</td>
          <td class="header-0"> &nbsp;</td>
          <td class="header-0">CS Name: &nbsp;</td>
          <td class="header"><?php echo $PO_CS; ?>&nbsp;</td>
          <!-- <td class="header"> &nbsp;</td> -->
          <!-- <td>&nbsp;</td> -->
        </tr>
        <tr>
          <td class="header-0">Order date: &nbsp;</td>
          <td class="header"><?php echo $PO_ORDERED_DATE ; ?> &nbsp;</td>
          <td class="header-0">RBO: &nbsp;</td>
          <td colspan=3 class="header"><?php echo $PO_RBO; ?> &nbsp;</td>
          <!-- <td class="header-0" style="width:10%; "  > &nbsp;</td>
          <td class="header"  ><?php //echo $PO_RBO; ?> &nbsp;</td> -->
          
        </tr>
        <tr>
          <td class="header-0" style="font-size:16px;font-weight:bold;">Promise date:</td>
          <td class="header" style="font-size:16px;font-weight:bold;"><?php echo $PO_PROMISE_DATE; ?> &nbsp;</td>
          <td class="header-0">Ship to: &nbsp;</td>
          <td colspan=3 class="header" style="font-size:
            <?php 
              if (strpos($PO_SHIP_TO_CUSTOMER, 'WORLDON')!==false || strpos($PO_SHIP_TO_CUSTOMER, 'worldon')!==false ) 
                echo '35px;'
            ?> 
          ">
            <?php echo $PO_SHIP_TO_CUSTOMER; ?> &nbsp;
          </td>
        </tr>
        <tr>
          <td class="header-0" style="font-size:16px;font-weight:bold;">Request date:</td>
          <td class="header" style="font-size:16px;font-weight:bold;"> 
          <?php 
            $CRDICRemark_string = (isset($CRDICRemark['remark']) ) ? (" / " . $CRDICRemark['remark'] . ": " . $CRDICRemark['request_date']) : '';
            echo $PO_REQUEST_DATE . $CRDICRemark_string; 
          ?>
          </td>
          <td class="header-0">Received date: &nbsp;</td>
          <td class="header"> 
            <?php 
              if ($PO_DATE_RECEIVED != '1970-01-01') {
                echo date('d-M-y',strtotime($PO_DATE_RECEIVED));
              }  
            ?> &nbsp;
          </td>
          <td class="header-0" style="width:10%; "  >file: &nbsp;</td>
          <td class="header"  > 
            <?php 
              if ($PO_FILE_DATE_RECEIVED == 1){
                echo "1";
              } else if ($PO_FILE_DATE_RECEIVED == 2){
                echo "2&3"; 
              } else if ($PO_FILE_DATE_RECEIVED == 4){
                echo "4"; 
              }
            ?> &nbsp;
          </td>
          
        </tr>
    </table>
</div>
<hr  width="100%" align="center" />
