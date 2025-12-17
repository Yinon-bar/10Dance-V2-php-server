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
  $institute = trim($_POST['institute']);

  if ($eventName === '' || $eventTitle === '' || $institute === '') {
    throw new Exception("eventName, eventTitle and institute cannot be empty");
  }

  // בדיקה אם יש כבר אירוע בשם הזה
  if ($dbLogic->checkEventExistingByName($eventName)) {
    $response['status'] = 409;
    throw new Exception("אירוע עם שם זה כבר קיים במערכת - אנא בחר שם אחר");
  }

  // יוצרים אירוע חדש
  $eventId = $dbLogic->createEvent($eventName, $eventTitle, $institute);
  // במידה והכל תקין מעדכנים את מערך ההודעה
  $response["message"]  = "האירוע נוצר בהצלחה";
  $response["status"]   = true;
  $response["event_id"] = $eventId;
  // print_r($eventId);

  // בדיקת קובץ
  // if (!isset($_FILES['eventTable']) || $_FILES['eventTable']['error'] !== UPLOAD_ERR_OK) {
  //   throw new Exception("Excel file (eventTable) is required");
  // }
  // לא חייב לפתוח אירוע עם קובץ אקסל מוכן, אפשר לפתוח אירוע ולהכניס את הנוכחים רק אח"כ
  if (isset($_FILES['eventTable'])) {
    $fileInfo    = $_FILES['eventTable'];
    $fileName    = $fileInfo['name'];
    $fileTmpPath = $fileInfo['tmp_name'];
    $fileExt     = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $allowedExtensions = ['xls', 'xlsx', 'csv'];

    if (!in_array($fileExt, $allowedExtensions)) {
      throw new Exception("Invalid file type. Allowed: xls, xlsx, csv");
    }

    // טוענים את האקסל
    $spreadsheet = IOFactory::load($fileTmpPath);
    $data        = $spreadsheet->getActiveSheet()->toArray();

    // מכניסים את הנוכחים
    $dbLogic->insertAttendeesForEvent($data, $eventId);
    $response["status"] = 201;
    $response["message"]  = "יצירת האירוע ושיוך הנוכחים הסתיים בהצלחה";
    // החזרת JSON
  }
} catch (Exception $e) {
  http_response_code($response["status"]);
  $response["message"] = $e->getMessage();
}
echo json_encode($response);
