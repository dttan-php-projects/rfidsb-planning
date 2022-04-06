<?php
	date_default_timezone_set('Asia/Ho_Chi_Minh');
	ini_set('max_execution_time',300);
	header("Content-Type: application/json; charset=utf-8");

	require_once ( "../define_constant_system.php");
	require_once (PATH_MODEL . "/__connection.php");

    //connect 
    $conn = getConnection();

    $RBO = $_GET['RBO'];
    if ( strpos(strtoupper($RBO),"NIKE") !==false ) {
        $RBO_tmp = "NIKE";
    }
    else if ( strpos(strtoupper($RBO),"UNIQLO") !==false ) {
        $RBO_tmp = "UNIQLO";
    }
    else if ( strpos(strtoupper($RBO),"FAST RETAILING") !==false   ) {
        $RBO_tmp = "FASTRETAILING";
    }
    else if ( strpos(strtoupper($RBO),"MUJI") !==false   ) {
        $RBO_tmp = "MUJIRFID";
    }
    else {
        $RBO_tmp = "OTHER";
    }
    //echo "RBO la ". $RBO_tmp;

    $query_scrap = "SELECT RBO, SCRAP FROM rfidsb_scrap WHERE RBO = '$RBO_tmp' ";
    $result_scrap = mysqli_query($conn, $query_scrap);
    if (!$result_scrap) {
        echo "Query scrap sai roi ";
    }
    else {//else result_ms_color
        $num_ms_color = mysqli_num_rows($result_scrap);
        $result_scrap = mysqli_fetch_array($result_scrap);
        if ( $num_ms_color > 0 ) {
                $scrap_percent = $result_scrap['SCRAP'];
        }
        else 	$scrap_percent = 1.014;//các loại khac
    }
    if ($scrap_percent > 0) {
        $data_scrap = array (
            'status' => true,
            'scrap_percent' => $scrap_percent
        );
    } else {
        $data_scrap = array (
            'status' => false,
            'scrap_percent' => $scrap_percent
        );
    }
   

    echo json_encode( $data_scrap );
    