<?php
    require_once ( "../define_constant_system.php");
    require_once (PATH_MODEL . "/__connection.php");

    if(!isset($_COOKIE["VNRISIntranet"])) { header('Location: login.php');
    } else { $PO_CREATED_BY = $_COOKIE["VNRISIntranet"]; }
    
    //connect host
        $conn = getConnection();
        $table = "rfid_po_save";

    //get data 
        $PO_NO = $_GET['PO_NO'];
        //$PO_NO = "RF1909-00005";
    // query 
        $query_po = "SELECT PO_NO,PO_FORM_TYPE FROM $table WHERE PO_NO = '$PO_NO' ";
        $result_po = mysqli_query($conn, $query_po);
        if (!$query_po) {
            $data = array (
                'status'  => 0,
                'message' => "Query sai (FORM)"
            );
            echo json_encode($data); exit();
        }
        $num_po = mysqli_num_rows($result_po);
        if ($num_po > 0) {
            $row_po = mysqli_fetch_array($result_po);
            $PO_NO = $row_po['PO_NO'];
            $data = array (
                'status'  => 1,
                'message' => "OK. Tiếp tục in",
                'PO_NO'   => $PO_NO
            );
        } else {
            $data = array (
                'status'  => 0,
                'message' => "Form chưa SAVE. SAVE để tiếp tục PRINT"
            );
        }

        // if ($conn) mysqli_close($conn);
        
        echo json_encode($data);exit();


