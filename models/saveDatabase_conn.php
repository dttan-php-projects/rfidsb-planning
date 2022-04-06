<?php
    if(!isset($_COOKIE["VNRISIntranet"])) { header('Location: login.php'); } else { $PO_CREATED_BY = $_COOKIE["VNRISIntranet"]; }

    // PrefixNO được lấy từ hàm createPrefixNO hoặc do người dùng nhập vào
    function createNO($prefixNO, $PRINT_TYPE ) {

        $conn = getConnection();
        
        $prefixNO = trim($prefixNO);
        $suffixNO = '-00001';

        $YearMonth = date('ym');
        if ($PRINT_TYPE == 'trim' || $PRINT_TYPE == 'trim_macy' ) {
            $prefixNO_time = 'TRIM.RF'.$YearMonth;
        } else if ($PRINT_TYPE == 'pvh_rfid') {
            $prefixNO_time = 'RF'.$YearMonth;
        } else if ($PRINT_TYPE == 'ua_no_cbs' || $PRINT_TYPE == 'rfid' || $PRINT_TYPE == 'ua_cbs' || $PRINT_TYPE == 'cbs') {
            $prefixNO_time = 'RF'.$YearMonth;
        }
        
        // prefix dạng RF/ TRIM.RF
        $sql = "SELECT PO_NO, PO_CREATED_TIME FROM rfid_po_save WHERE PO_NO LIKE '$prefixNO%' ORDER BY PO_CREATED_TIME DESC LIMIT 0,1 ";
        $query = mysqli_query($conn, $sql);

        // Trường hợp  có dang NO theo tháng có trong save. Trường hợp ngược lại, NO theo người dùng nhập
        if (mysqli_num_rows($query) > 0 ) {

            // Lấy PO_NO mới nhất, dựa theo ngày tạo đơn
            $po_no_item = mysqli_fetch_array($query, MYSQLI_ASSOC );
            $po_no = $po_no_item['PO_NO'];

            $po_no_detached = explode('-', $po_no );
            
            // Tách PO_NO, Lấy giá trị tiền tố, hậu tố
            $prefixNO_save = $po_no_detached[0];
            $suffixNO_save = (int)$po_no_detached[1];

            $prefixNO = $prefixNO_save;

            // Trường hợp 1: User đã sửa NO có tháng > tháng hiện tại
                $prefixNO = $prefixNO_save;
                // tạo suffixNO
                $suffixNO = $suffixNO_save+1;
                $length = strlen((string)$suffixNO);//chuyển đổi thành string xem có mấy ký tự
                if ($length == 1 ) {
                    $suffixNO = '0000'.$suffixNO;
                } else if ($length == 2 ) {
                    $suffixNO = '000'.$suffixNO;
                } else if ($length == 3 ) {
                    $suffixNO = '00'.$suffixNO;
                } else if ($length == 4 ) {
                    $suffixNO = '0'.$suffixNO;
                } else if ($length == 5 ) {
                    $suffixNO = $suffixNO;
                }

                $suffixNO = "-" . $suffixNO;


        } 

        // results
        return $prefixNO . $suffixNO;

    }

    function checkNOExist($PO_NO) {
        $conn       = getConnection();
        $sql = "SELECT PO_NO FROM rfid_po_save WHERE PO_NO = '$PO_NO' ORDER BY PO_CREATED_TIME DESC LIMIT 0,1 ";
        $query = mysqli_query($conn, $sql);
        if (mysqli_num_rows($query) > 0 ) {
            return true;
        } else {
            return false;
        }
    }

    function checkSOLineExist($SO_LINE) {
        $conn = getConnection();
        $sql = "SELECT `SO_PO_NO` FROM rfid_po_soline_save WHERE `SO_LINE` = '$SO_LINE' ";
        $query = mysqli_query($conn, $sql);
        if (mysqli_num_rows($query) > 0 ) {
            return true;
        } else {
            return false;
        }
    }

    function getNikeWorldonGPM($SO_LINE) {

        $GPM = '';
        //connect 
        $conn = getConnection138(); 
        $table_vnso = 'vnso';
        $table_total = 'vnso_total';

        //Lưu ý đây là trường hợp RFID nên chỉ nhập vào là SO_LINE (không nhập dạng SO) nên xử lý theo hướng nhập SO_LINE
        $SO_LINE_ARR 	= explode('-',$SO_LINE);
        $ORDER_NUMBER 	= $SO_LINE_ARR[0];
        $LINE_NUMBER 	= $SO_LINE_ARR[1];
        $query_gpm 		= "SELECT CUSTOMER_JOB FROM $table_vnso WHERE ORDER_NUMBER='$ORDER_NUMBER' AND LINE_NUMBER='$LINE_NUMBER' ORDER BY ID DESC LIMIT 0,1";
        $result_gpm 	= mysqli_query($conn, $query_gpm);
        if (!$result_gpm ) {
            return $GPM = '';
        } else {

            if(mysqli_num_rows($result_gpm) > 0 ) {
                $result_gpm = mysqli_fetch_array($result_gpm, MYSQLI_ASSOC);
            } else {
                $query_gpm 		= "SELECT CUSTOMER_JOB FROM $table_total WHERE ORDER_NUMBER='$ORDER_NUMBER' AND LINE_NUMBER='$LINE_NUMBER' ORDER BY ID DESC LIMIT 0,1";
                $result_gpm 	= mysqli_query($conn, $query_gpm);
                $result_gpm = mysqli_fetch_array($result_gpm, MYSQLI_ASSOC);
            }

            if (!empty($result_gpm) ) {

                $GPM = trim($result_gpm['CUSTOMER_JOB']);
                $GPM = (strpos($GPM, ' ') !==false ) ? str_replace(' ', '',$GPM) : $GPM;
    
                //Lấy số ký tự số GPM, Nếu có ký tự / thì tách ra thành mảng từ ký tự /.  
                // Sau đó, chạy vòng lặp mảng này, Từng phần tử loại bỏ các ký tự đặc biệt, chữ ra, nếu cái nào chỉ có số thôi thì lấy GPM
                $GPM_len = 0;
                if (strpos($GPM,'/' ) !== false ) {
                    // Trường hợp có dấu /
                    $GPM_DETACHED = explode('/', $GPM );
                    foreach ($GPM_DETACHED as $GPM_CHECK ) {
                        $GPM_CHECK = preg_replace('/[^0-9]/', '', $GPM_CHECK);
                        if (is_numeric($GPM_CHECK) ) {
                            $GPM_len = strlen($GPM_CHECK);
                            break;
                        }
                    }
    
                } else {
                    // Không có dấu /
                    $GPM_len = strlen($GPM); 
                }
    
                // Trường hợp này: Nếu GPM có nhiều số thì lấy số đầu
                if (strpos($GPM,',' ) !== false ) {
                    $GPM_DETACHED = explode(',', $GPM );
                    $GPM = $GPM_DETACHED[0];
                }
                
                // Loại bỏ các ký tự khác, chỉ giữ lại ký tự số
                $GPM = preg_replace('/[^0-9]/', '', $GPM);
                // Lấy đúng độ dài của GPM
                $GPM = is_numeric(substr($GPM,0,$GPM_len))?substr($GPM,0,$GPM_len):'';
    
            }

        }

        if ($conn) mysqli_close($conn);

        return $GPM;
        
    }

    function getBonusDate($lt ) 
	{

		// Get current date
		$dateCheck = getdate();
		$day = $dateCheck['mday'];
		$mon = $dateCheck['mon'];
		$year = $dateCheck['year'];

		// create date
		$date=date_create("$year-$mon-$day");
		// add bonus date
		date_add($date,date_interval_create_from_date_string("$lt days"));

		// formate
		$date = date_format($date,"d-M-y");

		// return 
		return $date;
		
	}

	function newPromiseDate($PO_RBO )
	{
		$_conn = getConnection();
		$data = array();
		$promise_date = '';

        if (strpos($PO_RBO, "&amp;") !== false ) {
            $PO_RBO = str_replace("&amp;", "&", $PO_RBO);
        }

		$table_rbo_lt = 'rfidsb_rbo_lt';
		$sql = "SELECT * FROM $table_rbo_lt ORDER BY `LT` ASC;";
		$results = mysqli_query($_conn, $sql);
		if (mysqli_num_rows($results) > 0) {
			$data = mysqli_fetch_all($results, MYSQLI_ASSOC);
			foreach ($data as $value ) {
				$rbo = strtoupper(trim($value['RBO']) );
                if (strpos($rbo, "&amp;") !== false ) {
                    $rbo = str_replace("&amp;", "&", $rbo);
                }

				if (strpos($PO_RBO, strtoupper($rbo)) !== false ) {
					$lt = $value['LT'];
					$promise_date = getBonusDate($lt );
					break;

				}
			}
		} 

		mysqli_close($_conn);

		return $promise_date;
	}

    // load 
    require_once ( "../define_constant_system.php");
    require_once (PATH_MODEL . "/__connection.php");
    require_once (PATH_MODEL . "/checkSOExist_conn.php");
    require_once (PATH_DATA . "/detachedSOLINE.php");
    require_once (PATH_DATA . "/formatDate.php");//format Date (-1 day)

    require_once (PATH_MODEL . "/createNO_All_conn.php");
    require_once (PATH_MODEL . "/automail_conn.php");


    //connect host
    $conn = getConnection();

    //get data 
    $data = $_POST['data'];
    // $data = '{"data_formNO":[{"PO_NO":"RF2202-08743","PO_SO_LINE":"69657245-1","PO_FORM_TYPE":"rfid","PO_INTERNAL_ITEM":"4-225332-000-00","PO_ORDER_ITEM":"RT-01S-600DPI","PO_GPM":"","PO_RBO":"H&M HENNES &amp; MAURITZ GBC AB","PO_SHIP_TO_CUSTOMER":"CONG TY TNHH GIAY DINH DAT","PO_CS":"Chau, Yen","PO_QTY":246,"PO_SAVE_DATE":"2022-02-22","PO_ORDERED_DATE":"21-Feb-22","PO_REQUEST_DATE":"23-Feb-22","PO_PROMISE_DATE":"","PO_LABEL_SIZE":"77 mm x 45 mm","PO_MATERIAL_CODE":"5-602920-236-00","PO_MATERIAL_QTY":253,"PO_MATERIAL_DES":"","PO_MATERIAL_REMARK":"","PO_INK_CODE":"9TR001110","PO_INK_QTY":20,"PO_INK_DES":"XC2111 84mm x 500M Paper core, MOQ: 16 rolls","PO_INK_REMARK":"","PO_MAIN_SAMPLE_LINE":"","PO_SAMPLE_15PCS":"","PO_SAMPLE":0,"PO_COUNT_SO_LINE":1,"PO_ORDER_TYPE_NAME":"VN GEN - VAT","PO_DATE_RECEIVED":"","PO_FILE_DATE_RECEIVED":"0"}],"data_GridSO":[{"SO_PO_NO":"RF2202-08743","SO_LINE":"69657245-1","SO_PO_QTY":246,"SO_INTERNAL_ITEM":"4-225332-000-00","SO_ORDER_ITEM":"RT-01S-600DPI","SO_WIDTH":77,"SO_HEIGHT":45,"COUNT_SO":1,"PACKING_INSTRUCTIONS":"","REMARK_SO_COMBINE":""}],"data_ink":[{"INK_PO_NO":"RF2202-08743","INK_SO_LINE":"69657245-1","INK_CODE":"9TR001110","INK_QTY":20,"INK_DES":"XC2111 84mm x 500M Paper core, MOQ: 16 rolls","INK_COUNT":1}],"data_material_no_cbs":[{"MN_PO_NO":"RF2202-08743","MN_PO_SO_LINE":"69657245-1","MN_MATERIAL_CODE":"5-602920-236-00","MN_MATERIAL_QTY":253,"MN_MATERIAL_DES":"","MN_COUNT":1}],"checkCombine":"0"}';
    
    if (empty($data) ) {
        $response = array (
            'status'  => 0,
            'message' => "Dữ liệu rỗng (0) "
        );
        echo json_encode($response); exit();
    } else {

        // get save data
        $formatData = json_decode($data,true);

        $dataForm = $formatData["data_formNO"]; //get data FormNO
        $dataGridSO = $formatData["data_GridSO"]; //get data Grid SO
        $FORM_TYPE = $dataForm[0]['PO_FORM_TYPE'];	//get form type

        // Dùng để check đơn NIKE + WORLDON combine RFID & THERMAL
        $checkCombine = isset($formatData['checkCombine']) ? (int)$formatData['checkCombine'] : 0;

        // check data
        if (empty($dataForm ) || empty($dataGridSO) || empty($FORM_TYPE) ) {
            
            $response = array (
                'status'  => 0,
                'message' => "Dữ liệu rỗng (1) "
            );
            echo json_encode($response); exit();
        }

        // check form data
        if ($FORM_TYPE == 'rfid' || $FORM_TYPE == 'ua_no_cbs' ) {
            
            $data_ink  = $formatData["data_ink"];
            $data_mn   = $formatData["data_material_no_cbs"];
            
            if (empty($data_ink) || empty($data_mn) ) {
                $response = array (
                    'status'  => 0,
                    'message' => "Dữ liệu rỗng (2) "
                );
                echo json_encode($response); exit();
            }
        } else if ($FORM_TYPE == 'trim' || $FORM_TYPE == 'trim_macy' || $FORM_TYPE == 'pvh_rfid' ) {
            
            $data_material_ink = $formatData["data_material_ink"];
            
            if (empty($data_material_ink) ) {
                $response = array (
                    'status'  => 0,
                    'message' => "Dữ liệu rỗng (3) "
                );
                echo json_encode($response); exit();
            }
        } else if ($FORM_TYPE == 'ua_cbs' || $FORM_TYPE == 'cbs' ) {
            
            $data_size = $formatData["data_size"];
            $data_material_cbs = $formatData["data_material_cbs"];

            if (empty($data_size) || empty($data_material_cbs) ) {
                $response = array (
                    'status'  => 0,
                    'message' => "Dữ liệu rỗng (4) "
                );
                echo json_encode($response); exit();
            }

        }


        //********GET DAT FORM (TO SAVE DATABASE)*************************************************** */
            // Get PO_NO
            $prefixNO                  = !empty($dataForm[0]['PO_NO'])?addslashes($dataForm[0]['PO_NO']):'';
            $PO_NO = '';
            if (empty($prefixNO ) ) {
                
                $response = array (
                    'status' => 0,
                    'message' => " Không lấy được số NO# "
                );
                echo json_encode($response); exit();

            } else {
                
                // Trường hợp đã tồn tại số NO# trong chương trình
                if (checkNOExist($prefixNO) == true ) {
                    $PO_NO = $prefixNO;
                } else {

                    // Trường hợp người dùng muốn gán lại số NO# đã bị xóa
                    if ($FORM_TYPE == 'trim' || $FORM_TYPE == 'trim_macy' || $FORM_TYPE == 'pvh_rfid' ) {
                        if (strlen($prefixNO) == 17 ) {
                            $PO_NO = $prefixNO;
                        }
                    } else {
                        if (strlen($prefixNO) == 12 ) {
                            $PO_NO = $prefixNO;
                        }

                    }

                    // tạo mới
                    if (empty($PO_NO ) ) {
                        $PO_NO = createNO($prefixNO, $FORM_TYPE );
                    }

                }

            }
            

            // Get data
            $PO_SO_LINE             = !empty($dataForm[0]['PO_SO_LINE'])?addslashes($dataForm[0]['PO_SO_LINE']):'';
            $PO_FORM_TYPE           = !empty($dataForm[0]['PO_FORM_TYPE'])?addslashes($dataForm[0]['PO_FORM_TYPE']):'';
            $PO_INTERNAL_ITEM       = !empty($dataForm[0]['PO_INTERNAL_ITEM'])?addslashes($dataForm[0]['PO_INTERNAL_ITEM']):'';
            $PO_ORDER_ITEM          = !empty($dataForm[0]['PO_ORDER_ITEM'])?addslashes($dataForm[0]['PO_ORDER_ITEM']):'';
            $PO_GPM                 = !empty($dataForm[0]['PO_GPM'])?addslashes($dataForm[0]['PO_GPM']):'';
            // $PO_RBO                 = !empty($dataForm[0]['PO_RBO'])?addslashes($dataForm[0]['PO_RBO']):'';
            $PO_RBO                 = !empty($dataForm[0]['PO_RBO'])? htmlspecialchars($dataForm[0]['PO_RBO'], ENT_QUOTES, 'UTF-8' ) :'';
            $PO_SHIP_TO_CUSTOMER    = !empty($dataForm[0]['PO_SHIP_TO_CUSTOMER'])?addslashes($dataForm[0]['PO_SHIP_TO_CUSTOMER']):'';
            $PO_CS                  = !empty($dataForm[0]['PO_CS'])?addslashes($dataForm[0]['PO_CS']):'';
            $PO_QTY                 = !empty($dataForm[0]['PO_QTY'])?addslashes($dataForm[0]['PO_QTY']):0; //int
            $PO_LABEL_SIZE          = !empty($dataForm[0]['PO_LABEL_SIZE'])?addslashes($dataForm[0]['PO_LABEL_SIZE']):'';
            $PO_MATERIAL_CODE       = !empty($dataForm[0]['PO_MATERIAL_CODE'])?addslashes($dataForm[0]['PO_MATERIAL_CODE']):'';
            $PO_MATERIAL_DES        = !empty($dataForm[0]['PO_MATERIAL_DES'])?addslashes($dataForm[0]['PO_MATERIAL_DES']):'';
            $PO_MATERIAL_DES = str_replace('"', ' ',$PO_MATERIAL_DES);
            $PO_MATERIAL_DES = str_replace('<br />\n', ' ',$PO_MATERIAL_DES);
            
            $PO_MATERIAL_QTY        = !empty($dataForm[0]['PO_MATERIAL_QTY'])?addslashes($dataForm[0]['PO_MATERIAL_QTY']):0; //int
            $PO_INK_CODE            = !empty($dataForm[0]['PO_INK_CODE'])?addslashes($dataForm[0]['PO_INK_CODE']):'';
            $PO_INK_DES             = !empty($dataForm[0]['PO_INK_DES'])?addslashes($dataForm[0]['PO_INK_DES']):'';
            
            $PO_INK_DES = str_replace('"', ' ',$PO_INK_DES);
            $PO_INK_DES = str_replace('<br />\n', ' ',$PO_INK_DES);
            $PO_INK_QTY             = !empty($dataForm[0]['PO_INK_QTY'])?addslashes($dataForm[0]['PO_INK_QTY']):0; //int
            $PO_COUNT_SO_LINE       = !empty($dataForm[0]['PO_COUNT_SO_LINE'])?addslashes($dataForm[0]['PO_COUNT_SO_LINE']):1;
            
            $PO_SAVE_DATE           = !empty($dataForm[0]['PO_SAVE_DATE'])?addslashes($dataForm[0]['PO_SAVE_DATE']):'';
            $PO_SAVE_DATE           = date("Y-m-d",strtotime($PO_SAVE_DATE));

            $PO_PROMISE_DATE        = !empty($dataForm[0]['PO_PROMISE_DATE'])?addslashes($dataForm[0]['PO_PROMISE_DATE']):'';

            /*  
                20220216: email:  Re: [RECAP] RFID SB - REVIEW BLANK PD
                Trường hợp PD trống ==> Áp dụng logic của Planning (lấy từ bảng rfidsb_rbo_lt) 

            */
            $REMARK_PROMISE_DATE = '';
            if (empty($PO_PROMISE_DATE) ) {
                $PO_PROMISE_DATE = '1970-01-01';
                $REMARK_PROMISE_DATE = "PD PPC: " . newPromiseDate($PO_RBO );
            } else {
                $PO_PROMISE_DATE        = date("Y-m-d",strtotime($PO_PROMISE_DATE));
                $PO_PROMISE_DATE_OK     = date("d-M-y",strtotime($PO_PROMISE_DATE));
            }

            $PO_REQUEST_DATE        = !empty($dataForm[0]['PO_REQUEST_DATE'])?addslashes($dataForm[0]['PO_REQUEST_DATE']):'';
            $PO_REQUEST_DATE        = date("Y-m-d", strtotime($PO_REQUEST_DATE) );
            $PO_REQUEST_DATE_OK     = date("d-M-y", strtotime($PO_REQUEST_DATE) );
            //$PO_REQUEST_DATE_OK     = formatData($PO_REQUEST_DATE_OK);

            $PO_ORDERED_DATE        = !empty($dataForm[0]['PO_ORDERED_DATE'])?$dataForm[0]['PO_ORDERED_DATE']:'';
            $PO_ORDERED_DATE        = date("Y-m-d", strtotime($PO_ORDERED_DATE) );
            $PO_ORDERED_DATE_OK     = date("d-M-y", strtotime($PO_ORDERED_DATE) );
            //$PO_ORDERED_DATE_OK     = formatData($PO_ORDERED_DATE_OK);

            $PO_MAIN_SAMPLE_LINE    = !empty($dataForm[0]['PO_MAIN_SAMPLE_LINE'])?addslashes($dataForm[0]['PO_MAIN_SAMPLE_LINE']):'';
            $PO_SAMPLE              = !empty($dataForm[0]['PO_SAMPLE'])?addslashes($dataForm[0]['PO_SAMPLE']):0; //int
            $PO_SAMPLE_15PCS        = !empty($dataForm[0]['PO_SAMPLE_15PCS'])?addslashes($dataForm[0]['PO_SAMPLE_15PCS']):'';
            $PO_MATERIAL_REMARK     = !empty($dataForm[0]['PO_MATERIAL_REMARK'])?addslashes($dataForm[0]['PO_MATERIAL_REMARK']):'';
            //$PO_MATERIAL_REMARK = preg_replace('/([^\pL\.\ ]+)/u', '', strip_tags($PO_MATERIAL_REMARK));
            $PO_MATERIAL_REMARK = str_replace('"', ' ',$PO_MATERIAL_REMARK);
            $PO_MATERIAL_REMARK = str_replace("\n", ' ',$PO_MATERIAL_REMARK);
            $PO_INK_REMARK          = !empty($dataForm[0]['PO_INK_REMARK'])?addslashes($dataForm[0]['PO_INK_REMARK']):'';
            //$PO_INK_REMARK = preg_replace('/([^\pL\.\ ]+)/u', '', strip_tags($PO_INK_REMARK));
            $PO_INK_REMARK = str_replace('"', ' ',$PO_INK_REMARK);
            $PO_INK_REMARK = str_replace("\n", ' ',$PO_INK_REMARK);

            
            /* 
                ==== NHÂN ĐÔI MỰC IN - FORM TRIM, TRIM MACY ====== 
                1. email: [RFID-SB] NHÃN IN HAI MẶT MỰC
                - Sử dụng material code làm điều kiện
                - Remark thêm vào
                2. email: [RFID-SB] NHÃN IN HAI MẶT MỰC 2022
                - Sử dụng internal item là điều kiện
                - Remark thêm vào
                
            */ 
            //Thêm điều kiện material_code đặc biệt 4-218393-236-00 và 4-219667-236-00, 4-219667-236-01 (code mới), 
            // remark mực: NHAN CHAY 2 MAT MUC IN (cũ là NHAN IN 2 MAT MUC) 
            //đây chỉ áp dụng cho form trim, trim_macy, tính nhân đôi mực phía dưới
            if ($FORM_TYPE == 'trim' || $FORM_TYPE == 'trim_macy') {
                // email: [RFID-SB] NHÃN IN HAI MẶT MỰC
                    $material_arr = array(
                        '4-218393-236-00', 
                        '4-219667-236-00',
                        '4-219667-236-01',
                        '5-603057-236-00',
                        '5-602682-385-00'// mới thêm vào 20211019
                    );
                    foreach( $material_arr as $material_check ) {
                        if ($PO_MATERIAL_CODE == $material_check ) {
                            $PO_INK_REMARK = !empty($PO_INK_REMARK) ? ($PO_INK_REMARK . ". " . "<br> NHAN CHAY 2 MAT MUC IN") : "NHAN CHAY 2 MAT MUC IN";
                        }
                    }

                // email: [RFID-SB] NHÃN IN HAI MẶT MỰC 2022 - 20220216
                    $internal_item_arr = array(
                        '4-232729-000-00', 
                        '4-232631-000-00'
                    );
                    foreach( $internal_item_arr as $item_check ) {
                        if ($PO_INTERNAL_ITEM == $item_check ) {
                            $PO_INK_REMARK = !empty($PO_INK_REMARK) ? ($PO_INK_REMARK . ". " . "<br> NHAN CHAY 2 MAT MUC IN") : "NHAN CHAY 2 MAT MUC IN";
                        }
                    }
                
            }

            $PO_CREATED_BY          = !empty($PO_CREATED_BY)?addslashes($PO_CREATED_BY):'';
            $PO_UPDATED_TIME = date('Y-m-d H:i:s');
            
            $PO_DATE_RECEIVED       = !empty($dataForm[0]['PO_DATE_RECEIVED'])?addslashes($dataForm[0]['PO_DATE_RECEIVED']):'';
            $PO_DATE_RECEIVED       = !empty($PO_DATE_RECEIVED) ? date("Y-m-d", strtotime($PO_DATE_RECEIVED) ) : '1970-01-01';
            $PO_FILE_DATE_RECEIVED  = !empty($dataForm[0]['PO_FILE_DATE_RECEIVED'])?addslashes($dataForm[0]['PO_FILE_DATE_RECEIVED']):'';

            //@REMARK_1: save to PO_REMARK_1
            $PO_ORDER_TYPE_NAME          = !empty($dataForm[0]['PO_ORDER_TYPE_NAME'])?addslashes($dataForm[0]['PO_ORDER_TYPE_NAME']):'';

            if (!$PO_SAVE_DATE) {

                $response = array (
                    'status' => 0,
                    'message' => "[ERROR 01.01]. Ngày LSX không được trống"
                );
                echo json_encode($response); exit();
            }
            
            // REMARK 1: ORDER_TYPE
            $PO_REMARK_1 = '';
            if ( strpos($PO_ORDER_TYPE_NAME,'REPLACEMENT')  !== false  ) {
                $PO_REMARK_1 = 'REPLACEMENT';
            } else if ( strpos($PO_ORDER_TYPE_NAME,'FAST TRACK') !== false ) {
                $PO_REMARK_1 = 'FAST TRACK';
            }
            
            //@REMARK_2: RBO
            $ATTACHMENT = '';
            $PO_SO_LINE_ARR = explode('-',$PO_SO_LINE);
            $PACKING_INSTRUCTIONS      = !empty($dataGridSO[0]['PACKING_INSTRUCTIONS'])?addslashes($dataGridSO[0]['PACKING_INSTRUCTIONS']):'';
            $PACKING_INSTRUCTIONS_ATTACHMENT = getPACKING_INSTRUCTIONS_ATTACHMENT($PO_SO_LINE_ARR[0], $PO_SO_LINE_ARR[1]);

            // lấy cột CUSTOMER PO NUMBER 
                $CUST_PO_NUMBER = getColAutomail($PO_SO_LINE_ARR[0],$PO_SO_LINE_ARR[1], 'CUST_PO_NUMBER');
                $CUST_PO_NUMBER= !empty($CUST_PO_NUMBER) ? addslashes($CUST_PO_NUMBER): $CUST_PO_NUMBER;

            $PO_REMARK_2 = '';
            if (strpos(strtoupper($PO_RBO), 'NIKE') !==false ) {
                if ($checkCombine == 1 ) {
                    $PO_REMARK_2 = 'NIKE-WORLDON';
                } else if ($checkCombine == 2 ) {
                    $PO_REMARK_2 = 'NIKE-TINHLOI';
                } else {
                    if ( strpos($PACKING_INSTRUCTIONS_ATTACHMENT,'HANGLE') !== false || strpos($PACKING_INSTRUCTIONS_ATTACHMENT,'HANG LE') !== false ) {
                        $PO_REMARK_2 = 'HÀNG LẺ';
                    } else  {
                        $LINE_RAP = (int)$PO_SO_LINE_ARR[1];
                        $LINE_RAP = $LINE_RAP + 1;
                        $PO_REMARK_2 = "Rap voi thermal <br />\n" . $PO_SO_LINE_ARR[0] . "-" . $LINE_RAP;
                    }                
                }
                

            } else if ( strpos(strtoupper($PO_RBO), 'POLO') !== false ) {
                if ( strpos($PO_SHIP_TO_CUSTOMER, 'TNHH May Tinh Loi') !==false || strpos($PO_SHIP_TO_CUSTOMER, 'May Tinh Loi') !==false  ) {
                        $PO_REMARK_2 = 'Đóng gói theo màu cho KH Tinh Lợi';
                }
            } else if ( strpos(strtoupper($PO_RBO), 'JC PENNEY') !== false ) {
                //@REMARK_2: RBO là JC PENNEY COMBO: 
                //remark: Mỗi SKU +1 pcs( Nhãn in), cuối cuộn cho thêm 10pcs( Nhãn trắng)
                $PO_REMARK_2 = 'Mỗi SKU +1 pcs( Nhãn in), cuối cuộn cho thêm 10pcs( Nhãn trắng). JC PENNEY ';

            }
            $PO_REMARK_2 = addslashes($PO_REMARK_2);
            
            //@REMARK_3: DONG HANG COMBINE CHUNG
            $PO_REMARK_3 = '';
            if ( strpos($PACKING_INSTRUCTIONS,'DONG HANG COMBINE CHUNG') !== false ) {
                $PO_REMARK_3 = 'DONG HANG COMBINE CHUNG NHIEU SO LINE VO 1 KIEN';
                $PO_REMARK_3 = addslashes($PO_REMARK_3);
            }

            //Remark 4: Chưa thêm vào,
            $PO_REMARK_4 = $REMARK_PROMISE_DATE;
        
        //******** SAVE DATABASE: po_save *************************************************** */
            // Nếu tồn tại thì update, ngược lại thì thêm mới
            if (checkNOExist($PO_NO) == true ) {
                // update
                $sql_form = "UPDATE rfid_po_save 
                            SET 
                                `PO_SO_LINE` = '$PO_SO_LINE',
                                `PO_FORM_TYPE` = '$PO_FORM_TYPE',
                                `PO_INTERNAL_ITEM` = '$PO_INTERNAL_ITEM',
                                `PO_ORDER_ITEM` = '$PO_ORDER_ITEM',
                                `PO_GPM` = '$PO_GPM',
                                `PO_RBO` = '$PO_RBO',
                                `PO_SHIP_TO_CUSTOMER` = '$PO_SHIP_TO_CUSTOMER',
                                `PO_CS` = '$PO_CS',
                                `PO_QTY` = '$PO_QTY',
                                `PO_LABEL_SIZE` = '$PO_LABEL_SIZE',
                                `PO_MATERIAL_CODE` = '$PO_MATERIAL_CODE',
                                `PO_MATERIAL_DES` = '$PO_MATERIAL_DES',
                                `PO_MATERIAL_QTY` = '$PO_MATERIAL_QTY',
                                `PO_INK_DES` = '$PO_INK_DES',
                                `PO_INK_QTY` = '$PO_INK_QTY',
                                `PO_COUNT_SO_LINE` = '$PO_COUNT_SO_LINE',
                                `PO_SAVE_DATE` = '$PO_SAVE_DATE',
                                `PO_PROMISE_DATE` = '$PO_PROMISE_DATE',
                                `PO_REQUEST_DATE` = '$PO_REQUEST_DATE',
                                `PO_ORDERED_DATE` = '$PO_ORDERED_DATE',
                                `PO_MAIN_SAMPLE_LINE` = '$PO_MAIN_SAMPLE_LINE',
                                `PO_SAMPLE` = '$PO_SAMPLE',
                                `PO_SAMPLE_15PCS` = '$PO_SAMPLE_15PCS',
                                `PO_MATERIAL_REMARK` = '$PO_MATERIAL_REMARK',
                                `PO_INK_REMARK` = '$PO_INK_REMARK',
                                `PO_REMARK_1` = '$PO_REMARK_1',
                                `PO_REMARK_2` = '$PO_REMARK_2',
                                `PO_REMARK_3` = '$PO_REMARK_3',
                                `PO_REMARK_4` = '$PO_REMARK_4',
                                `PO_CREATED_BY` = '$PO_CREATED_BY',
                                `PO_UPDATED_TIME` = '$PO_UPDATED_TIME',
                                `PO_PRINTED` = 0,
                                `PO_DATE_RECEIVED` = '$PO_DATE_RECEIVED',
                                `PO_FILE_DATE_RECEIVED` = '$PO_FILE_DATE_RECEIVED',
                                `PO_ORDER_TYPE_NAME` = '$PO_ORDER_TYPE_NAME',
                                `CUST_PO_NUMBER` = '$CUST_PO_NUMBER'
                            WHERE `PO_NO` = '$PO_NO' ";
            } else {
                // insert
                $sql_form = "INSERT INTO rfid_po_save (
                    `PO_NO`, `PO_SO_LINE`, `PO_FORM_TYPE`,`PO_INTERNAL_ITEM`,`PO_ORDER_ITEM`, `PO_GPM`, `PO_RBO`, `PO_SHIP_TO_CUSTOMER`, `PO_CS`,`PO_QTY`, 
                    `PO_LABEL_SIZE`, `PO_MATERIAL_CODE`, `PO_MATERIAL_DES`, `PO_MATERIAL_QTY`, `PO_INK_CODE`, `PO_INK_DES`, `PO_INK_QTY`, `PO_COUNT_SO_LINE`, `PO_SAVE_DATE`, `PO_PROMISE_DATE`, 
                    `PO_REQUEST_DATE`, `PO_ORDERED_DATE`, `PO_MAIN_SAMPLE_LINE`, `PO_SAMPLE`, `PO_SAMPLE_15PCS`, `PO_MATERIAL_REMARK`, `PO_INK_REMARK`, `PO_CREATED_BY`, `PO_REMARK_1`,`PO_DATE_RECEIVED`,
                    `PO_FILE_DATE_RECEIVED`,`PO_ORDER_TYPE_NAME`, `PO_REMARK_2`, `PO_REMARK_3`, `PO_REMARK_4`, `CUST_PO_NUMBER`
                )
                VALUES (
                    '$PO_NO','$PO_SO_LINE','$PO_FORM_TYPE','$PO_INTERNAL_ITEM','$PO_ORDER_ITEM','$PO_GPM','$PO_RBO','$PO_SHIP_TO_CUSTOMER','$PO_CS','$PO_QTY',
                    '$PO_LABEL_SIZE','$PO_MATERIAL_CODE','$PO_MATERIAL_DES','$PO_MATERIAL_QTY','$PO_INK_CODE','$PO_INK_DES','$PO_INK_QTY','$PO_COUNT_SO_LINE','$PO_SAVE_DATE','$PO_PROMISE_DATE',
                    '$PO_REQUEST_DATE','$PO_ORDERED_DATE','$PO_MAIN_SAMPLE_LINE','$PO_SAMPLE','$PO_SAMPLE_15PCS','$PO_MATERIAL_REMARK','$PO_INK_REMARK', '$PO_CREATED_BY','$PO_REMARK_1','$PO_DATE_RECEIVED',
                    '$PO_FILE_DATE_RECEIVED','$PO_ORDER_TYPE_NAME', '$PO_REMARK_2', '$PO_REMARK_3', '$PO_REMARK_4', '$CUST_PO_NUMBER'
                ) ";
            }

            // Check error
            $query_form = mysqli_query($conn, $sql_form);
           
            if (!$query_form ) {

                $response = array (
                    'status'  => 0,
                    'message' => "[ERROR 01.02] Save PO data error "
                );
                echo json_encode($response); exit(); 
            }

        //******** SAVE SOLINE: po_soline_save *************************************************** */
            $i=0;
            $COUNT_SO = !empty($dataGridSO[0]['COUNT_SO'])?(int)$dataGridSO[0]['COUNT_SO'] : 1;//default = 1

            while ( $i < $COUNT_SO ) {
                $SO_PO_NO           = $PO_NO;
                $SO_LINE            = !empty($dataGridSO[$i]['SO_LINE'])?addslashes($dataGridSO[$i]['SO_LINE']):'';
                $SO_PO_QTY          = !empty($dataGridSO[$i]['SO_PO_QTY'])?addslashes($dataGridSO[$i]['SO_PO_QTY']):0;
                $SO_PO_QTY          = (int)$SO_PO_QTY;
                $SO_INTERNAL_ITEM   = !empty($dataGridSO[$i]['SO_INTERNAL_ITEM'])?addslashes($dataGridSO[$i]['SO_INTERNAL_ITEM']):'';
                $SO_ORDER_ITEM      = !empty($dataGridSO[$i]['SO_ORDER_ITEM'])?addslashes($dataGridSO[$i]['SO_ORDER_ITEM']):'';
                $SO_WIDTH           = !empty($dataGridSO[$i]['SO_WIDTH'])?$dataGridSO[$i]['SO_WIDTH']:0;
                $SO_WIDTH           = (float)$SO_WIDTH;
                $SO_HEIGHT          = !empty($dataGridSO[$i]['SO_HEIGHT'])?$dataGridSO[$i]['SO_HEIGHT']:0;
                $SO_HEIGHT           = (float)$SO_HEIGHT;

                $REMARK_SO_COMBINE          = !empty($dataGridSO[$i]['REMARK_SO_COMBINE'])?$dataGridSO[$i]['REMARK_SO_COMBINE']:'';
                $REMARK_SO_COMBINE = (stripos($PO_REMARK_2, 'Rap voi thermal') !==false ) ? ($REMARK_SO_COMBINE . '. ' . $PO_REMARK_2) : $REMARK_SO_COMBINE;
                
                $GPM = getNikeWorldonGPM($SO_LINE);

                $SO_PO_CREATED_BY   = !empty($PO_CREATED_BY)?addslashes($PO_CREATED_BY):'';
                
                //check row empty
                if (empty($SO_PO_NO) || empty($SO_LINE) ) {

                    $response = array (
                        'status'  => 0,
                        'message' => "[ERROR 02.01]. Mã LSX (No) hoặc SO# trống"
                    );
                    echo json_encode($response); exit();
                    
                } else {
                    // Nếu tồn tại thì update, ngược lại thì thêm mới
                    if (checkSOLineExist($SO_LINE )  == true ) {
                        
                        // update
                        $sql_so = "UPDATE rfid_po_soline_save
                                    SET 
                                        `SO_PO_NO` = '$SO_PO_NO',
                                        `SO_PO_QTY` = $SO_PO_QTY,
                                        `SO_INTERNAL_ITEM` = '$SO_INTERNAL_ITEM',
                                        `SO_ORDER_ITEM` = '$SO_ORDER_ITEM',
                                        `SO_WIDTH` = $SO_WIDTH,
                                        `SO_HEIGHT` = $SO_HEIGHT,
                                        `SO_PO_CREATED_BY` = '$SO_PO_CREATED_BY',
                                        `SO_CREATED_DATE` = '$PO_UPDATED_TIME',
                                        `REMARK_SO_COMBINE` = '$REMARK_SO_COMBINE',
                                        `GPM` = '$GPM'

                                    WHERE `SO_LINE` = '$SO_LINE'  ";
                    } else {

                        // insert
                        $sql_so = "INSERT INTO  rfid_po_soline_save (
                            `SO_PO_NO`,`SO_LINE`,`SO_PO_QTY`,`SO_INTERNAL_ITEM`,`SO_ORDER_ITEM`,`SO_WIDTH`,`SO_HEIGHT`,`SO_PO_CREATED_BY`, `REMARK_SO_COMBINE`, `GPM`
                        )
                        VALUES (
                            '$SO_PO_NO','$SO_LINE',$SO_PO_QTY,'$SO_INTERNAL_ITEM','$SO_ORDER_ITEM',$SO_WIDTH,$SO_HEIGHT, '$SO_PO_CREATED_BY', '$REMARK_SO_COMBINE', '$GPM'
                        )";

                    }

 
                    $query_so = mysqli_query($conn, $sql_so);
                    if (!$query_so ) {
                        $response = array (
                            'status'  => 0,
                            'message' => "[ERROR 02.02] Save SO# data error "
                        );
                        echo json_encode($response); exit(); 
                    }

                }

                $i++;

            }//while


        //******** SAVE dữ liệu cho từng form *************************************************** */
            if ($FORM_TYPE == 'rfid' || $FORM_TYPE == 'ua_no_cbs' ) {
                
                if (!empty($data_ink) ) {
                    $i = $INK_ID = 0;
                    $INK_COUNT             = !empty($data_ink[0]['INK_COUNT'])?(int)$data_ink[0]['INK_COUNT']:1;

                    //su dung ham tao INK_NO
                    $INK_PO_NO           = $PO_NO;
                    $INK_NO_NEW        = createINK_NO($INK_PO_NO);

                    while ($i < $INK_COUNT) {

                        $INK_PO_SO_LINE    = !empty($data_ink[$i]['INK_SO_LINE'])?addslashes($data_ink[$i]['INK_SO_LINE']):'';

                        $INK_ID            = $i+1;
                        $INK_PO_FORM_TYPE  = !empty($FORM_TYPE)?addslashes($FORM_TYPE):'';
                        $INK_CODE          = !empty($data_ink[$i]['INK_CODE'])?addslashes($data_ink[$i]['INK_CODE']):'';
                        $INK_QTY           = !empty($data_ink[$i]['INK_QTY'])?addslashes($data_ink[$i]['INK_QTY']):0;
                        $INK_DES           = !empty($data_ink[$i]['INK_DES'])?addslashes($data_ink[$i]['INK_DES']):'';
                        $INK_PO_CREATED_BY = !empty($PO_CREATED_BY)?addslashes($PO_CREATED_BY):'';

                        //check empty
                        if (empty($INK_CODE) ) {

                            $response = array (
                                'status'  => 0,
                                'message' => "[ERROR 03.01]. INK CODE trống"
                            );
                            echo json_encode($response); exit();
                        }

                        // insert 
                        $sql_ink = "INSERT INTO  rfid_po_ink_no_cbs_save (
                            `INK_NO`,`INK_ID`, `INK_PO_NO`, `INK_PO_SO_LINE`, `INK_PO_FORM_TYPE`, `INK_CODE`, `INK_DES`,`INK_QTY`,`INK_REMARK`,`INK_PO_CREATED_BY` 
                        )
                        VALUES (
                            '$INK_NO_NEW','$INK_ID', '$INK_PO_NO', '$INK_PO_SO_LINE', '$INK_PO_FORM_TYPE', '$INK_CODE', '$INK_DES','$INK_QTY','', '$INK_PO_CREATED_BY' 
                        ) ";

                        $query_ink = mysqli_query($conn, $sql_ink);
                        
                        // check 
                        if (!$query_ink ) {
                            $response = array (
                                'status'  => 0,
                                'message' => "[ERROR 03.03]. Save Ink không thành công"
                            );
                            echo json_encode($response); exit();
                        }

                        $i++;

                    }//end while ink


                }//end if ink


                //MATERIAL NO CBS
                if ( !empty($data_mn) ) {

                    $i = $MN_ID = 0;
                    $MN_COUNT              = !empty($data_mn[0]['MN_COUNT'])?(int)$data_mn[0]['MN_COUNT']:1;
                    
                    $MN_PO_NO = $PO_NO;
                    $MN_NO_NEW         = createMATERIAL_NO($MN_PO_NO);

                    while ($i < $MN_COUNT) {

                        
                        $MN_PO_SO_LINE     = !empty($data_mn[$i]['MN_PO_SO_LINE'])?addslashes($data_mn[$i]['MN_PO_SO_LINE']):'';
                        
                        
                        $MN_ID             = $i+1;
                        $MN_PO_FORM_TYPE   = !empty($FORM_TYPE)?addslashes($FORM_TYPE):'';
                        $MN_MATERIAL_CODE  = !empty($data_mn[$i]['MN_MATERIAL_CODE'])?addslashes($data_mn[$i]['MN_MATERIAL_CODE']):'';
                        $MN_MATERIAL_QTY   = !empty($data_mn[$i]['MN_MATERIAL_QTY'])?addslashes($data_mn[$i]['MN_MATERIAL_QTY']):0;
                        $MN_MATERIAL_DES   = !empty($data_mn[$i]['MN_MATERIAL_DES'])?addslashes($data_mn[$i]['MN_MATERIAL_DES']):'';
                        $MN_PO_CREATED_BY  = !empty($PO_CREATED_BY)?addslashes($PO_CREATED_BY):'';
                        
                        //check
                        if (empty($MN_MATERIAL_CODE) ) {
                            
                            $response = array (
                                'status'    => 0,
                                'message'   => "[ERROR 04.01]. MATERIAL CODE trống "
                            );
                            echo json_encode($response); exit();
                        }

                        // insert
                        $sql_mn = "INSERT INTO  rfid_po_material_no_cbs_save (
                            `MN_NO`,`MN_ID`, `MN_PO_NO`, `MN_PO_SO_LINE`, `MN_PO_FORM_TYPE`, `MN_MATERIAL_CODE`, `MN_MATERIAL_DES`,`MN_MATERIAL_QTY`,`MN_MATERIAL_REMARK`,`MN_PO_CREATED_BY` 
                        )
                        VALUES (
                            '$MN_NO_NEW','$MN_ID', '$MN_PO_NO', '$MN_PO_SO_LINE', '$MN_PO_FORM_TYPE', '$MN_MATERIAL_CODE', '$MN_MATERIAL_DES','$MN_MATERIAL_QTY','', '$MN_PO_CREATED_BY' 
                        ) ";
                        
                        $query_mn = mysqli_query($conn, $sql_mn);

                        // check 
                        if (!$query_mn ) {
                            
                            $response = array (
                                'status'   => 0,
                                'message'  => "[ERROR 04.03]. Save Material no cbs không thành công"
                            );
                            echo json_encode($response); exit();

                        } 

                        $i++;

                    }//end while material no cbs

                }//end if material no cbs

                
            } else if ($FORM_TYPE == 'trim' || $FORM_TYPE == 'trim_macy' || $FORM_TYPE == 'pvh_rfid' ) {

                if ( !empty($data_material_ink) ) {
                    
                    $i = $MI_ID = 0;
                    $MI_COUNT = !empty($data_material_ink[0]['MI_COUNT'])?(int)$data_material_ink[0]['MI_COUNT']:1;

                    $MI_PO_NO = $PO_NO;
                    //set MI_NO
                    // Hàm này kiểm tra MI_NO đã có chưa, nếu có thì xóa và lấy lại số MI_NO, Nếu không thì tạo mới
                    $MI_NO_NEW = createMATERIAL_INK_NO($MI_PO_NO);

                    while ( $i < $MI_COUNT ) {

                        $MI_PO_SO_LINE     = !empty($data_material_ink[$i]['MI_PO_SO_LINE'])?addslashes($data_material_ink[$i]['MI_PO_SO_LINE']):'';

                        $MI_ID             = $i+1;
                        $MI_PO_FORM_TYPE   = !empty($FORM_TYPE)?addslashes($FORM_TYPE):'';
                        $MI_MATERIAL_CODE  = !empty($data_material_ink[$i]['MI_MATERIAL_CODE'])?addslashes($data_material_ink[$i]['MI_MATERIAL_CODE']):'';
                        $MI_MATERIAL_QTY   = !empty($data_material_ink[$i]['MI_MATERIAL_QTY'])?addslashes($data_material_ink[$i]['MI_MATERIAL_QTY']):0;//int
                        $MI_MATERIAL_DES   = !empty($data_material_ink[$i]['MI_MATERIAL_DES'])?htmlspecialchars($data_material_ink[$i]['MI_MATERIAL_DES'], ENT_QUOTES, 'UTF-8'):'';
                        $MI_INK_CODE       = !empty($data_material_ink[$i]['MI_INK_CODE'])?addslashes($data_material_ink[$i]['MI_INK_CODE']):'';
                        $MI_INK_QTY        = !empty($data_material_ink[$i]['MI_INK_QTY'])?addslashes($data_material_ink[$i]['MI_INK_QTY']):0;//int
                        $MI_INK_DES        = !empty($data_material_ink[$i]['MI_INK_DES'])?htmlspecialchars($data_material_ink[$i]['MI_INK_DES'], ENT_QUOTES, 'UTF-8'):'';
                        $MI_PO_CREATED_BY  = !empty($PO_CREATED_BY)?addslashes($PO_CREATED_BY):'';

                        //check empty
                        if (empty($MI_MATERIAL_CODE) ) {
                            $response = array (
                                'status'  => 0,
                                'message' => "[ERROR 05.01]. MATERIAL CODE trống"
                            );
                            echo json_encode($response); exit();

                        } 
                        
                        if (empty($MI_INK_CODE)  ) {
                            
                            $response = array (
                                'status'  => 0,
                                'message' => "[ERROR 05.02]. INK CODE trống"
                            );
                            echo json_encode($response); exit();

                        } 

                        // insert
                        $sql_mi = "INSERT INTO  rfid_po_material_ink_save (
                            `MI_NO`,`MI_ID`, `MI_PO_NO`, `MI_PO_SO_LINE`, `MI_PO_FORM_TYPE`, `MI_MATERIAL_CODE`, `MI_MATERIAL_DES`,`MI_MATERIAL_QTY`,`MI_INK_CODE`, `MI_INK_DES`,`MI_INK_QTY`, `MI_MATERIAL_INK_REMARK`, `MI_PO_CREATED_BY` 
                        )
                        VALUES (
                            '$MI_NO_NEW','$MI_ID','$MI_PO_NO','$MI_PO_SO_LINE','$MI_PO_FORM_TYPE','$MI_MATERIAL_CODE','$MI_MATERIAL_DES','$MI_MATERIAL_QTY','$MI_INK_CODE','$MI_INK_DES','$MI_INK_QTY', '', '$MI_PO_CREATED_BY' 
                        ) ";

                        $query_mi = mysqli_query($conn, $sql_mi);

                        // check
                        if (!$query_mi ) {
                            
                            $response = array (
                                'status'   => 0,
                                'message'  => "[ERROR 05.04]. Save Material và Ink không thành công"
                            );
                            echo json_encode($response); exit();

                        }
                        
                        $i++;

                    }//end while


                }//end if mi


            } else if ($FORM_TYPE == 'ua_cbs' || $FORM_TYPE == 'cbs' ) {
                
                //save data_size
                if ( !empty($data_size) ) {
                    
                    $i = $S_ID = 0;
                    $S_COUNT              = !empty($data_size[0]['SIZE_COUNT'])?(int)$data_size[0]['SIZE_COUNT']:1;
                    $S_PO_NO          = $PO_NO;
                    $S_NO_NEW         = createSize_NO($S_PO_NO);

                    while ( $i < $S_COUNT ) {
                        
                        $S_PO_SO_LINE     = !empty($data_size[$i]['SIZE_PO_SO_LINE'])?addslashes($data_size[$i]['SIZE_PO_SO_LINE']):'';
                        $S_ID             = $i+1;

                        $S_PO_FORM_TYPE   = !empty($FORM_TYPE)?addslashes($FORM_TYPE):'';
                        $S_SIZE           = (string)$data_size[$i]['SIZE'];
                        $S_LABEL_ITEM     = !empty($data_size[$i]['LABEL'])?addslashes($data_size[$i]['LABEL']):'';
                        $S_BASE_ROLL      = !empty($data_size[$i]['BASE_ROLL'])?addslashes($data_size[$i]['BASE_ROLL']):'';
                        $S_QTY            = !empty($data_size[$i]['SIZE_QTY'])?addslashes($data_size[$i]['SIZE_QTY']):0;//int
                        $S_PO_CREATED_BY  = !empty($PO_CREATED_BY)?addslashes($PO_CREATED_BY):'';

                        $S_INK_QTY        = !empty($data_size[$i]['S_INK_QTY'])?$data_size[$i]['S_INK_QTY']:0;//int

                        //check empty
                        if ($S_SIZE == '' || empty($S_LABEL_ITEM) ) {
                            
                            $response = array (
                                'status'  => 0,
                                'message' => "[ERROR 06.01]. SIZE hoặc LABEL trống"
                            );
                            echo json_encode($response); exit();

                        } 
                        
                        if (empty($S_BASE_ROLL) || empty($S_QTY) ) {
    
                            $response = array (
                                'status'  => 0,
                                'message' => "[ERROR 06.02]. BALL ROLL hoặc SIZE QTY trống"
                            );
                            echo json_encode($response); exit();
                        } 

                        // insert
                        $sql_s = "INSERT INTO  rfid_po_size_cbs_save (
                            `S_NO`,`S_ID`, `S_PO_NO`, `S_PO_SO_LINE`, `S_PO_FORM_TYPE`, `S_SIZE`, `S_LABEL_ITEM`,`S_BASE_ROLL`,`S_QTY`,`S_PO_CREATED_BY`,`S_INK_QTY` 
                        )
                        VALUES (
                            '$S_NO_NEW','$S_ID', '$S_PO_NO', '$S_PO_SO_LINE', '$S_PO_FORM_TYPE', '$S_SIZE', '$S_LABEL_ITEM', '$S_BASE_ROLL','$S_QTY','$S_PO_CREATED_BY','$S_INK_QTY' 
                        ) ";

                        $query_s = mysqli_query($conn, $sql_s);

                        // check 
                        if (!$query_s ) {
                            $response = array (
                                'status'   => 0,
                                'message'  => "[ERROR 06.04]. Save Size không thành công"
                            );
                            echo json_encode($response); exit();
                        }

                        $i++;


                    }//end while

                }//end if size

                //save data_material_cbs
                if ( !empty($data_material_cbs) ) {
                    $i = $M_ID = 0;
                    $M_COUNT = !empty($data_material_cbs[0]['M_COUNT'])?addslashes($data_material_cbs[0]['M_COUNT']):1;
                    $M_PO_NO = $PO_NO;
                    $M_NO_NEW = createMATERIALCBS_NO($M_PO_NO);

                    while ($i < $M_COUNT ) {
                        
                        $M_PO_SO_LINE     = !empty($data_material_cbs[$i]['M_PO_SO_LINE'])?addslashes($data_material_cbs[$i]['M_PO_SO_LINE']):'';
                        $M_ID             = $i + 1;
                        
                        $M_PO_FORM_TYPE   = !empty($FORM_TYPE)?addslashes($FORM_TYPE):'';
                        $M_MATERIAL_CODE  = !empty($data_material_cbs[$i]['M_MATERIAL_CODE'])?addslashes($data_material_cbs[$i]['M_MATERIAL_CODE']):'';
                        $M_MATERIAL_QTY   = !empty($data_material_cbs[$i]['M_MATERIAL_QTY'])?addslashes($data_material_cbs[$i]['M_MATERIAL_QTY']):0;
                        $M_MATERIAL_DES   = !empty($data_material_cbs[$i]['M_MATERIAL_DES'])?addslashes($data_material_cbs[$i]['M_MATERIAL_DES']):'';
                        $M_PO_CREATED_BY  = !empty($PO_CREATED_BY)?addslashes($PO_CREATED_BY):'';

                        $query_m = "INSERT INTO  rfid_po_material_cbs_save
                                            (`M_NO`,`M_ID`, `M_PO_NO`, `M_PO_SO_LINE`, `M_PO_FORM_TYPE`, `M_MATERIAL_CODE`, `M_MATERIAL_DES`,`M_MATERIAL_QTY`,`M_MATERIAL_REMARK`,`M_PO_CREATED_BY` )
                                        VALUES ('$M_NO_NEW','$M_ID','$M_PO_NO','$M_PO_SO_LINE','$M_PO_FORM_TYPE','$M_MATERIAL_CODE','$M_MATERIAL_DES','$M_MATERIAL_QTY','','$M_PO_CREATED_BY' ) ";
                        $result_m = mysqli_query($conn, $query_m);
                        if (!$result_m) {
                            $response = array (
                                'status'  => 0,
                                'message' => "[ERROR 06.02]. Save Material CBS không thành công"
                            ); 
                        } else  $check_m = 1;

                        $i++;
                    }//end while

                }//end if material


            }

        
        //******** results finish *************************************************** */
            $response = array (
                'status'  => 1,
                'message' => "Save NO# $PO_NO thành công. Bạn có muốn in?",
                'PO_NO'    => $PO_NO
            );
            echo json_encode($response); exit();

        
    }//if empty data 