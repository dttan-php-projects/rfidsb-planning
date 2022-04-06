<?php
	//check login
	if(!isset($_COOKIE["VNRISIntranet"])) { header('Location: login.php');//check login
	} else { $USER_CUR = $_COOKIE["VNRISIntranet"];  }

	date_default_timezone_set('Asia/Ho_Chi_Minh');
	
	function formatDate($value){ return date('d-M-y',strtotime($value)); } 
	
	require_once ( "../define_constant_system.php");
	require_once (PATH_MODEL . "/__connection.php");  
	
	//connect
		$conn252 = getConnection();
		$table = "rfidsb_scrap";

	//set content type and xml tag
		header("Content-type:text/xml");
		echo "<?xml version=\"1.0\"?>";

	// query model
		$fields = " RBO,SCRAP,CREATED_BY,CREATED_DATE ";
		$query = " SELECT $fields FROM $table ORDER BY RBO ASC ";
	//execute
	$rowsResult = toQueryAll($conn252, $query);
	if (!empty($rowsResult) ) {
		$header =  '<head>
						<column width="40" type="ed" align="center" sort="str">TT</column>
						<column width="*" type="ed" align="center" sort="str">RBO</column>
						<column width="100" type="ed" align="center" sort="str">SCRAP</column>
						<column width="140" type="ed" align="center" sort="str">CREATED BY</column>
						<column width="140" type="ed" align="center" sort="str">CREATED DATE</column>
				</head>';

		echo("<rows>");
			
			echo $header;
			
			if(!empty($rowsResult)){  
				$ID = 0;
				$cellStart = "<cell><![CDATA[";
				$cellEnd = "]]></cell>";
				foreach ($rowsResult as $row){
					if ($row['RBO'] == "TRIM_MACY") {
						continue;
					}
					$ID++;
					$RBO 		    = $row['RBO']; 
					$SCRAP 			= $row['SCRAP'];
					$CREATED_BY 	= $row['CREATED_BY'];
					$CREATED_DATE 	= $row['CREATED_DATE'];
					
					echo("<row id='".$ID."'>");
						echo( $cellStart);  // LENGTH
							echo($ID);  //0                
						echo( $cellEnd);
						echo( $cellStart);  // LENGTH
							echo($row['RBO']);  //1                 
						echo( $cellEnd);
						echo( $cellStart);  // LENGTH
							echo($row['SCRAP']);  //2                 
						echo( $cellEnd);
						echo( $cellStart);  // LENGTH
							echo($row['CREATED_BY']);  //2                 
						echo( $cellEnd);
						echo( $cellStart);  // LENGTH
							echo($row['CREATED_DATE']);  //2                 
						echo( $cellEnd);
					echo("</row>");
				}
			}
		echo("</rows>");
	}else{
		echo("<rows></rows>");
	}
?>