<?php
echo "<br />\n";

$url_img = '../../assets/media/images/checklist/';
if ($handle = opendir('../../assets/media/images/checklist')) {

    /* This is the correct way to loop over the directory. */
    $image_check_list_inkjet = '';
    $image_check_list_inkjet_horizontal = '';
    $image_check_list_common = '';
    $image_check_list_common_horizontal = '';
    while (false !== ($entry = readdir($handle))) {
        if (strpos(strtolower($entry ),'inkjet' ) !== false ) {
            if (strpos(strtolower($entry ),'horizontal' ) !== false ) { // form ngang
                $image_check_list_inkjet_horizontal = $entry;
            } else {
                $image_check_list_inkjet = $entry;
            }
            
        } else if (strpos(strtolower($entry ),'common' ) !== false ) {
            if (strpos(strtolower($entry ),'horizontal' ) !== false ) { // form ngang
                $image_check_list_common_horizontal = $entry;
            } else {
                $image_check_list_common = $entry;
            }
        }
    }

    closedir($handle);
}

// break page
echo '<p style="page-break-after:always;">&nbsp;</p>';
$img_show = '';
//show image 
if ($check_list == true) {
    // check form
    if ($PO_FORM_TYPE == 'trim' || $PO_FORM_TYPE == 'trim_macy' || $PO_FORM_TYPE == 'pvh_rfid' ) {
        $img_show =  '&nbsp;&nbsp; <img width="980px" height="680px" src="' . $url_img . $image_check_list_inkjet_horizontal . ' "/>';
    } else {
        if ( (strpos($PO_REMARK_2, 'NIKE-WORLDON') !== false) || (strpos($PO_REMARK_2, 'NIKE-TINHLOI') !== false) ) {
            $img_show =  '&nbsp;&nbsp; <img width="980px" height="680px" src="' . $url_img . $image_check_list_inkjet_horizontal . ' "/>';
        } else {
            $img_show =  '&nbsp;&nbsp; <img width="710px" height="980px" src="' . $url_img . $image_check_list_inkjet . ' "/>';
        }
        
    }
    
} else {
    // check form
    if ($PO_FORM_TYPE == 'trim' || $PO_FORM_TYPE == 'trim_macy' || $PO_FORM_TYPE == 'pvh_rfid'  ) {
        $img_show =  '&nbsp;&nbsp; <img width="980px" height="680px" src="' . $url_img . $image_check_list_common_horizontal . ' "/>';
    } else {
        $img_show =  '&nbsp;&nbsp; <img width="710px" height="980px" src="' . $url_img . $image_check_list_common . ' "/>';
    }
}

echo $img_show;