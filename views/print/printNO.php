<?php
    //check login
    if(!isset($_COOKIE["VNRISIntranet"])) { header('Location: login.php'); }
	header('Content-Type: text/html; charset=utf-8');
    //get data
    $PRINT_PO_NO = $_GET['PRINT_PO_NO'];

    require_once ( "../../define_constant_system.php");
    require_once (PATH_MODEL . "/__connection.php");
    require_once (PATH_MODEL . "/automail_conn.php");

?>
<script type="text/javascript">
    window.onload = function() {
        window.print();
		setTimeout(function () { window.close(); }, 100);
	}
 </script>
<body >
<?php
    //get data from model
    require_once (PATH_MODEL . "/printNO_conn.php");//load data print

?>
