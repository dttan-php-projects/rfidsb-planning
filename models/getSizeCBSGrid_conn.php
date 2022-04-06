<?php
require_once ( "../define_constant_system.php");
require_once (PATH_MODEL . "/__connection.php");

require_once (PATH_DATA . "/detachedSOLINE.php");
require_once (PATH_DATA . "/formatDate.php");

function getAttachment($SOLine) {

    $conn = getConnection138();
    $table_vnso = "vnso";
    $table_vnso_total = "vnso_total";

    $Attachment = "";
    if(strpos($SOLine, "-") == 8) {
        $ORDER_NUMBER = explode("-",$SOLine)[0];
        $LINE_NUMBER = explode("-",$SOLine)[1];
        
        $Result = "";
        $sql = "SELECT VIRABLE_BREAKDOWN_INSTRUCTIONS FROM $table_vnso WHERE ORDER_NUMBER = '$ORDER_NUMBER' AND LINE_NUMBER = '$LINE_NUMBER' ORDER BY ID DESC LIMIT 0,1";
        $rowsResult = mysqli_query($conn, $sql);
        if (mysqli_num_rows($rowsResult)) {
            $Result = mysqli_fetch_array($rowsResult);
        } else {
            $sql = "SELECT VIRABLE_BREAKDOWN_INSTRUCTIONS FROM $table_vnso_total WHERE ORDER_NUMBER = '$ORDER_NUMBER' AND LINE_NUMBER = '$LINE_NUMBER' ORDER BY ID DESC LIMIT 0,1";
            $rowsResult = mysqli_query($conn, $sql);
            if (mysqli_num_rows($rowsResult)) {
                $Result = mysqli_fetch_array($rowsResult);
            }
            
        }

        if (!empty($Result)) {
            $Attachment = $Result['VIRABLE_BREAKDOWN_INSTRUCTIONS'];
        }

    }

    if ($conn) mysqli_close($conn);

    return $Attachment;

}

function getSize_New($SOLine, $print_type ) {
    $conn = getConnection138();
    $table_vnso_size = "vnso_size";
    $table_vnso_size_oe = "vnso_size_oe";

    $dataResults = array();
    if(strpos($SOLine, "-") == 8) {
        $ORDER_NUMBER = explode("-",$SOLine)[0];
        $LINE_NUMBER = explode("-",$SOLine)[1];
        
        $Result = array();

        $where = " ORDER_NUMBER = '$ORDER_NUMBER' AND LINE_NUMBER = '$LINE_NUMBER' ORDER BY SIZE ASC; ";
        $sql = "SELECT ORDER_NUMBER, LINE_NUMBER, SIZE, COLOR, MATERIAL, QTY FROM $table_vnso_size WHERE $where ";
        $rowsResult = mysqli_query($conn, $sql);
        if (mysqli_num_rows($rowsResult)) {
            $Result = mysqli_fetch_all($rowsResult, MYSQLI_ASSOC);
        } else {
            $sql = "SELECT ORDER_NUMBER, LINE_NUMBER, SIZE, COLOR, MATERIAL, QTY FROM $table_vnso_size_oe WHERE $where ";
            $rowsResult = mysqli_query($conn, $sql);
            if (mysqli_num_rows($rowsResult)) {
                $Result = mysqli_fetch_all($rowsResult, MYSQLI_ASSOC);
            }
            
        }
        
        if (!empty($Result)) {
            
            foreach ($Result as $value ) {
                $size = trim((string)$value['SIZE']);
                if ($size == '' || strtolower($print_type) == 'cbs' ) {
                    $size = trim($value['COLOR']);
                }
                $qty = (int)$value['QTY'];

                if (empty($dataResults) ) {
                    $dataResults[$size] = $qty;
                } else {
                    $exist_size = false;
                    foreach ($dataResults as $key => $data_value ) {
                        // so sánh key (size) có giống nhau thì cộng qty lại, không thì thêm mới
                        if ($size == $key ) {
                            $dataResults[$size] += $qty;
                            $exist_size = true;
                            break;
                        }
                    }

                    if ($exist_size == false ) {
                        $dataResults[$size] = $qty;
                    }
                }

                
            }
        }

    }

    if ($conn) mysqli_close($conn);

    return $dataResults;
}


