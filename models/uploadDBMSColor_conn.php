<?php
    //check login
    if(!isset($_COOKIE["VNRISIntranet"])) header('Location: login.php');//check login
    date_default_timezone_set('Asia/Ho_Chi_Minh');
    require_once ( "../define_constant_system.php");
    require_once (PATH_MODEL . "/__connection.php");  
    //connect host
    $table      = "ms_color";
    $conn       = getConnection();

    //get exel File function
        include_once ("__getFileExcel.php");

    //execute
    if (@$_REQUEST["mode"] == "html5" || @$_REQUEST["mode"] == "flash") {
        header("Content-Type: text/json");
        $filename = date("d_m_Y__H_i_s");
        $excelType = ['d/vnd.ms-excel','application/vnd.openxmlformats-officedocument.spreadsheetml.sheet','application/vnd.ms-excel','application/vnd.ms-excel.sheet.macroEnabled.12'];
        $fileSize = $_FILES['file']['size'];
        $fileType = $_FILES['file']['type'];
        if($fileSize>1000000) {
            $response = [
                'state' 	=>	false,
                'filename'   	=>	$filename,
                'extra' 	=>	[
                    'mess'  => 'File dữ liệu import quá lớn, Vui lòng kiểm tra lại'
                ]
            ];
        }else if(!in_array($fileType,$excelType)) {
            $response = [
                'state' 	=>	false,
                'filename'   	=>	$filename,
                'extra'		=>	[
                    'mess'  => 'File dữ liệu phải là EXCEL, Vui lòng kiểm tra lại'
                ]
            ];
        } else {

            $data = getFileExcel($_FILES['file']["tmp_name"]);	
            //$data = "(SAMPLE)_RFIDSB_DBMSColor.xlsx";
            if(!empty($data)){
                $updated_by = $_COOKIE["VNRISIntranet"];//get user
                foreach($data as $key => $value){
                    //get data 
                    $internal_item          = !empty($value[0])?addslashes($value[0]):'';
                    $rbo                    = !empty($value[1])?addslashes($value[1]):'';
                    $order_item             = !empty($value[2])?addslashes($value[2]):'';
                    $color_code             = !empty($value[3])?addslashes($value[3]):'';
                    $item_color             = !empty($value[4])?addslashes($value[4]):'';
                    $material_code          = !empty($value[5])?addslashes($value[5]):'';
                    $material_des           = !empty($value[6])?addslashes($value[6]):'';
                    $ribbon_code            = !empty($value[7])?addslashes($value[7]):'';//float
                    $ink_des                = !empty($value[8])?addslashes($value[8]):'';//float
                    $width                  = !empty($value[9])?addslashes($value[9]):0;//float
                    $width = (float)$width;
                    $height                 = !empty($value[10])?addslashes($value[10]):0;
                    $height = (float)$height;
                    $ghi_chu_item           = !empty($value[11])?addslashes($value[11]):'';
                    $blank_gap              = !empty($value[12])?addslashes($value[12]):0;
                    $blank_gap = (float)$blank_gap;
                    $remark                 = !empty($value[13])?addslashes($value[13]):'';
                    $other_remark_1         = !empty($value[14])?addslashes($value[14]):'';
                    $other_remark_2         = !empty($value[15])?addslashes($value[15]):'';
                    $other_remark_3         = !empty($value[16])?addslashes($value[16]):'';
                    $other_remark_4         = !empty($value[17])?addslashes($value[17]):'';

                    //update/insert data
                    $query_check = "SELECT `internal_item` FROM $table WHERE internal_item='$internal_item' AND order_item='$order_item' AND item_color='$item_color' AND color_code='$color_code' ";
                    $result_check = mysqli_query($conn, $query_check);
                    
                    if (mysqli_num_rows($result_check)>0) {//// update
                        $query = "UPDATE $table
                                  SET   
                                        `rbo`='$rbo',
                                        `order_item`='$order_item',
                                        `color_code`='$color_code',
                                        `item_color`='$item_color',
                                        `material_code`='$material_code',
                                        `material_des`='$material_des',
                                        `ribbon_code`='$ribbon_code',
                                        `ink_des`='$ink_des',
                                        `width`='$width',
                                        `height`='$height',
                                        `note`='$ghi_chu_item',
                                        `blank_gap`='$blank_gap',
                                        `remark`='$remark',
                                        `updated_by`='$updated_by',
                                        `other_remark_1`='$other_remark_1',
                                        `other_remark_2`='$other_remark_2',
                                        `other_remark_3`='$other_remark_3',
                                        `other_remark_4`='$other_remark_4', 
                                        `created_time`=now()
                                  WHERE internal_item='$internal_item' AND  order_item='$order_item' AND item_color='$item_color' AND color_code='$color_code' ";
                    } else {
                        $query = "INSERT INTO $table 
                                        (
                                            `internal_item`,
                                            `rbo`,
                                            `order_item`,
                                            `color_code`,
                                            `item_color`,
                                            `material_code`,
                                            `material_des`,
                                            `ribbon_code`,
                                            `ink_des`,
                                            `width`,
                                            `height`,
                                            `note`,
                                            `blank_gap`,
                                            `remark`,
                                            `updated_by`,
                                            `other_remark_1`,
                                            `other_remark_2`,
                                            `other_remark_3`,
                                            `other_remark_4`
                                        )
                                VALUES ( 
                                            '$internal_item',
                                            '$rbo',
                                            '$order_item',
                                            '$color_code',
                                            '$item_color',
                                            '$material_code',
                                            '$material_des',
                                            '$ribbon_code',
                                            '$ink_des',
                                            '$width',
                                            '$height',
                                            '$ghi_chu_item',
                                            '$blank_gap',
                                            '$remark',
                                            '$updated_by',
                                            '$other_remark_1',
                                            '$other_remark_2',
                                            '$other_remark_3',
                                            '$other_remark_4' 
                                        )";
                    }
                    //excute
                    $result = mysqli_query($conn, $query);
                    if($result === FALSE) { die(mysql_error()); }
                    //FALSE
                    if(!$result){
                        $response = [
                            'state' 	=>	false,
                            'name'   	=>	$filename,
                            'extra'		=>	[
                                'mess'  => 'Có lỗi xảy ra trong quá trình import, Query: '.$query
                            ]
                        ];
                        echo json_encode($response);die;
                    }

                }//end for
                
                //	TRUE
                if($result){
                    $response = [
                        'state' 	=>	true,
                        'filename'  =>	$filename,
                        'extra'		=>	[
                            'mess'  => 'Import dữ liệu thành công, Website sẽ reload!'
                        ]
                    ];
                }//end result OK

            }else{
                $response = [
                    'state' 	=>	false,
                    'filename'  =>	$filename,
                    'extra'		=>	[
                        'mess'  => 'Kiểm tra lại dữ liệu file EXCEL'
                    ]
                ];
            }				
        }
        
        echo json_encode($response);
    }
    
    /*
    
    HTML4 MODE
    
    response format:
    
    to cancel uploading
    {state: 'cancelled'}
    
    if upload was good, you need to specify state=true, name - will passed in form.send() as serverName param, size - filesize to update in list
    {state: 'true', name: 'filename', size: 1234}
    
    */
    
    if (@$_REQUEST["mode"] == "html4") {
        header("Content-Type: text/html");
        if (@$_REQUEST["action"] == "cancel") {
            print_r("{state:'cancelled'}");
        } else {
            $filename = $_FILES["file"]["name"];
            move_uploaded_file($_FILES["file"]["tmp_name"], "uploaded/".$filename);
            print_r("{state: true, name:'".str_replace("'","\\'",$filename)."', size:".$_FILES["file"]["size"]/*filesize("uploaded/".$filename)*/.", extra: {info: 'just a way to send some extra data', param: 'some value here'}}");
        }
    }