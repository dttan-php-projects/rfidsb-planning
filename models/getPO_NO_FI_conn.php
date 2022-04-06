<?php
    // require_once ( "../define_constant_system.php");
    // require_once (PATH_MODEL . "/__connection.php");

    if(!isset($_COOKIE["VNRISIntranet"])) {
        header('Location: login.php');
    } else {
        //get CREATED_BY
        $PO_CREATED_BY = $_COOKIE["VNRISIntranet"];
    }

    function getPO_NO_FI($PO_NO) {

        //connect host
        $conn = getConnection();
        $table = "rfid_po_save";
        
        //init var
        $FR_SHOW='';
        $PO_NO_FI=$PO_NO;

        //query 
        $query = "SELECT PO_NO,PO_ORDER_TYPE_NAME,PO_RBO,PO_FORM_TYPE FROM $table WHERE PO_NO = '$PO_NO' LIMIT 1 ";

        
        $result = mysqli_query($conn, $query);
        if($result===false) { die(mysql_error()); }

        if (mysqli_num_rows($result)>0){
            
            //get data
            $row = mysqli_fetch_array($result, MYSQLI_ASSOC );
            
            //set data
            $PO_ORDER_TYPE_NAME = !empty($row["PO_ORDER_TYPE_NAME"]) ? trim(strtoupper($row["PO_ORDER_TYPE_NAME"])) : "";
            $PO_RBO             = !empty($row["PO_RBO"]) ? trim(strtoupper($row["PO_RBO"])) : "";
            $PO_RBO = html_entity_decode($PO_RBO);
            $PO_FORM_TYPE       = !empty($row["PO_FORM_TYPE"]) ? trim($row["PO_FORM_TYPE"]) : "";

            if (strpos($PO_RBO, '&AMP;') !==false ) {
                $PO_RBO = str_replace('&AMP;', '&', $PO_RBO );
            }

            /* ******** Check trường hợp FR  */
            //Option 1: RENNER
            if (strpos($PO_RBO,'RENNER') !==false ) {
                $FR_SHOW = '-FR';
            }

            //array RBO FR
            $RBO_FR_ARR = [ 'H&M', 'FAST RETAILING', 'UNIQLO' ];
    
            //Option 2. if RBO = H&M and ORDER_TYPE_NAME = VN QR
            if ( strpos($PO_ORDER_TYPE_NAME,'VN QR') !==false ) {
            
                foreach ($RBO_FR_ARR as $key => $RBO_FR) {
                    
                    if (strpos(strtoupper($PO_RBO), strtoupper($RBO_FR) ) !== false ) {
                        
                        $FR_SHOW = '-FR';
                        break;
                    }
                }
                
            }


            //set PO_NO_FI
            $PO_NO_FI .= $FR_SHOW;

            /* ********** Check trường hợp JC PENNEY. Hien thi don JC PENNEY phia sau PO NO  */
            if ( $PO_FORM_TYPE == 'trim' && strpos($PO_RBO,'JC PENNEY') !==false ) {
                $PO_NO_FI .= "-JC PENNEY COMBO"; //Nối chuỗi
            }

            

        }
        
        return $PO_NO_FI;
        
    }//end function

