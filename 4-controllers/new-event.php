<?php

// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include_once '../2-utils/Database.php';
include_once '../3-logic/server-logic.php';

// תוסף לעבודה עם קבצי אקסל
require "../vendor/autoload.php";

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$database = new Database();
$db = $database->connect();

$dbLogic = new Logic($db);

$customTableName = uniqid($prefix = "event_");

$msg = "";

if (isset($_POST['eventName'])) {
  // Table handler
  if ($dbLogic->checkEventExisting($_POST['eventName'])) {
    $msg = "Event already exist in the database";
  } else {


    // $dbLogic->checkEventExisting($_POST['eventName']);
    $tableFileName = $_FILES["eventTable"]['name'];
    $tableFileExt = pathinfo($tableFileName, PATHINFO_EXTENSION);
    $tableFileNamePure = pathinfo($tableFileName, PATHINFO_FILENAME);
    $allowedExtentions = ['xls', 'csv', 'xlsx'];

    // logo handler - Todo!
    // $tableFileName = $_FILES["eventTable"]['name'];
    // $tableFileExt = pathinfo($tableFileName, PATHINFO_EXTENSION);
    // $tableFileNamePure = pathinfo($tableFileName, PATHINFO_FILENAME);
    // $allowedExtentions = ['xls', 'csv', 'xlsx'];

    if (in_array($tableFileExt, $allowedExtentions)) {
      $inputFileNamePath = $_FILES['eventTable']['tmp_name'];
      $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($inputFileNamePath);
      $data = $spreadsheet->getActiveSheet()->toArray();
      // Create table in the Database only if not exist
      $result = $dbLogic->createNewEventHebName($_POST['eventName'], $_POST['eventTitle'], $customTableName);
      $eventID = $result[0]["id"];
      $dbLogic->createNewEvent($customTableName);
      // Insert records to the new table
      $result = $dbLogic->insertAttendees($data, $customTableName, $eventID);
      $msg = true;
    }
  }
}

// Turn it into Json
echo json_encode(["status" => $msg, "data" => $_POST, "files" => $_FILES['eventTable']]);
