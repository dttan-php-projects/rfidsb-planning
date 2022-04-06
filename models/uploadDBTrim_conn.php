<?php
    //check login
    if(!isset($_COOKIE["VNRISIntranet"])) header('Location: login.php');//check login
    date_default_timezone_set('Asia/Ho_Chi_Minh');
    require_once ( "../define_constant_system.php");
    require_once (PATH_MODEL . "/__connection.php");  
    //connect host
    $table      = "database_trim";
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
                    'mess'  => 'File dữ liệu import quá lớn, Vui lòng kiểm tra lại',
                ]
            ];
        }else if(!in_array($fileType,$excelType)) {
            $response = [
                'state' 	=>	false,
                'filename'   	=>	$filename,
                'extra'		=>	[
                    'mess'  => 'File dữ liệu phải là EXCEL, Vui lòng kiểm tra lại',
                ]
            ];
        } else {

            $data = getFileExcel($_FILES['file']["tmp_name"]);	
            if(!empty($data)){
                
                
                $UPDATED_BY = $_COOKIE["VNRISIntranet"];//get user
                foreach($data as $key => $value){
                    //get data 
                    $INTERNAL_ITEM          = !empty($value[0])?addslashes($value[0]):'';
                    $MATERIAL_CODE          = !empty($value[1])?addslashes($value[1]):'';
                    $MATERIAL_DES          = !empty($value[2])?addslashes($value[2]):'';
                    $RIBBON_CODE            = !empty($value[3])?addslashes($value[3]):'';
                    $RIBBON_DES            = !empty($value[4])?addslashes($value[4]):'';
                    $CHIEU_DAI              = !empty($value[5])?addslashes($value[5]):'';
                    $CHIEU_NGANG            = !empty($value[6])?addslashes($value[6]):'';
                    $RBO                    = !empty($value[7])?addslashes($value[7]):'';
                    $ORDER_ITEM             = !empty($value[8])?addslashes($value[8]):'';
                    $REMARK                 = !empty($value[9])?addslashes($value[9]):'';
                    $REMARK_MUC             = !empty($value[10])?addslashes($value[10]):'';
                    $MACHINE                = !empty($value[11])?addslashes($value[11]):'';
                    $REMARK_GIAY            = !empty($value[12])?addslashes($value[12]):'';
                    $OTHER_REMARK_1         = !empty($value[13])?addslashes($value[13]):'';
                    $OTHER_REMARK_2         = !empty($value[14])?addslashes($value[14]):'';
                    $OTHER_REMARK_3         = !empty($value[15])?addslashes($value[15]):'';
                    $OTHER_REMARK_4         = !empty($value[16])?addslashes($value[16]):'';
                    
                    //update/insert data
                    $query_check = "SELECT INTERNAL_ITEM FROM $table WHERE INTERNAL_ITEM='$INTERNAL_ITEM' ";
                    $result_check = mysqli_query($conn, $query_check);
                    $num_check = mysqli_num_rows($result_check);
                    if ($num_check>0) {//// update
                        $query = "UPDATE $table
                                  SET   `MATERIAL_CODE`='$MATERIAL_CODE',`MATERIAL_DES`='$MATERIAL_DES',`RIBBON_CODE`='$RIBBON_CODE',`RIBBON_DES`='$RIBBON_DES',`CHIEU_DAI`='$CHIEU_DAI',`CHIEU_NGANG`='$CHIEU_NGANG',`RBO`='$RBO',`ORDER_ITEM`='$ORDER_ITEM',`REMARK`='$REMARK',`REMARK_MUC`='$REMARK_MUC',`MACHINE`='$MACHINE',`REMARK_GIAY`='$REMARK_GIAY',`UPDATED_BY`='$UPDATED_BY',`OTHER_REMARK_1`='$OTHER_REMARK_1',`OTHER_REMARK_2`='$OTHER_REMARK_2',`OTHER_REMARK_3`='$OTHER_REMARK_3',`OTHER_REMARK_4`='$OTHER_REMARK_4', `CREATED_DATE_TIME`=now()
                                  WHERE  INTERNAL_ITEM='$INTERNAL_ITEM' ";
                    } else {
                        $query = "INSERT INTO $table (`INTERNAL_ITEM`,`MATERIAL_CODE`,`RIBBON_CODE`,`CHIEU_DAI`,`CHIEU_NGANG`,`RBO`,`ORDER_ITEM`,`REMARK`,`REMARK_MUC`,`MACHINE`,`REMARK_GIAY`,`UPDATED_BY`,`OTHER_REMARK_1`,`OTHER_REMARK_2`,`OTHER_REMARK_3`,`OTHER_REMARK_4`)
                                  VALUES ( '$INTERNAL_ITEM','$MATERIAL_CODE','$RIBBON_CODE','$CHIEU_DAI','$CHIEU_NGANG','$RBO','$ORDER_ITEM','$REMARK','$REMARK_MUC','$MACHINE','$REMARK_GIAY','$UPDATED_BY','$OTHER_REMARK_1','$OTHER_REMARK_2','$OTHER_REMARK_3','$OTHER_REMARK_4' )";
                    }
                    //excute
                    $result = mysqli_query($conn, $query);
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
                            'mess'  => 'Import dữ liệu thành công, Website sẽ reload!!!!'
                        ]
                    ];
                }//end result OK

            }else{
                $response = [
                    'state' 	=>	false,
                    'filename'  =>	$filename,
                    'extra'		=>	[
                        'mess'  => 'Kiểm tra lại dữ liệu file EXCEL',
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