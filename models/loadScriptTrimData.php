<?php

    //get data from database_trim table
    $field = 'MATERIAL_CODE,MATERIAL_DES, RIBBON_CODE,RIBBON_DES, CHIEU_DAI, CHIEU_NGANG, RBO,ORDER_ITEM, REMARK, REMARK_MUC, REMARK_GIAY';
    $query = "SELECT    *
            FROM        `database_trim` 
            WHERE       internal_item ='$ORACLE_ITEM' 
            ORDER BY    CREATED_DATE_TIME DESC ";
    $result = mysqli_query($conn252, $query);
    if($result === FALSE) { die(mysql_error()); }
    $numTRIMMACY = mysqli_num_rows($result);
    if ($numTRIMMACY > 0) {
        
        //get data
        $resultTRIM 	= mysqli_fetch_array($result,MYSQLI_ASSOC);

        //set data
        $ORDER_ITEM 	= !empty($resultTRIM['ORDER_ITEM']) ? trim($resultTRIM['ORDER_ITEM']):'';
        $RBO 			= !empty($resultTRIM['RBO']) ? trim($resultTRIM['RBO']):'';
        $MATERIAL_CODE 	= !empty($resultTRIM['MATERIAL_CODE']) ? trim($resultTRIM['MATERIAL_CODE']):'';
        $MATERIAL_DES 	= !empty($resultTRIM['MATERIAL_DES']) ? trim($resultTRIM['MATERIAL_DES']):'';
        $INK_CODE 		= !empty($resultTRIM['RIBBON_CODE']) ? trim($resultTRIM['RIBBON_CODE']):'';
        $INK_DES 		= !empty($resultTRIM['RIBBON_DES']) ? trim($resultTRIM['RIBBON_DES']):'';

        $WIDTH 			= !empty($resultTRIM['CHIEU_DAI']) ? trim($resultTRIM['CHIEU_DAI']):'';
        $WIDTH 			= (float)$WIDTH;
        $HEIGHT 		= !empty($resultTRIM['CHIEU_NGANG']) ? trim($resultTRIM['CHIEU_NGANG']):'';
        $HEIGHT 		= (float)$HEIGHT;

        $MATERIAL_REMARK= !empty($resultTRIM['REMARK_GIAY']) ? trim($resultTRIM['REMARK_GIAY']):'';
        $INK_REMARK 	= !empty($resultTRIM['REMARK_MUC']) ? trim($resultTRIM['REMARK_MUC']):'';

        //GET SCRAP and GAP
        // $scap_percent 	= 1.4;
        // tính lại
        $trim_macy = true;
        $scap_percent       = getScrap($RBO, $trim_macy );
        $GAP = 0;

        //2019-12-05: @Duyen yêu cầu làm tròn vật tư trước rồi mới tính phần mực. Material qty và Ink qty luôn làm tròn lên
        $MATERIAL_QTY 		= ceil( $QTY + ($QTY * $scap_percent)/100 );
        $INK_QTY = ceil(( $QTY * $WIDTH * 1.014)/1000); //CT Duyên gửi
        // $INK_QTY = ceil($INK_QTY);

        //Trường hợp PVH, Duyên yêu cầu sửa lại công thức 20200122
        if ($FORM_TYPE=='pvh_rfid') {
            $MATERIAL_QTY 		= ceil( $QTY + ($QTY * 1.4)/100 );
            $INK_QTY 		= ceil( ($MATERIAL_QTY * $WIDTH * 1.014)/1000);
        }
        
        if($INK_CODE == 'Muc Kiaro D'){$INK_QTY = 0;}

    }