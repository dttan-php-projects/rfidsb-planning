<?php
date_default_timezone_set('Asia/Ho_Chi_Minh');
ini_set('max_execution_time', 300);
header("Content-Type: application/json; charset=utf-8");

require_once("../define_constant_system.php");
require_once(PATH_MODEL . "/__connection.php");

require_once(PATH_MODEL . "/User_rfid_conn.php");
require_once(PATH_MODEL . "/automail_conn.php");

require_once(PATH_DATA . "/detachedSOLINE.php");

$SO_LINE = $_GET['SO_LINE']; //GET SOLINE 

//@INCORRECT: START ********** ERROR 02 ********** START
if (strlen($SO_LINE) < 8 || strlen($SO_LINE) > 12) {

  $data = array(
    'status'        => 0,
    'message'       => "SOLINE  $SO_LINE KHÔNG ĐÚNG",
    'error'         => "[ERROR 02.01]",
    'checkSOExist'  => 0
  );
  echo json_encode($data);
  die;
} //else 1
else {
  //detached SOLINE
  $detached = detachedSOLINE($SO_LINE);
  extract($detached);

  if ($count_check > 0 && $count_check < 3) {

    $result = checkSOLINE($SO_LINE); //file automail_conn.php
    extract($result);

    if ($status_vnso == false) {

      $data = array(
        'status'       => 0,
        'message'       => "SOLINE: $SO_LINE KHÔNG TỒN TẠI",
        'error'         => "[ERROR 03.01]",
        'checkSOExist'  => 0
      );
      echo json_encode($data);
      die;
    } else { //@TRUE

      $checkSOExist = isAlreadyExist($SO_LINE); //file checkSOExist.php
      if ($checkSOExist == 0) {

        $data = array(
          'status'          => 1,
          'message'         => "SOLINE: $SO_LINE",
          'error'           => "[OK1]",
          'checkSOExist'    => $checkSOExist,
          'result_vnso' => $result_vnso
        );
        echo json_encode($data);
        die;
      } else { //else SOLINE is exist @TRUE.

        $data = array(
          'status'          => 1,
          'message'         => "SOLINE: $SO_LINE ",
          'error'           => "[OK2]",
          'checkSOExist'    => $checkSOExist,
          'result_vnso' => $result_vnso
        );
        echo json_encode($data);
        die;
      } //end else SOLINE is exist

    } //end TRUE

  } else {
    $data = array(
      'status'        => 0,
      'message'       => "SOLINE: $SO_LINE KHÔNG ĐÚNG",
      'error'         => "[ERROR 02.02]",
      'checkSOExist'  => 0
    );
    echo json_encode($data);
    die;
  }
}
