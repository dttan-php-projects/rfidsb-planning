<?php
    $field = 'order_item, note_rbo, rbo, ribbon_code, ink_des, width, height, blank_gap,material_code, material_des, 
                ghi_chu_item, remark_giay, remark_muc,lay_sample_15_pcs';
    $query_no = "SELECT $field FROM no_cbs 
                WHERE internal_item ='$ORACLE_ITEM' ";
    $result_no = mysqli_query($conn252, $query_no);
    if($result_no === FALSE) { die(mysql_error()); }
    if (mysqli_num_rows($result_no)>0) {
        
        //get array data
        $resultNOCBS = mysqli_fetch_array($result_no,MYSQLI_ASSOC);

        //set data
        $RBO 		        = !empty($resultNOCBS['rbo']) ? trim($resultNOCBS['rbo']):'';	
        $ORDER_ITEM 		= !empty($resultNOCBS['order_item']) ? trim($resultNOCBS['order_item']):'';	
        $MATERIAL_CODE 		= !empty($resultNOCBS['material_code']) ? trim($resultNOCBS['material_code']):'';	
        $MATERIAL_DES 		= !empty($resultNOCBS['material_des']) ? trim($resultNOCBS['material_des']):'';	
        $INK_CODE 		    = !empty($resultNOCBS['ribbon_code']) ? trim($resultNOCBS['ribbon_code']):'';
        $INK_DES 		    = !empty($resultNOCBS['ink_des']) ? trim($resultNOCBS['ink_des']):'';

        $WIDTH 		        = !empty($resultNOCBS['width']) ? trim($resultNOCBS['width']):0;	
        $WIDTH 				= (float)$WIDTH;
        $HEIGHT 		    = !empty($resultNOCBS['height']) ? trim($resultNOCBS['height']):0;	
        $HEIGHT 			= (float)$HEIGHT;

        $MATERIAL_REMARK    = !empty($resultNOCBS['remark_giay']) ? trim($resultNOCBS['remark_giay']):'';
        $INK_REMARK 		= !empty($resultNOCBS['remark_muc']) ? trim($resultNOCBS['remark_muc']):'';
 
        $SAMPLE15PCS 		= !empty($resultNOCBS['lay_sample_15_pcs']) ? trim($resultNOCBS['lay_sample_15_pcs']):'';
        $I21 		        = !empty($resultNOCBS['ghi_chu_item']) ? trim($resultNOCBS['ghi_chu_item']):'';

        /* ***GET @SCRAP + GAP ************************************************ */
        $scap_percent       = getScrap($RBO);
        $GAP 		        = !empty($resultNOCBS['blank_gap']) ? (float)$resultNOCBS['blank_gap']:0;

        //$GAP 				= (float)$GAP;
        /* *********************************************************** */

        

        //Công thức tính vật tư
        //2019-12-05: @Duyen yêu cầu làm tròn vật tư trước rồi mới tính phần mực. Material qty và Ink qty luôn làm tròn lên
        if($ORACLE_ITEM == 'CB1627627'||$ORACLE_ITEM == '1-215877-000-00'||(strpos(trim(strtoupper($SAMPLE15PCS)),'15 PCS')!==false)){
            $MATERIAL_QTY 	= ceil(($QTY+15)*$scap_percent);
        } else {
            $MATERIAL_QTY 	= ceil( $QTY*$scap_percent );
        }

        

        if( strpos(strtoupper($I21), "NHAN CHAY 2 MAT MUC IN") !==false ){
            
            $INK_QTY 	= ceil(( $MATERIAL_QTY * ($WIDTH + $GAP)/1000) * 2);//x2
            // $INK_QTY 	= ceil( $INK_QTY * $scap_percent );

        } else if (strpos(strtoupper($I21), "NHAN IN 2 MAT MUC" ) !==false ) {
            
            $INK_REMARK = $INK_REMARK. ' '.$I21;
            $INK_QTY 	= ceil($MATERIAL_QTY * ($WIDTH + $GAP)/1000) ;
            // $INK_QTY 	= ceil( $INK_QTY * $scap_percent );
        } else {

            $INK_QTY 	= ceil($MATERIAL_QTY * ($WIDTH + $GAP)/1000) ;
            // $INK_QTY 	= ceil( $INK_QTY * $scap_percent );
        }

        //Công thức tính mực
            if($INK_CODE == 'Muc Kiaro D'){$INK_QTY = 0;} 
        


        /* START: DON HANG KIT (NO CSB và RFID) ------------------------------------------------------------------------------- */
        
            $itemKITArr[] = array( 'internal_item' => '25HPOL-219403-383-00 (VN)', 'material_price' => 14800, 'ink_price' => 600);
            $itemKITArr[] = array( 'internal_item' => '25HPOL-221413-383-00 (VN)', 'material_price' => 14800, 'ink_price' => 600);
            $itemKITArr[] = array( 'internal_item' => '25HPOL-235973-383-00 (VN)', 'material_price' => 14800, 'ink_price' => 600);
            $itemKITArr[] = array( 'internal_item' => '25HPOL-219403-383-00 (VN) KIT', 'material_price' => 14800, 'ink_price' => 600);
            $itemKITArr[] = array( 'internal_item' => '25HASICSRFIDHT-VN-VN', 'material_price' => 12500, 'ink_price' => 500);
            $itemKITArr[] = array( 'internal_item' => '25HASICSRFIDHT', 'material_price' => 12500, 'ink_price' => 500);
            $itemKITArr[] = array( 'internal_item' => '25K-601914-237-00', 'material_price' => 17500, 'ink_price' => 600);
            // mail: [RFID SB] DANG KIT
            $itemKITArr[] = array( 'internal_item' => '25K-230836-324-00', 'material_price' => 14400, 'ink_price' => 600);
            $itemKITArr[] = array( 'internal_item' => '25K-215233-370-00', 'material_price' => 4300, 'ink_price' => 600);

            // mail: [RFID SB] DANG KIT. Cập nhật thêm 20211223
            $itemKITArr[] = array( 'internal_item' => '25K-603544333-00', 'material_price' => 9300, 'ink_price' => 600);
            $itemKITArr[] = array( 'internal_item' => '25K-603547333-00', 'material_price' => 9300, 'ink_price' => 600);
            // Bán vật tư: Chỉ tính vật tư, mực = 0
            // $itemKITArr[] = array( 'internal_item' => '25K-215261-320-00-BR-RL', 'material_price' => 15320, 'ink_price' => 0);
            // Beo yeu cau 20211230
            $itemKITArr[] = array( 'internal_item' => '25K-215261-320-00-BR-RL', 'material_price' => 3830, 'ink_price' => 0);
            // Chỉ bán mực: Chỉ tính mực, vật tư = 0
            $itemKITArr[] = array( 'internal_item' => '25K-215261-320-00-INK-RL', 'material_price' => 0, 'ink_price' => 600);

            $itemKITArr[] = array( 'internal_item' => '25HPOL-235973-383-00 (VN) KIT', 'material_price' => 14800, 'ink_price' => 600);
            $itemKITArr[] = array( 'internal_item' => '25HPOL-221413-383-00 (VN) KIT', 'material_price' => 14800, 'ink_price' => 600);

            // mail: [RFID SB] DANG KIT. Cập nhật thêm 20220214
            $itemKITArr[] = array( 'internal_item' => '25K-603688-386-00', 'material_price' => 14800, 'ink_price' => 600);
            $itemKITArr[] = array( 'internal_item' => '25K-603547333-000', 'material_price' => 9300, 'ink_price' => 600);

            // mail: [RFID SB] DANG KIT. Cập nhật thêm 20220307
            $itemKITArr[] = array( 'internal_item' => '25K-603KIT-386-00', 'material_price' => 14800, 'ink_price' => 600);

            // mail: [RFID SB] DANG KIT. Cập nhật thêm 20220316
            $itemKITArr[] = array( 'internal_item' => '25K-KIT051-386-00', 'material_price' => 14400, 'ink_price' => 600);
            
            // mail: [RFID SB] DANG KIT. Cập nhật thêm 20220320
            $itemKITArr[] = array( 'internal_item' => '25K-603877-374-02-KIT', 'material_price' => 4400, 'ink_price' => 600);


            foreach ($itemKITArr as $itemKIT ) {
                $internal_item_kit = $itemKIT['internal_item'];

                if ($internal_item_kit == $ORACLE_ITEM ) {
                    $material_price = $itemKIT['material_price'];
                    $ink_price = $itemKIT['ink_price'];

                    $MATERIAL_QTY 	= ceil($QTY * $material_price );
                    $INK_QTY 	= ceil($QTY * $ink_price );
                    break;
                }
            }


        /* END: DON HANG KIT (NO CSB và RFID) ------------------------------------------------------------------------------- */

        /* START: ITEM ĐẶC BIỆT: 2-425904-000-00. Công thức tính Mực = Qty * 600. @Phuong, @Duyen yêu cầu 20210217. Gửi Hangout ------------------------------------------------ */
            if ($ORACLE_ITEM == '2-425904-000-00' ) {
                $INK_QTY = $QTY * 600;
            }

        /* END: ITEM ĐẶC BIỆT: 2-425904-000-00. Công thức tính Mực = Qty * 600. @Phuong, @Duyen yêu cầu 20210217. Gửi Hangout ------------------------------------------------ */
        

    } else {
        $response = [
            'status' => false,
            'mess' 	 =>  "MASTER ITEM $ORACLE_ITEM KHÔNG TỒN TẠI, VUI LÒNG CẬP NHẬT! (DB 1 LINE)"
        ];
        echo json_encode($response);
    }


