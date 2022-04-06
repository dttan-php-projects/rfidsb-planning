<?php
	//check login
	if(!isset($_COOKIE["VNRISIntranet"])) { header('Location: login.php');
	} else { $USER_CUR = $_COOKIE["VNRISIntranet"]; }
	date_default_timezone_set('Asia/Ho_Chi_Minh');
	function formatDate($value){ return date('d-M-y',strtotime($value)); } 
	require_once ( "../define_constant_system.php");
	require_once (PATH_MODEL . "/__connection.php");  
	//connect
	$conn252 = getConnection();
	$table      = "ms_color";

	//set content type and xml tag
	header("Content-type:text/xml");
	echo "<?xml version=\"1.0\"?>";

	// query model
	$fields = " internal_item,rbo,order_item,color_code,item_color,material_code,material_des,ribbon_code,ink_des,width,height,note,blank_gap,remark,form_type,CREATED_TIME,UPDATED_BY,OTHER_REMARK_1,OTHER_REMARK_2,OTHER_REMARK_3,OTHER_REMARK_4    ";
	$query = " SELECT $fields FROM $table ORDER BY CREATED_TIME DESC ";
	$rowsResult = toQueryAll($conn252, $query);
	if (!empty($rowsResult) ) {
		
		echo("<rows>");
		if(!empty($rowsResult)){  
			$ID = 0;
			$cellStart = "<cell><![CDATA[";
			$cellEnd = "]]></cell>";
			foreach ($rowsResult as $row){
				$ID++;
				$internal_item 			= $row['internal_item']; 
				$rbo 					= $row['rbo'];
                $order_item 			= $row['order_item'];
				$color_code 			= $row['color_code'];
				$item_color 			= $row['item_color'];
                $material_code 			= $row['material_code'];
				$material_des 			= $row['material_des'];
                $ribbon_code 			= $row['ribbon_code'];
                $ink_des 				= $row['ink_des'];
                $width 					= $row['width'];
                $height 				= $row['height'];
                $note 					= $row['note'];
                $blank_gap 				= $row['blank_gap'];
                $remark 				= $row['remark'];
                $form_type 			    = $row['form_type'];
                $CREATED_TIME 		    = $row['CREATED_TIME'];
                $UPDATED_BY 			= $row['UPDATED_BY'];
                $OTHER_REMARK_1 		= $row['OTHER_REMARK_1'];
				$OTHER_REMARK_2 		= $row['OTHER_REMARK_2'];
				$OTHER_REMARK_3 		= $row['OTHER_REMARK_3'];
				$OTHER_REMARK_4 		= $row['OTHER_REMARK_4'];
	
				echo("<row id='".$ID."'>");
					echo( $cellStart);  // LENGTH
						echo($ID);  //0                
					echo( $cellEnd);
					echo( $cellStart);  // LENGTH
						echo($internal_item);  //1                 
					echo( $cellEnd);
					echo( $cellStart);  // LENGTH
						echo($rbo);  //2                 
					echo( $cellEnd);
					echo( $cellStart);  // LENGTH
						echo($order_item);  //3                 
					echo( $cellEnd);
					echo( $cellStart);  // LENGTH
						echo($color_code);  //4  OK               
					echo( $cellEnd);
					echo( $cellStart);  // LENGTH
						echo($item_color);  //5   OK             
					echo( $cellEnd);
					echo( $cellStart);  // LENGTH
						echo($material_code);  //6    OK             
					echo( $cellEnd);
					echo( $cellStart);  // LENGTH
						echo($material_des);  //7             OK    
					echo( $cellEnd);
					echo( $cellStart);  // LENGTH
						echo($ribbon_code);  //8 OK     
					echo( $cellEnd);
					echo( $cellStart);  // LENGTH
						echo($ink_des);  //9            OK.      
					echo( $cellEnd);
					echo( $cellStart);  // LENGTH
						echo($width);  //10                 ok
					echo( $cellEnd);
					echo( $cellStart);  // LENGTH
						echo($height);  //11                ok 
					echo( $cellEnd);
					echo( $cellStart);  // LENGTH
						echo($note);  //12                ok
					echo( $cellEnd);
					echo( $cellStart);  // LENGTH
						echo($blank_gap);  //13                 ok
					echo( $cellEnd);
					echo( $cellStart);  // LENGTH
						echo($remark);  //14                 ok
					echo( $cellEnd);

					echo( $cellStart);  // LENGTH
						echo($OTHER_REMARK_1);  		//15                 
					echo( $cellEnd);
					echo( $cellStart);  // LENGTH
						echo($OTHER_REMARK_2);  	//16                
					echo( $cellEnd);
					echo( $cellStart);  // LENGTH
						echo($OTHER_REMARK_3);  //17            
					echo( $cellEnd);
					echo( $cellStart);  // LENGTH
						echo($OTHER_REMARK_4);  		//18
					echo( $cellEnd);

					echo( $cellStart);  // LENGTH
						echo($UPDATED_BY);  		//19          
					echo( $cellEnd);
					echo( $cellStart);  // LENGTH
						echo($CREATED_TIME);  		//20          
					echo( $cellEnd);

				echo("</row>");
			}
		}
		echo("</rows>");
	}else{
		echo("<rows></rows>");
	}
?>