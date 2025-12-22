<?php
// Headers
header("Access-Control-Allow-Origin: *"); // Allow requests from any origin
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS"); // Allow the specified methods
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization"); // Allow the specified headers

include_once '../2-utils/Database.php';
include_once '../3-logic/server-logic.php';
include_once '../3-logic/jwt-logic.php';
include_once '../vendor/autoload.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
  http_response_code(200);
  exit;
}

use \Firebase\JWT\JWT;

$database = new Database();
$db = $database->connect();
$appLogic = new Logic($db);
$jwtLogic = new JwtLogic();

$dataJson = json_decode(file_get_contents("php://input")); // חילוץ של ג'ייסון טהור

// print_r($dataJson);

// JSON לא תקין
if (!$dataJson) {
  http_response_code(400);
  echo json_encode(["message" => "קובץ Json לא תקין"]);
  exit;
}

// בדיקות שדות חובה
$required = ['id', 'name', 'title', 'institute'];
foreach ($required as $field) {
  if (!isset($dataJson->$field) || $dataJson->$field === "") {
    http_response_code(422);
    echo json_encode(["message" => "Missing field: $field"]);
    exit;
  }
}

// print_r($dataJson);
$attendeeToUpdate = [
  "id" => $dataJson->id,
  "name" => $dataJson->name,
  "title" => $dataJson->title,
  "institute" => $dataJson->institute,
];

$result = $appLogic->updateEvent($attendeeToUpdate);

if ($result->rowCount() > 0) {
  $apdatedAttendee = [];
  while ($row = $result->fetch(PDO::FETCH_OBJ)) {
    array_push($apdatedAttendee, $row);
  }
  // print_r($apdatedAttendee);
  http_response_code(200);
  echo json_encode(["message" => "אירוע עודכן בהצלחה"]);
} else {
  http_response_code(404);
  echo json_encode(
    ["error" => "משתמש לא נמצא"]
  );
  // http_response_code(401);
}
