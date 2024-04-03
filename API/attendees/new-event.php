<?php

// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include_once '../../config.php/Database.php';
include_once '../../models/Post.php';

// תוסף לעבודה עם קבצי אקסל
require "vendor/autoload.php";

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


$database = new Database();
$db = $database->connect();

$attendees = new Post($db);

if (isset($_POST['eventTable'])) {
  $filName = $_FILES['import_file']['name'];
  $fileExt = pathinfo($filName, PATHINFO_EXTENSION);
  $fileNamePure = pathinfo($filName, PATHINFO_FILENAME);
  $allowedExtentions = ['xls', 'csv', 'xlsx'];

  if (in_array($fileExt, $allowedExtentions)) {
    $inputFileNamePath = $_FILES['import_file']['tmp_name'];
    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($inputFileNamePath);
    $data = $spreadsheet->getActiveSheet()->toArray();
    $createTable = "CREATE TABLE IF NOT EXISTS $fileNamePure (
      id int AUTO_INCREMENT PRIMARY KEY,
      tz_id varchar(255),
      fName varchar(255),
      lName varchar(255),
      institute varchar(255),
      isArrived int,
      event_id int
  )";
    $result = mysqli_query($conn, $createTable);

    foreach ($data as $row) {
      $tz_id = $row['0'];
      $fName = $row['1'];
      $lName = $row['2'];
      $institute = $row['3'];
      $isArrived = $row['4'];
      $event_id = $row['5'];

      $insertAttendeeQuery = "INSERT INTO $fileNamePure (tz_id, fName, lName, institute, isArrived, event_id) VALUES('$tz_id', '$fName', '$lName', '$institute', $isArrived, $event_id)";
      $result = mysqli_query($conn, $insertAttendeeQuery);
      $msg = true;
    }

    if (isset($msg)) {
      $_SESSION['message'] = 'File Upload Successfully!';
      header("Location: index.php");
      exit(0);
    } else {
      $_SESSION['message'] = 'File not imported';
      header("Location: index.php");
      exit(0);
    }
  } else {
    $_SESSION['message'] = 'File not support';
    header("Location: index.php");
    exit(0);
  }
}

// $result = $attendees->read();

// Torn it into Json
echo json_encode(["status" => "ok", "data" => $_POST, "files" => $_FILES['eventTable']]);
// echo json_encode(
//   [
//     "message" => 'No posts found'
//   ]
// );
