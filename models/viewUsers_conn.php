<?php
	//check login
	if(!isset($_COOKIE["VNRISIntranet"])) { header('Location: login.php'); } else { $USER_CUR = $_COOKIE["VNRISIntranet"]; }
	
	date_default_timezone_set('Asia/Ho_Chi_Minh');
	
	function formatDate($value){ return date('d-M-y',strtotime($value)); } 
	
	require_once ( "../define_constant_system.php");
	require_once (PATH_MODEL . "/__connection.php");  
	
	//connect
		$conn252 = getConnection();
		$table = "user_rfid";

	//set content type and xml tag
		header("Content-type:text/xml");
		echo "<?xml version=\"1.0\"?>";


	// query
		$sql = "SELECT * FROM $table WHERE `email` = '$USER_CUR' ; ";
		$user_item = toQueryArr($conn252, $sql);
		if (!empty($user_item) ) {

			$is_access = $user_item['is_admin'];
			if ($is_access == 1 || $is_access == 9 ) {
				$sqlCheck = "SELECT `*` FROM $table  ORDER BY is_admin desc, id asc ; ";
			} else {
				$sqlCheck = "SELECT `*` FROM $table WHERE `email` = '$USER_CUR' ; ";
			}

			$rowsResult = toQueryAll($conn252, $sqlCheck);
			if( !empty($rowsResult) ){ 
				$header = '<head>
								<column width="45" type="ed" align="center" sort="str">Stt</column>
								<column width="140" type="ed" align="center" sort="str">Username</column>
								<column width="120" type="ed" align="center" sort="str">User Type</column>
								<column width="80" type="link" align="center" sort="str">Edit</column>
								<column width="80" type="link" align="center" sort="str">Delete</column>
							</head>';
				
				echo("<rows>");
				
					echo $header;

					$index = 0;
					$cellStart = "<cell><![CDATA[";
					$cellEnd = "]]></cell>";
					foreach ($rowsResult as $key => $row){

						$username = $row['email'];
						$is_admin = $row['is_admin'];
						if ($is_admin == 0 ) {
							$user_type = 'Standard';
						} else if ($is_admin == 1 ) {
							$user_type = 'Admin';
						} else if ($is_admin == 9 ) {
							// Nếu = 9 thì ẩn đi
							continue;
						}
						
						if($is_access == 1 || $is_access == 9 ){
							$edit_url = "./views/user/update.php?username=$username";
							$delete_url = "./models/deleteUser_conn.php?username=$username";
						} else {
							$edit_url = "./views/user/update.php?username=$username";
							$delete_url = '';
						}
						
						$index++;
							
						echo("<row id='".$key."'>");
							echo( $cellStart);
								echo($index);  //value for product name                 
							echo( $cellEnd);

							echo( $cellStart);
								echo($username);  //value for product name                 
							echo( $cellEnd);

							echo( $cellStart);
								echo($user_type);  //value for product name                 
							echo( $cellEnd);

							echo( $cellStart);
								echo("Edit^$edit_url");  //value for product name                 
							echo( $cellEnd);

							echo( $cellStart);
								echo("Delete^$delete_url");  //value for product name          
							echo( $cellEnd);
						
						echo("</row>");
					}


				echo("</rows>");
			}else{
				echo("<rows></rows>");
			}
		}
?>