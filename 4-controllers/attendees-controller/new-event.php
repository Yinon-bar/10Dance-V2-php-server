<?php

// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include_once '../../2-utils/Database.php';
include_once '../../3-logic/server-logic.php';

// תוסף לעבודה עם קבצי אקסל
require "../../vendor/autoload.php";

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


$database = new Database();
$db = $database->connect();

$dbLogic = new Logic($db);

if (isset($_POST['eventName'])) {
  $filName = $_FILES["eventTable"]['name'];
  $fileExt = pathinfo($filName, PATHINFO_EXTENSION);
  $fileNamePure = pathinfo($filName, PATHINFO_FILENAME);
  $allowedExtentions = ['xls', 'csv', 'xlsx'];

  if (in_array($fileExt, $allowedExtentions)) {
    $inputFileNamePath = $_FILES['eventTable']['tmp_name'];
    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($inputFileNamePath);
    $data = $spreadsheet->getActiveSheet()->toArray();
    // Create table in the Database only if not exist
    $dbLogic->createNewEvent($fileNamePure);
    // Insert records to the new table
    $result = $dbLogic->insertAttendees($data, $fileNamePure);
    $msg = true;
  }
}

// Torn it into Json
echo json_encode(["status" => $msg, "data" => $_POST, "files" => $_FILES['eventTable']]);
