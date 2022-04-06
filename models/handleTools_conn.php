<?php
	date_default_timezone_set('Asia/Ho_Chi_Minh');
	ini_set('max_execution_time',300);

	require_once ( "../define_constant_system.php");
	require_once (PATH_MODEL . "/__connection.php");
	$conn = getConnection();

	$event = (isset($_GET['event']) && !empty($_GET['event']) ) ? trim($_GET['event']) : 'loadRBOLT';

	$table_rbo_lt = 'rfidsb_rbo_lt';
	$updated_by = isset($_COOKIE["VNRISIntranet"]) ? $_COOKIE["VNRISIntranet"] : '';
	

	if ($event == 'loadRBOLT' ) {

		// XML header
			header('Content-type: text/xml');

        // open
            echo "<rows>";

            // header
                $header = '<head>
                    <column width="50" type="ro" align="center" sort="str">No.</column>
                    <column width="*" type="ed" align="left" sort="str">RBO</column>
                    <column width="120" type="ed" align="center" sort="str">LT</column>
                    <column width="120" type="ro" align="center" sort="str">Updated by</column>
                    <column width="120" type="ro" align="center" sort="str">Updated date</column>
                    <column width="70" type="acheck" align="center" sort="str">Save</column>
                    <column width="100" type="acheck" align="center" sort="str">Delete</column>
                </head>';

                echo $header;
            // query
				$data = array();
				
				$sql = "SELECT * FROM $table_rbo_lt ORDER BY `LT` ASC;";
				$results = mysqli_query($conn, $sql);
				if (mysqli_num_rows($results) > 0) {
					$data = mysqli_fetch_all($results, MYSQLI_ASSOC);
				} 
                

            // content
                if (empty($data) ) {
                    $index = 0;
                    for ($i=0; $i<5; $i++ ) {
                        $index++;
                        echo '<row id="'. $i .'">';
                            echo '<cell>'. $index .'</cell>';
                            echo '<cell></cell>';
                            echo '<cell></cell>';
                            echo '<cell></cell>';
                            echo '<cell></cell>';
                            echo '<cell></cell>';
                            echo '<cell></cell>';
                        echo '</row>';
                    }
                    
                } else {

                    // set data
                        $index = 0;
                        
                        foreach ($data as $key => $item ) {

                            $index++;

							$RBO =  str_replace("&", "&amp;", $item['RBO']);
                            
                            echo '<row id="'. $key .'">';
                                echo '<cell>'. $index .'</cell>';
                                echo '<cell>'. $RBO .'</cell>';
                                echo '<cell>'. $item['LT'].'</cell>';
                                echo '<cell>'. $item['UPDATED_BY'].'</cell>';
                                echo '<cell>'. $item['UPDATED_DATE'].'</cell>';
                                echo '<cell></cell>';
                                echo '<cell></cell>';
                            echo '</row>';
                            
                        }


                    // add 5 empty rows
                        $last = $index + 5;
                        for ($i=($key+1); $i<$last; $i++ ) {
                            $index++;
                            echo '<row id="'. $i .'">';
								echo '<cell>'. $index .'</cell>';
								echo '<cell></cell>';
								echo '<cell></cell>';
								echo '<cell></cell>';
								echo '<cell></cell>';
								echo '<cell></cell>';
								echo '<cell></cell>';
                            echo '</row>';
                        }

                }

                

        // close
            echo "</rows>";

	} else if ($event == 'saveRBOLT' ) {

		header("Content-Type: application/json; charset=utf-8");

		// init 
		$message = 'Cập nhật chưa hoàn thành';
		$status = false;

		if (isset($_POST['data']) && !empty($_POST['data']) ) {

			$data = json_decode($_POST['data']);
			$RBO = isset($data->rbo) ? str_replace("&amp;", "&",$data->rbo) : '';
			$RBO = addslashes(trim($RBO) );
			$LT = isset($data->lt) ? trim($data->lt) : '';

			if (empty($RBO) || empty($LT) ) {
				$message = "RBO và LT không được trống!";
			} else {

				$check = mysqli_query($conn, "SELECT * FROM $table_rbo_lt WHERE `RBO` = '$RBO';");
				if (mysqli_num_rows($check) > 0) {

					// update query
					$updated_date = date('Y-m-d H:i:s');
					$sql = "UPDATE $table_rbo_lt SET `LT` = '$LT', `updated_by` = '$updated_by', `updated_date` = '$updated_date' WHERE `RBO` = '$RBO'; ";
					
				} else {
					
					// insert query
					$sql = "INSERT INTO $table_rbo_lt ( `RBO`, `LT`, `UPDATED_BY`) VALUES ('$RBO', '$LT', '$updated_by' ); ";
				}
				
		
			}

			$result = mysqli_query($conn, $sql);
			if ($result == false ) {
				$message = 'Cập nhật không thành công. Kiểm tra lại dữ liệu nhập!';
			} else {
				$message = 'Cập nhật thành công';
				$status = true;
			}

		}

		$results = array( 'status' => $status, 'message' => $message);
		echo json_encode($results); exit();

	} else if ($event == 'delRBOLT' ) {

		header("Content-Type: application/json; charset=utf-8");

		// init 
		$message = 'Cập nhật chưa hoàn thành';
		$status = false;

		if (isset($_POST['data']) && !empty($_POST['data']) ) {

			
			$data = json_decode($_POST['data']);
			$RBO = isset($data->rbo) ? str_replace("&amp;", "&",$data->rbo) : '';
			$LT = isset($data->lt) ? trim($data->lt) : '';

			if (empty($RBO)) {
				$message = "Không nhận được dữ liệu RBO để Xóa!";
			} else {

				$check = mysqli_query($conn, "SELECT * FROM $table_rbo_lt WHERE `RBO` = '$RBO';");
				if (mysqli_num_rows($check) > 0) {
					// del query
					$result = mysqli_query($conn, "DELETE FROM $table_rbo_lt WHERE `RBO` = '$RBO';");
					if (!$result) {
						$message = "Xóa RBO: $RBO không thành công! Vui lòng liên hệ Team Auto!";
					} else {
						$message = "Xóa thành công RBO: $RBO";
						$status = true;
					}

				} else {
					$message = "Không có RBO: $RBO tồn tại trong hệ thống";
				}
				
		
			}

			

		}

		$results = array( 'status' => $status, 'message' => $message);
		echo json_encode($results, JSON_UNESCAPED_UNICODE);

	}



	// close db
	if ($conn ) mysqli_close($conn);