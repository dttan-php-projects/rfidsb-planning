<?php

function formatDate($value,$format='d-M-Y',$remove=1){
    $display = '';
    if(!empty($value)){
        $dateFormat = explode(" ",$value);
        if(!empty($dateFormat[0])){
            $dateArray = explode("/",$dateFormat[0]);
            $date = $dateArray[1];
            $month = $dateArray[0];
            $year = $dateArray[2];            
            if(strlen($date)===1){
                $date="0".$date;
            }
            if(strlen($month)===1){
                $month="0".$month;
            }
            $day = $date."-".$month."-".$year;
            $dayTime = strtotime($date."-".$month."-".$year);

            /* 
                MẶC ĐỊNH THỜI GIAN TRỪ 1 NGÀY
                CHỈ THAY ĐỔI GIÁ TRỊ NGAY CHỔ NÀY
            */ 
            $subDate = 1;

            if($format==='dd-mm-YYYY'){
                if($remove){
                    // -2 if monday else -1                
                    if(date('w',$dayTime)==1) $subDate +=1;
                    $display = date('d-m-Y', strtotime("-$subDate day", $dayTime));

                }else{
                    $display = date('d-m-Y', $dayTime);
                }
                
            }elseif($format==='dd.mm.YYYY'){
                // -2 if monday else -1     
                if($remove){
                    if(date('w',$dayTime)==1) $subDate +=1;
                    $display = date('d.m.Y', strtotime("-$subDate day", $dayTime));

                }else{
                    $display = date('d.m.Y', $dayTime);
                }          
                
            }else{
                // 3-Nov-18	
                if($remove){
                    if(date('w',$dayTime)==1) $subDate +=1;
                    $display = date('d-M-y', strtotime("-$subDate day", $dayTime));
                    
                }else{
                    $display = date('d-M-y', $dayTime);
                }                
            }
            return $display;
        }
    }
    return "";    
}