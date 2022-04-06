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
	$table = "database_trim";

	//set content type and xml tag
	header("Content-type:text/xml");
	echo "<?xml version=\"1.0\"?>";

	// query model
	$fields = " INTERNAL_ITEM,MATERIAL_CODE,MATERIAL_DES,RIBBON_CODE,RIBBON_DES,CHIEU_DAI,CHIEU_NGANG,RBO,ORDER_ITEM,REMARK,REMARK_MUC,MACHINE,REMARK_GIAY, CREATED_DATE_TIME,UPDATED_BY,OTHER_REMARK_1,OTHER_REMARK_2,OTHER_REMARK_3,OTHER_REMARK_4    ";
	$query = " SELECT $fields FROM $table ORDER BY CREATED_DATE_TIME DESC ";
	$rowsResult = toQueryAll($conn252, $query);
	
	if (!empty($rowsResult) ) {
		echo("<rows>");
		if(!empty($rowsResult)){  
			$ID = 0;
			$cellStart = "<cell><![CDATA[";
			$cellEnd = "]]></cell>";
			foreach ($rowsResult as $row){
				$ID++;
				$INTERNAL_ITEM 		    = $row['INTERNAL_ITEM']; 
				$MATERIAL_CODE 			= $row['MATERIAL_CODE'];
				$MATERIAL_DES 			= $row['MATERIAL_DES'];
				$RIBBON_CODE 			= $row['RIBBON_CODE'];
				$RIBBON_DES 			= $row['RIBBON_DES'];
				$CHIEU_DAI 			    = $row['CHIEU_DAI'];
                $CHIEU_NGANG 			= $row['CHIEU_NGANG'];
                $RBO 			        = $row['RBO'];
				$ORDER_ITEM 			= $row['ORDER_ITEM'];
                $REMARK 			    = $row['REMARK'];
                $REMARK_MUC 			= $row['REMARK_MUC'];
                $MACHINE 				= $row['MACHINE'];
                $REMARK_GIAY 			= $row['REMARK_GIAY'];
                $CREATED_DATE_TIME 		= $row['CREATED_DATE_TIME'];
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
						echo($INTERNAL_ITEM);  //1                 
					echo( $cellEnd);
					echo( $cellStart);  // LENGTH
						echo($MATERIAL_CODE);  //2                 
					echo( $cellEnd);
					echo( $cellStart);  // LENGTH
						echo($MATERIAL_DES);  //3                
					echo( $cellEnd);
					echo( $cellStart);  // LENGTH
						echo($RIBBON_CODE);  //4                 
					echo( $cellEnd);
					echo( $cellStart);  // LENGTH
						echo($RIBBON_DES);  //5               
					echo( $cellEnd);
					echo( $cellStart);  // LENGTH
						echo($CHIEU_DAI);  //6  OK               
					echo( $cellEnd);
					echo( $cellStart);  // LENGTH
						echo($CHIEU_NGANG);  //7   OK             
					echo( $cellEnd);
					echo( $cellStart);  // LENGTH
						echo($RBO);  //8    OK             
					echo( $cellEnd);
					echo( $cellStart);  // LENGTH
						echo($ORDER_ITEM);  //9            OK    
					echo( $cellEnd);
					echo( $cellStart);  // LENGTH
						echo($REMARK);  //10 OK     
					echo( $cellEnd);
					echo( $cellStart);  // LENGTH
						echo($REMARK_MUC);  //11            OK.      
					echo( $cellEnd);
					echo( $cellStart);  // LENGTH
						echo($MACHINE);  //12                ok
					echo( $cellEnd);
					echo( $cellStart);  // LENGTH
						echo($REMARK_GIAY);  //13                ok 
					echo( $cellEnd);

					echo( $cellStart);  // LENGTH
						echo($OTHER_REMARK_1);  		//14                 
					echo( $cellEnd);
					echo( $cellStart);  // LENGTH
						echo($OTHER_REMARK_2);  	//15                
					echo( $cellEnd);
					echo( $cellStart);  // LENGTH
						echo($OTHER_REMARK_3);  //16        
					echo( $cellEnd);
					echo( $cellStart);  // LENGTH
						echo($OTHER_REMARK_4);  		//17
					echo( $cellEnd);

					echo( $cellStart);  // LENGTH
						echo($UPDATED_BY);  		//18         
					echo( $cellEnd);
					echo( $cellStart);  // LENGTH
						echo($CREATED_DATE_TIME);  		//19
					echo( $cellEnd);

				echo("</row>");
			}
		}
		echo("</rows>");
	}else{
		echo("<rows></rows>");
	}
?>