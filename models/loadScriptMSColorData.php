<?php
    $field = 'order_item,rbo,color_code,item_color,material_code,material_des,ribbon_code,ink_des,width, height, blank_gap, remark';
    $query = "SELECT order_item,rbo,color_code,item_color,material_code,material_des,ribbon_code,ink_des,width, height, blank_gap, remark
            FROM `ms_color` 
            WHERE  internal_item = '$ORACLE_ITEM'  
            ORDER BY created_time DESC ";
    $result = mysqli_query($conn252, $query);
    $numMS = mysqli_num_rows($result);
    if($result === FALSE) { die(mysql_error()); }
    if ($numMS>0) {

        //get data
        $resultCBS 	= mysqli_fetch_array($result,MYSQLI_ASSOC);
                            
        //set data 
        $ORDER_ITEM 		= !empty($resultCBS['order_item']) ? trim($resultCBS['order_item']):'';	
        $RBO 				= !empty($resultCBS['rbo']) ? trim($resultCBS['rbo']):'';
        // $item_color 		= !empty($resultCBS['item_color']) ? trim($resultCBS['item_color']):'';	
        $MATERIAL_CODE 		= !empty($resultCBS['material_code']) ? trim($resultCBS['material_code']):'';
        $MATERIAL_DES 		= !empty($resultCBS['material_des']) ? trim($resultCBS['material_des']):'';
        $INK_CODE 			= !empty($resultCBS['ribbon_code']) ? trim($resultCBS['ribbon_code']):'';
        $INK_DES 			= !empty($resultCBS['ink_des']) ? trim($resultCBS['ink_des']):'';

        $WIDTH 				= !empty($resultCBS['width']) ? trim($resultCBS['width']):'';	
        $WIDTH 				= (float)$WIDTH;
        $HEIGHT 			= !empty($resultCBS['height']) ? trim($resultCBS['height']):'';	
        $HEIGHT = 			(float)$HEIGHT;

        /* ***GET @SCRAP ************************************************ */
        $scap_percent 		= getScrap($RBO);//
        $scap_percent = (float)$scap_percent;

        $GAP 				= !empty($resultCBS['blank_gap']) ? trim($resultCBS['blank_gap']):'';
        $GAP 				= (float)$GAP;

        $MATERIAL_REMARK 	= !empty($resultCBS['remark']) ? trim($resultCBS['remark']):'';
        $INK_REMARK= '';

        //2019-12-05: @Duyen yêu cầu làm tròn vật tư trước rồi mới tính phần mực. 
        //Material qty và Ink qty luôn làm tròn lên
        $MATERIAL_QTY 		= ceil( $QTY * $scap_percent ); 
        // Công thức  tính mực
        $INK_QTY 		= ceil( $MATERIAL_QTY * ($WIDTH + $GAP)/1000);
        // $INK_QTY 		= ceil($INK_QTY*$scap_percent); // Duyen yeu cau bo scrap.

        //Trường hợp PVH, Duyên yêu cầu sửa lại công thức 20200122
        if ($FORM_TYPE=='pvh_rfid') {
            $MATERIAL_QTY 		= ceil( $QTY + ($QTY * 1.4)/100 );
            $INK_QTY 		= ceil( ($MATERIAL_QTY * $WIDTH * 1.014)/1000);
        }
        //Trường hợp đặc biệt
        if($INK_CODE == 'Muc Kiaro D'){$INK_QTY = 0;}
        

        
    } 



?>