function getSize($SOLine){

	$string = getAttachment($SOLine);

	//init var
    $dataResults = [];
    $size = $qty = '';
	$errorCount = $check_exist = $pause = 0;
    $sizepos = $qtypos = '';

    //loại bỏ các khoảng trắng
    $string = str_replace(" ", "",$string);
    if (strpos($string, ";Total")!==false ) {

    } else if (strpos($string,"Total")!==false ) {
        $string = str_replace("Total", ";Total",$string);
    }

    //Lấy Ký tự cuối check xem phải là ký tự: ^ hay k, k phải thì trả về lỗi
    $check = substr( $string,  strlen($string)-1, 1 );
    
    if ($check !== '^') {$pause = 1;}

	//Tách chuỗi thành mảng, mỗi phần tử có các nội dung size, color, qty, material_code
    $string_explode = explode(";",$string);
	
    //Đoạn code xác định vị trí size, color, qty, material_code.
    $maxpos = 0;
    foreach ($string_explode as $stringpos) {
        
        $detachedpos = explode(":",$stringpos);
        if (empty($stringpos)) {continue;}
        for ($i=0;$i<count($detachedpos);$i++) {
            
            if (strpos(strtoupper($detachedpos[$i]),"SIZE")!==false ) { 
                $sizepos=$i; 
                $maxpos = count($detachedpos); 
            }

            if (strpos(strtoupper($detachedpos[$i]),"COLOR")!==false || strpos(strtoupper($detachedpos[$i]),"COLOUR")!==false ) {
                $sizepos=$i; 
                $maxpos = count($detachedpos);
            }

            if (strpos(strtoupper($detachedpos[$i]),"QUANTITY")!==false || strpos(strtoupper($detachedpos[$i]),"QTY") !==false || strpos(strtoupper($detachedpos[$i]),"Q'TY") !==false){ $qtypos=$i;  }
        }
        if ($maxpos > 0) break;
        
    }
    
	//Nếu có data và có ký tự ^ (data k bị mất). Trường hợp ngược lại không them vào
	if(!empty($string_explode) && !$pause){
        
        foreach ($string_explode as $key => $value) {
            $check_exist=0;
            //get format string  detached.
            $detachedStringAll = trim($value);

            //check error. Nếu không đúng định dạng => return error
            if(substr_count($detachedStringAll,":")<1){//Trường hợp min = 2 col
                $errorCount++; continue;
            }

            //tách chuỗi thành mảng bởi ký tự :
            $detachedString = explode(":",$detachedStringAll);
            
            //check detachedString không đúng định dạng. Dừng
            if (count($detachedString) !=$maxpos) {$errorCount++; continue;}

            //get data
            if ( $sizepos!=$qtypos ) {

				//lấy dữ liệu //Trường hợp không lấy được cột data nào thì cho dữ liệu đó = rỗng.
                $size = !empty($detachedString[$sizepos]) ? trim($detachedString[$sizepos]) : '';
                $qty = !empty($detachedString[$qtypos]) ? $detachedString[$qtypos] : '';

				/* *** Check trường hợp OE không nhập dấu ; trước chữ Total, dấu ^, (còn thì thêm vào ...) *** */
				$character_error_arr = [
					'Total',
					'^'
                ];

				//Tìm các dữ liệu thừa để tách chuỗi thành mảng từ ký tự đó và lấy ra phần tử dữ liệu đã tách.
				foreach ($character_error_arr as $key => $value) {
					if (strpos(strtoupper($size),strtoupper($value))!==false) {
						$detached_tmp = explode($value,$size);
                        $size = strtoupper(trim($detached_tmp[0]));
					}
	
					if (strpos(strtoupper($qty),strtoupper($value))!==false) {
						$detached_tmp = explode($value,$qty);
						$qty = $detached_tmp[0];
					}
	
                } //end for

                // Check trường hợp size trống
                if (empty($size)) {
                    continue;
                }

            }

            if(!is_numeric($qty)){//kiểm tra qty có phải số không
                $errorCount++;
            } else {
                //check data ton tai chua, neu ton tai => cong them vao qty
                if(array_key_exists($size,$dataResults)){
                    $size_exist = 0;
                    foreach($dataResults as $key_size => $key_qty){
                        if($key_size==$size){
                            $size_exist+= $key_qty;
                        }
                    }												
                    $dataResults[$size] = $qty+$size_exist;
                }else{
                    $dataResults[$size] = $qty;
                }

            }

        }

		// //return result data
		return $dataResults;

	}

}

//save_new_size 
function getOnNewSize($SOLine){

	//connect 
	$conn = getConnection(); 
	
    $ArrayMain = array();
    $sql = "SELECT * FROM rfid_po_size_cbs_save WHERE so_line = '$SOLine';";
    $Attachment = "";
	//$rowsResult = MiQuery($sql, $dbMi2);
	$rowsResult = mysqli_query($conn, $sql);
	$rowsResult = mysqli_fetch_array($rowsResult);
    if($rowsResult){
        foreach ($rowsResult as $key => $valueNewSize) {
            $size = $valueNewSize['S_SIZE'];
            $quantity = $valueNewSize['S_QTY'];
            $ArrayMain[$size] = $quantity;
        }            
    }

    if ($conn) mysqli_close($conn);
    
    return  $ArrayMain;
}

header("Content-Type: application/json");

$data = $_POST['data'];

// $data = '{"so_lines":["44879730-1"]}';
// $data = '{"so_lines":["44856318-1"]}';

$formatData = json_decode($data,true);
if(!empty($formatData)){
    $so_lines = $formatData['so_lines'];
    $print_type = $formatData['print_type'];

    if(!empty($so_lines)){
        $SOLine = $so_lines[0];
        if($SOLine){
            $arr_so = [];
            $arr_size = [];
            $check_empty = 0;
            foreach($so_lines as $key => $value){

                // $data_size = getSize_New($value, $print_type);
                // if(count($data_size)==0){
                    // load on new size
                    $data_size = getSize($value);
                    if(count($data_size)==0){
                        $check_empty = 1;                
                        break;
                    }else{
                        foreach ($data_size as $key => $value_size_new) {
                            $arr_size[]=$key;
                        }
                        $arr_so[$value] = $data_size;
                    }                
                // }else{
                //     foreach ($data_size as $key => $value_size) {
                //         $arr_size[]=$key;
                //     }
                //     $arr_so[$value] = $data_size;
                // }
            }
            if($check_empty){
                $response = [
                    'status' => false,
                    'mess' =>  'Empty Size',
                    'data' => null,
                    'size' => []
                ];
            }else{            
                $arr_size = array_unique($arr_size);
                $arr_tmp=[];
                foreach ($arr_size as $key=>$value) {
                    $arr_tmp[]=$value;
                }
                $response = [
                    'status' => true,
                    'mess' =>  'Have Sizes',
                    'data' => $arr_so,
                    'size' => $arr_tmp
                ]; 
            }
        }else{
            $response = [
                'status' => false,
                'mess' =>  'Empty Size',
                'data' => null,
                'size' => []
            ];
        }               
    }else{
        $response = [
            'status' => false,
            'mess' =>  'Empty Size',
            'data' => null,
            'size' => []
        ];
    }
    echo json_encode($response);
}
?>