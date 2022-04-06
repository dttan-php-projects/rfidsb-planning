<?php
set_time_limit(6000); 
date_default_timezone_set('Asia/Ho_Chi_Minh');
date_default_timezone_set('Asia/Ho_Chi_Minh');
require_once ( "../define_constant_system.php");
require_once (PATH_MODEL . "/__connection.php");  
require_once ('../spreadsheet/php-excel-reader/excel_reader2.php');
require_once ('../spreadsheet/SpreadsheetReader.php');
if(!isset($_COOKIE["VNRISIntranet"])) header('Location: login.php');//check login

$table = "no_cbs";
$conn = getConnection();

$UPDATED_BY = isset($_COOKIE["VNRISIntranet"]) ? $_COOKIE["VNRISIntranet"] : "";

if (isset($_POST["submit"])) {

    $allowedFileType = ['application/vnd.ms-excel', 'application/octet-stream', 'text/xls', 'text/xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];

    if (in_array($_FILES["file"]["type"], $allowedFileType)) {

        $file_name = 'DB1Line_' . $UPDATED_BY . '_' . date('Y-m-d_H-i-s') . '.xlsx';
        $targetPath = '../Excel/' . $file_name;
        
        // hàm move_uploaded_file k sử dụng được (có thể do bị hạn chế quyền của thư mục tmp)
        if (copy($_FILES['file']['tmp_name'], $targetPath)) {
            // echo "Đã upload file : $targetPath <br />\n";
        } else {
            $type = "Error";
            $message = "Problem in Importing Excel Data";
        }
        
        $Reader = new SpreadsheetReader($targetPath);
        $sheetCount = count($Reader->sheets());
        
        $query_insert_data = '';
        
        for ($i = 1; $i <= $sheetCount; $i++) {
            $Reader->ChangeSheet($i);
            $error_row = $i + 1;

            foreach ($Reader as $value) {
                //get data 
                $internal_item          = !empty($value[0])?addslashes($value[0]):'';
                $rbo                    = !empty($value[1])?addslashes($value[1]):'';
                $order_item             = !empty($value[2])?addslashes($value[2]):'';
                $material_code          = !empty($value[3])?addslashes($value[3]):'';
                $ribbon_code            = !empty($value[4])?addslashes($value[4]):'';
                $material_des           = !empty($value[5])?addslashes($value[5]):'';
                $ink_des                = !empty($value[6])?addslashes($value[6]):'';
                $width                  = !empty($value[7])?addslashes($value[7]):0;//float
                $width                  = (float)$width;
                $height                 = !empty($value[8])?addslashes($value[8]):0;//float
                $height                  = (float)$height;
                $blank_gap              = !empty($value[9])?addslashes($value[9]):0;//float
                $blank_gap              = (float)$blank_gap;
                $ghi_chu_item           = !empty($value[10])?addslashes($value[10]):'';
                $note_rbo               = !empty($value[11])?addslashes($value[11]):'';
                $remark_GIAY            = !empty($value[12])?addslashes($value[12]):'';
                $lay_sample_15_pcs      = !empty($value[13])?addslashes($value[13]):'';
                $remark_MUC             = !empty($value[14])?addslashes($value[14]):'';
                $first_order            = !empty($value[15])?addslashes($value[15]):'';
                $pcs_sht                = !empty($value[16])?addslashes($value[16]):'';//float
                $kind_of_label          = !empty($value[17])?addslashes($value[17]):'';
                $STANDARD_LT             = !empty($value[18])?addslashes($value[18]):'';
                $note                   = !empty($value[19])?addslashes($value[19]):'';
                $note_price             = !empty($value[20])?addslashes($value[20]):'';
                $note_color             = !empty($value[21])?addslashes($value[21]):'';
                $OTHER_REMARK_1         = !empty($value[22])?addslashes($value[22]):'';
                $OTHER_REMARK_2         = !empty($value[23])?addslashes($value[23]):'';
                $OTHER_REMARK_3         = !empty($value[24])?addslashes($value[24]):'';
                $OTHER_REMARK_4         = !empty($value[25])?addslashes($value[25]):'';

                // Kiểm tra dữ liệu tồn tại hay chưa, dựa vào internal_item, material_code, nếu tồn tại thì update
                // Chưa tồn tại thì thêm mới
                if (!empty($internal_item)) {
                    
                    if (strpos(strtoupper($internal_item), 'INTERNAL') !== false) continue;

                    $query_check = "SELECT internal_item FROM $table WHERE internal_item = '" . $internal_item . "' ";
                    $result_check = mysqli_query($conn, $query_check);
                    if (mysqli_num_rows($result_check) > 0) {
                        $query = "UPDATE $table
                                    SET   `rbo`='$rbo',`order_item`='$order_item',`material_code`='$material_code',`ribbon_code`='$ribbon_code',
                                        `material_des`='$material_des',`ink_des`='$ink_des',`width`='$width',`height`='$height',
                                        `pcs_sht`='$pcs_sht',`ghi_chu_item`='$ghi_chu_item',`note_rbo`='$note_rbo',`remark_GIAY`='$remark_GIAY',
                                        `lay_sample_15_pcs`='$lay_sample_15_pcs',`remark_MUC`='$remark_MUC',`first_order`='$first_order',
                                        `blank_gap`='$blank_gap',`kind_of_label`='$kind_of_label',`note`='$note',`note_price`='$note_price',
                                        `note_color`='$note_color',`UPDATED_BY`='$UPDATED_BY',`STANDARD_LT`='$STANDARD_LT',
                                        `OTHER_REMARK_1`='$OTHER_REMARK_1',`OTHER_REMARK_2`='$OTHER_REMARK_2',`OTHER_REMARK_3`='$OTHER_REMARK_3',
                                        `OTHER_REMARK_4`='$OTHER_REMARK_4', `CREATED_DATE_TIME`=now()
                                    WHERE internal_item='$internal_item'; ";
                        $result = mysqli_query($conn, $query);
                        if (!$result) break;

                    } else {
                        $query_insert_data .= "INSERT INTO $table (`internal_item`,`rbo`,`order_item`,`material_code`,`ribbon_code`,`material_des`,`ink_des`,`width`,`height`,`pcs_sht`,`ghi_chu_item`,`note_rbo`,`remark_GIAY`,`lay_sample_15_pcs`,`remark_MUC`,`first_order`,`blank_gap`,`kind_of_label`,`note_price`,`note_color`,`UPDATED_BY`,`STANDARD_LT`,`OTHER_REMARK_1`,`OTHER_REMARK_2`,`OTHER_REMARK_3`,`OTHER_REMARK_4` )
                                    VALUES ( '$internal_item','$rbo','$order_item','$material_code','$ribbon_code','$material_des','$ink_des','$width','$height','$pcs_sht','$ghi_chu_item','$note_rbo','$remark_GIAY','$lay_sample_15_pcs','$remark_MUC','$first_order','$blank_gap','$kind_of_label','$note_price','$note_color','$UPDATED_BY','$STANDARD_LT','$OTHER_REMARK_1','$OTHER_REMARK_2','$OTHER_REMARK_3','$OTHER_REMARK_4' );";  
                    }

                } else {
                    $type = "Error";
                    $message = " Empty the " . $error_row . "th row Internal_item data";
                }

            } // for 


        } // for
        

        // insert
        if (!empty($query_insert_data)) {
            
            $result = mysqli_multi_query($conn, $query_insert_data);
            if (!$result) {
                $response = [
                    'state' 	=>	false,
                    'extra'		=>	[ 'mess'  => 'Import Data Error, Query: '. mysqli_error($conn) ]
                ];
                echo json_encode($response);die;
            }
            
        }


        if ($result == true) {
            $type = "Success";
            $message = "Excel Data Imported into the Database";
        } else {
            $type = "Error";
            $message = "Problem in Importing Excel Data, the " . $error_row . "th row ";
        }
    } else {
        $type = "Error";
        $message = "Invalid File Type. Upload Excel File.";
    }
}

if (isset($message)) {
    $result = $type . '. ' . $message;
} else {
    $result = '';
}

?>
<script>
    var message = '<?php  echo $result; ?>';
    
    alert(message);
    window.location="../";
    
</script>
