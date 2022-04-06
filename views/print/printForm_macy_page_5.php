<?php
    //include_once ("../../define_constant_system.php");
    
    
    //$PO_INTERNAL_ITEM = '4-221838-310-01'; //39941291-1
    //1: Load image to array
    $url_img = '../../assets/media/nike_layout/';
    if ($handle = opendir('../../assets/media/nike_layout')) {

        /* This is the correct way to loop over the directory. */
        $image_layout_nike_arr = array();
        while (false !== ($entry = readdir($handle))) {
          if (strpos($entry,'4')!==false) {
            //load file vao array
            array_push($image_layout_nike_arr,$entry);
          } 
        }

        closedir($handle);
    }

    //show image 
    foreach ($image_layout_nike_arr as $key => $value) {
      
      // if (strpos(strtolower($value),'nike-worldon' ) !== false ) {
      //   if (strpos($value,$PO_INTERNAL_ITEM)!==false ) {
      //     echo '<p style="page-break-after:always;">&nbsp;</p>';
      //     echo "<br />\n";
      //     echo '&nbsp;&nbsp; <img width="980px" height="650px" src="' . $url_img . $value . ' "/>';  
      //     break;
      //   }
      // } else {
      //   if (strpos($value,$PO_INTERNAL_ITEM)!==false ) {
      //     echo '<p style="page-break-after:always;">&nbsp;</p>';
      //     echo "<br />\n";
      //     echo '&nbsp;&nbsp; <img width="680px" height="980px" src="' . $url_img . $value . ' "/>';
      //     break;
      //   }
      // }

      if (strpos($value,$PO_INTERNAL_ITEM)!==false && strpos(strtolower($value),'nike-worldon' ) !== false && strpos(strtolower($PO_REMARK_2),'nike-tinhloi' ) !== false ) {
        
          echo '<p style="page-break-after:always;">&nbsp;</p>';
          echo "<br />\n";
          
          echo '&nbsp;&nbsp; <img width="980px" height="650px" src="' . $url_img . $value . ' "/>';  
          break;
	    } else {
        
        if (strpos($value,$PO_INTERNAL_ITEM)!==false ) {
          echo '<p style="page-break-after:always;">&nbsp;</p>';
          echo "<br />\n";
          
          echo '&nbsp;&nbsp; <img width="680px" height="980px" src="' . $url_img . $value . ' "/>';
          break;
        }
        
      
      }
	  
	  
    }



?>