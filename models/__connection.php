<?php
//check login
if (!isset($_COOKIE["VNRISIntranet"])) {
  header('Location: login.php');
}

//1. get the database connection

function getConnection($db=null)
{
  if ($db == null ) $db = "au_avery_rfidsb"; // mặc định
  $host = "147.121.56.227";
  $username = "planning";
  $password = "PELS&Auto@{2020}";
  $conn = mysqli_connect($host, $username, $password, $db) or die('Không thể kết nối tới Server ' . $host);
  $conn->query("SET NAMES 'utf8'");

  return $conn;
}

function getConnection138($db=null)
{
  if ($db == null ) $db = "au_avery"; // mặc định
  $host = "147.121.56.227";
  $username = "planning";
  $password = "PELS&Auto@{2020}";
  $conn = mysqli_connect($host, $username, $password, $db) or die('Không thể kết nối tới Server ' . $host);
  $conn->query("SET NAMES 'utf8'");

  return $conn;
}


function toQuery($conn, $query)
{
  $result = mysqli_query($conn, $query);
  $result = mysqli_fetch_assoc($result);
  mysqli_close($conn);
  return $result;
}

function toQueryArr($conn, $query)
{
  $result = mysqli_query($conn, $query);
  if (!$result ) return array();
  $result = mysqli_fetch_array($result);  
  // if ($conn) mysqli_close($conn);
  return $result;
  
}


function toQueryAll($conn, $query)
{
  $result = mysqli_query($conn, $query);
  if (!$result ) return array();
  $result = mysqli_fetch_all($result, MYSQLI_ASSOC);  

  mysqli_close($conn);
  return $result;
  
}

function toQueryAssoc($conn, $query)
{
  $result = mysqli_query($conn, $query);
  if (!$result ) return array();
  $result = mysqli_fetch_assoc($result);  

  mysqli_close($conn);
  return $result;
  
}
