<?php
require_once (PATH_MODEL . "/checkSOExist_conn.php");

//@detached start ********************************
function detachedSOLINE($SO_LINE)
{
    $check_soline = explode('-',$SO_LINE);
    $count_check = count($check_soline);

    //Chuyển đổi thành kiểu int để loại bỏ các phần tử không phải số. 
    //Rồi chuyển lại kiểu string và đếm phần tử
    $val_SO = $check_soline[0];
    $val_SO_test = (int) $check_soline[0];
    $len_SO = strlen( (string)$val_SO_test) ;

    

    if ( $count_check == 2 ) {
        $val_LINE = $check_soline[1];
        $val_LINE_test = (int) $check_soline[1];
        $len_LINE = strlen((string)$val_LINE_test);

        if ($len_LINE > 0) {
            $detached_SOLINE = $val_SO . "-" . $val_LINE;
            $gpm_SOLINE = $val_SO . "." . $val_LINE;//để check GPM: DK so sánh SOL trong tbl_gos_file table
        } 
        else {
            $detached_SOLINE = $val_SO;
            $gpm_SOLINE = $val_SO.'.1';
          }

        $data = array (
            "val_SO"        => $val_SO,
            "len_SO"        => $len_SO,
            "val_LINE"      => $val_LINE,
            "len_LINE"      => $len_LINE,
            "count_check"   => $count_check,
            "detached_SOLINE" => $detached_SOLINE,
            "gpm_SOLINE"    => $gpm_SOLINE
        );
        
    } 
    else {//count_check = 1

        $len_LINE = 0;
        $detached_SOLINE = $val_SO;
        $gpm_SOLINE = $val_SO.'.1';

        $data = array (
            "val_SO"        => $val_SO,
            "len_SO"        => $len_SO,
            "val_LINE"      => "",
            "len_LINE"      => 0,
            "count_check"   => $count_check,
            "detached_SOLINE" => $detached_SOLINE,
            "gpm_SOLINE"    => $gpm_SOLINE
        );
    }    

    return $data;
    

}//end detachedSOLINE()

//@detached end *********************************//