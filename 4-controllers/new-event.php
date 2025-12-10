<?php

// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=utf-8');

include_once '../2-utils/Database.php';
include_once '../3-logic/server-logic.php';

// תוסף לעבודה עם קבצי אקסל
require "../vendor/autoload.php";

use PhpOffice\PhpSpreadsheet\IOFactory;

$database = new Database();
$db       = $database->connect();

$dbLogic = new Logic($db);

$response = [
  "status"   => false,
  "message"  => "",
  "event_id" => null,
];

try {
  // חייבים לקבל eventName ו-eventTitle
  if (!isset($_POST['eventName']) || !isset($_POST['eventTitle'])) {
    throw new Exception("Missing eventName or eventTitle");
  }

  $eventName  = trim($_POST['eventName']);
  $eventTitle = trim($_POST['eventTitle']);

  if ($eventName === '' || $eventTitle === '') {
    throw new Exception("eventName and eventTitle cannot be empty");
  }

  // בדיקה אם יש כבר אירוע בשם הזה
  if ($dbLogic->checkEventExistingByName($eventName)) {
    throw new Exception("Event already exists");
  }

  // בדיקת קובץ
  if (!isset($_FILES['eventTable']) || $_FILES['eventTable']['error'] !== UPLOAD_ERR_OK) {
    throw new Exception("Excel file (eventTable) is required");
  }

  $fileInfo    = $_FILES['eventTable'];
  $fileName    = $fileInfo['name'];
  $fileTmpPath = $fileInfo['tmp_name'];
  $fileExt     = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

  $allowedExtensions = ['xls', 'xlsx', 'csv'];

  if (!in_array($fileExt, $allowedExtensions)) {
    throw new Exception("Invalid file type. Allowed: xls, xlsx, csv");
  }

  // יוצרים אירוע חדש
  $eventId = $dbLogic->createEvent($eventName, $eventTitle);

  // טוענים את האקסל
  $spreadsheet = IOFactory::load($fileTmpPath);
  $data        = $spreadsheet->getActiveSheet()->toArray();

  // מכניסים את הנוכחים
  $dbLogic->insertAttendeesForEvent($data, $eventId);

  $response["status"]   = true;
  $response["message"]  = "Event and attendees created successfully";
  $response["event_id"] = $eventId;
} catch (Exception $e) {
  $response["status"]  = false;
  $response["message"] = $e->getMessage();
}

// החזרת JSON
echo json_encode($response, JSON_UNESCAPED_UNICODE);
