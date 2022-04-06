<?php 
function getScrap($RBO, $trim_macy = null ) 
{
    //connect 
    $conn = getConnection();

    // init 
    $scrap = 1.014;
    
    // form type
    $form_type = !empty($_COOKIE["print_type_rfsb"])?$_COOKIE["print_type_rfsb"]:'';
    
    // tính cách mới
    $query = mysqli_query($conn, "SELECT `RBO`, `SCRAP` FROM `rfidsb_scrap` ;" ) ;
    if (!$query) {
        echo "Query scrap sai roi ";
    } else {
        
        if ( mysqli_num_rows($query) > 0 ) {
            $results = mysqli_fetch_all($query, MYSQLI_ASSOC);
            foreach ($results as $key => $value ) {
                if (strpos($RBO, $value['RBO']) !== false ) {
                    $scrap = $value['SCRAP'];
                    break;
                } else {
                    if  ( ($trim_macy == true) & ($value['RBO'] == 'TRIM_MACY') ) {
                        $scrap = $value['SCRAP'];
                        break;
                    }
                }
            }
            
        }
    }


    if ($conn) mysqli_close($conn);
    
    return (float)$scrap;
    
}//function