<?php
    date_default_timezone_set('Asia/Ho_Chi_Minh'); ini_set('max_execution_time',300);
    header("Content-Type: application/json; charset=utf-8");

    require_once ("../define_constant_system.php");
    require_once (PATH_MODEL . "/__connection.php");
    require_once (PATH_MODEL . "/getPrintType_conn.php");
    require_once (PATH_DATA . "/detachedSOLINE.php");

    $ITEM_VNSO = $_GET['ITEM_VNSO'];
    $SOLINE = $_GET['SOLINE'];

    $getPrintType = getPrintType($SOLINE,$ITEM_VNSO);
    if (!empty($getPrintType) ) {
        extract($getPrintType);
    } else {
        $getPrintType = getPrintTypeMS($ITEM_VNSO);

        extract($getPrintType);
        if ($status_item == 0) {
            $data = array (
                'status_item' => 0,
                'message'       =>  "ITEM: $ITEM_VNSO KHÔNG TÌM THẤY LOẠI FORM, VUI LÒNG KIỂM TRA LẠI!",
                'result_item'   => ""
            );
            echo json_encode($data);
        }

    }

    //Trường hợp lấy được form (không phải ms color)
    $data = array (
        'status_item'   => 1,
        'message'       =>  "OK ITEM",
        'result_item'   => $result_item
    );
    echo json_encode($data);

