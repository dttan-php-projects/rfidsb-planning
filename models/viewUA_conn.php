<?php   

$script = basename($_SERVER['PHP_SELF']);
$urlRoot = str_replace($script,'',$_SERVER['PHP_SELF']);
$urlRoot = str_replace('data/','',$urlRoot);
header("Content-type:text/xml");//set content type and xml tag
echo "<?xml version=\"1.0\"?>";

	require_once ( "../define_constant_system.php");
	require_once (PATH_MODEL . "/__connection.php");  
	//connect
	$conn252 = getConnection();
	$table = "ua";

	$fields = '*';
	$sql = "SELECT $fields FROM $table ORDER BY ID ASC";
	$rowsResult = toQueryAll($conn252, $sql);
	if (!empty($rowsResult) ) {

		$header = '<head>
						<column width="40" type="ch" align="left" sort="str"></column>
						<column width="130" type="ed" align="left" sort="str">ITEM</column>
						<column width="90" type="ed" align="left" sort="str">SIZE</column>
						<column width="130" type="ed" align="left" sort="str">BASE_ROLL</column>
						<column width="120" type="link" align="left" sort="str"></column>
					</head>';
		echo("<rows>");
	
			if(!empty($rowsResult)){ 
				$cellStart = "<cell><![CDATA[";
				$cellEnd = "]]></cell>";
				$ID=0;
				foreach ($rowsResult as $row){
					$ID++;
					$index = $row['ID'];
					$item = $row['item'];
					$size = $row['size'];
					$base_roll = $row['base_roll'];
					/*
					if($deleteNO){
						$link  = 'DELETE^javascript:deleteUA('.$ID.');^_top';
					}		
					*/			
					echo("<row id='".$ID."'>");
					echo( $cellStart);  // LENGTH
						echo(0);  //value for product name                 
					echo( $cellEnd);
					echo( $cellStart);  // LENGTH
						echo($item);  //value for product name                 
					echo( $cellEnd);
					echo( $cellStart);  // LENGTH
						echo($size);  //value for product name                 
					echo( $cellEnd);
					echo( $cellStart);  // LENGTH
						echo($base_roll);  //value for product name                 
					echo( $cellEnd);
					echo( $cellStart);  // LENGTH
						echo($index);  //value for product name                 
					echo( $cellEnd);
					/*
					if($deleteNO){
						echo( $cellStart);  // LENGTH
						echo $link;  //value for product name                 
						echo( $cellEnd);
					}			
					*/
					echo("</row>");
				}


				// add 10 
				for($i=1;$i<=10;$i++){
					$ID = 'new_id_'.$i;
					$item = '';
					$size = '';
					$base_roll = '';		
					echo("<row id='".$ID."'>");
						echo( $cellStart);  // LENGTH
							echo(0);  //value for product name                 
						echo( $cellEnd);
						echo( $cellStart);  // LENGTH
							echo($item);  //value for product name                 
						echo( $cellEnd);
						echo( $cellStart);  // LENGTH
							echo($size);  //value for product name                 
						echo( $cellEnd);
						echo( $cellStart);  // LENGTH
							echo($base_roll);  //value for product name                 
						echo( $cellEnd);
						echo( $cellStart);  // LENGTH
							echo '';  //value for product name                 
						echo( $cellEnd);			
					echo("</row>");
				}
			}
		echo("</rows>");
}else{
	echo("<rows></rows>");
}
?>