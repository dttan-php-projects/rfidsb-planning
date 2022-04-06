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
		$table = "setting_item_form";

	//set content type and xml tag
		header("Content-type:text/xml");
		echo "<?xml version=\"1.0\"?>";

	// query model
		$fields = " INTERNAL_ITEM,FORM_TYPE, CREATED_BY, CREATED_TIME ";
		$query = " SELECT $fields FROM $table ORDER BY CREATED_TIME DESC ";
	//execute
	$rowsResult = toQueryAll($conn252, $query);
	if (!empty($rowsResult) ) {
		echo("<rows>");
			$ID = 0;
			$cellStart = "<cell><![CDATA[";
			$cellEnd = "]]></cell>";
			foreach ($rowsResult as $row){
				$ID++;
				$INTERNAL_ITEM 		    = $row['INTERNAL_ITEM']; 
				$FORM_TYPE 			    = $row['FORM_TYPE'];
                $CREATED_BY 			= $row['CREATED_BY'];
				$CREATED_TIME 			= $row['CREATED_TIME'];
                
				echo("<row id='".$ID."'>");
					echo( $cellStart);  // LENGTH
						echo($ID);  //0                
					echo( $cellEnd);
					echo( $cellStart);  // LENGTH
						echo($INTERNAL_ITEM);  //1                 
					echo( $cellEnd);
					echo( $cellStart);  // LENGTH
						echo($FORM_TYPE);  //2                 
					echo( $cellEnd);
					echo( $cellStart);  // LENGTH
						echo($CREATED_BY);  //3                 
					echo( $cellEnd);
					echo( $cellStart);  // LENGTH
						echo($CREATED_TIME);  //4  OK               
					echo( $cellEnd);

				echo("</row>");
			}
		echo("</rows>");
	}else{
		echo("<rows></rows>");
	}
?>