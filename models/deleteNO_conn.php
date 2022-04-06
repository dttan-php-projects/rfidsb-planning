<script>
    var conf = confirm("Bạn có chắc chắn muốn xóa đơn ? ");
    if (!conf) {
        this.close();
    }

</script>

<?php
    if(!isset($_COOKIE["VNRISIntranet"]))  header('Location: login.php');//check login
    date_default_timezone_set('Asia/Ho_Chi_Minh');
    // header("Content-Type: application/json");

    require_once ( "../define_constant_system.php");
    require_once (PATH_MODEL . "/__connection.php");
    
    //connect
    $conn = getConnection();

    if (!isset($_GET['data']) || empty($_GET['data']) ) {
        
        $response = [
            "status" => false,
            "message" =>  "Không lấy được NO# để xóa"
        ];
        $result = json_encode($response);

    } else {
        
        $PO_NO = trim($_GET['data']);
        
        // get form_type
        $FORM_TYPE = '';
        $query = mysqli_query($conn, "SELECT `PO_FORM_TYPE`, `PO_NO` FROM rfid_po_save WHERE `PO_NO` = '$PO_NO' ; " );
        $query_so = mysqli_query($conn, "SELECT `*` FROM rfid_po_soline_save WHERE `SO_PO_NO` = '$PO_NO' ; " );

        if (!$query || !$query_so ) {
            $response = [
                "status" => false,
                "message" =>  "Không tồn tại NO# $PO_NO trong Save "
            ];
            $result = json_encode($response);

        } else {

            $row_f = mysqli_fetch_array($query);
            $FORM_TYPE = $row_f['PO_FORM_TYPE'];

            if (empty($FORM_TYPE) ) {
                $response = [
                    "status" => false,
                    "message" =>  "Không lấy được FORM để xóa "
                ];
                $result = json_encode($response);
            } else {
                if ( $FORM_TYPE == 'rfid' || $FORM_TYPE == 'ua_no_cbs' ) {
                    // ink no cbs
                    $query_del_ink = mysqli_query($conn, "SELECT `*` FROM rfid_po_ink_no_cbs_save WHERE `INK_PO_NO` = '$PO_NO' ; " );
                    // material no cbs
                    $query_del_mn = mysqli_query($conn, "SELECT `*` FROM rfid_po_material_no_cbs_save WHERE `MN_PO_NO` = '$PO_NO' ; " );

                    if (!$query_del_ink || !$query_del_mn ) {
                        $response = [
                            "status" => false,
                            "message" =>  "Không tồn tại NO# để xóa ( no cbs) "
                        ];
                        $result = json_encode($response);
                    }

                } else if ($FORM_TYPE == 'trim' || $FORM_TYPE == 'trim_macy' || $FORM_TYPE == 'pvh_rfid' ) {
                    
                    // trim/macy/pvh
                    $query_del_mi = mysqli_query($conn, "SELECT `*` FROM rfid_po_material_ink_save WHERE `MI_PO_NO` = '$PO_NO' ; " );

                    if (!$query_del_mi ) {
                        $response = [
                            "status" => false,
                            "message" =>  "Không tồn tại NO# để xóa (trim/macy/pvh) "
                        ];
                        $result = json_encode($response);
                    }

                } else if ($FORM_TYPE == 'ua_cbs' || $FORM_TYPE == 'cbs' ) {

                    // ua_cbs, cbs
                    $query_del_s = mysqli_query($conn, "SELECT `*` FROM rfid_po_size_cbs_save WHERE `S_PO_NO` = '$PO_NO' ; " );
                    // material cbs
                    $query_del_m = mysqli_query($conn, "SELECT `*` FROM rfid_po_material_cbs_save WHERE `M_PO_NO` = '$PO_NO' ; " );

                    if (!$query_del_s || !$query_del_m ) {
                        $response = [
                            "status" => false,
                            "message" =>  "Không tồn tại NO# để xóa (ua/cbs) "
                        ];
                        $result = json_encode($response);
                    }
                }

                
            }

            // delete 
            $result_del_po = mysqli_query($conn, "DELETE FROM rfid_po_save WHERE `PO_NO` = '$PO_NO' ; " );
            $result_del_so = mysqli_query($conn, "DELETE FROM rfid_po_soline_save WHERE `SO_PO_NO` = '$PO_NO' ; " );

            if (!$result_del_po || !$result_del_so ) {
                $response = [
                    "status" => false,
                    "message" =>  "Có lỗi trong quá trình xóa (0) "
                ];
                $result = json_encode($response);

            } else {

                if ( $FORM_TYPE == 'rfid' || $FORM_TYPE == 'ua_no_cbs' ) {
                    // query
                    $result_del_1 = mysqli_query($conn, "DELETE FROM rfid_po_ink_no_cbs_save WHERE `INK_PO_NO` = '$PO_NO' ; " );
                    $result_del_2 = mysqli_query($conn, "DELETE FROM rfid_po_material_no_cbs_save WHERE `MN_PO_NO` = '$PO_NO' ; " );
                    // check 
                    if (!$result_del_1 || !$result_del_2 ) {
                        $response = [
                            "status" => false,
                            "message" =>  "Có lỗi trong quá trình xóa (no cbs) "
                        ];
                        $result = json_encode($response);
                    }
    
                } else if ($FORM_TYPE == 'trim' || $FORM_TYPE == 'trim_macy' || $FORM_TYPE == 'pvh_rfid' ) {
                    // query
                    $result_del_1 = mysqli_query($conn, "DELETE FROM rfid_po_material_ink_save WHERE `MI_PO_NO` = '$PO_NO' ; " );

                    // check 
                    if (!$result_del_1 ) {
                        $response = [
                            "status" => false,
                            "message" =>  "Có lỗi trong quá trình xóa (trim/macy/pvh) "
                        ];
                        $result = json_encode($response);
                    }
    
                } else if ($FORM_TYPE == 'ua_cbs' || $FORM_TYPE == 'cbs' ) {
                    // query
                    $result_del_1 = mysqli_query($conn, "DELETE FROM rfid_po_size_cbs_save WHERE `S_PO_NO` = '$PO_NO' ; " );
                    $result_del_2 = mysqli_query($conn, "DELETE FROM rfid_po_material_cbs_save WHERE `M_PO_NO` = '$PO_NO' ; " );

                    // check 
                    if (!$result_del_1 || !$result_del_2 ) {
                        $response = [
                            "status" => false,
                            "message" =>  "Có lỗi trong quá trình xóa (ua/cbs) "
                        ];
                        $result = json_encode($response);
                    }

                }

            }
 
        }

        $response = [
            "status" => true,
            "message" => "Xóa NO# $PO_NO thành công"  
        ];
        $results = json_encode($response);
        
    }

?>

<script>

    var results = '<?php print_r($results); ?>';

	// parse results
	results = JSON.parse(results);

	// check false
	if (results.status == false ) {
		alert(results.message);
        window.location ='../';
	} else {
		alert(results.message); 
		window.location ='../';
	}


</script>
    