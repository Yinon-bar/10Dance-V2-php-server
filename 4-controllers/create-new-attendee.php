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
$user = [
  "tz_id" => $dataJson->tzId,
  "event_id" => $dataJson->eventId,
  "first_name" => $dataJson->fName,
  "last_name" => $dataJson->lName,
  "institute" => $dataJson->institute,
];

$result = $appLogic->addNewAttendee($user);

if ($result->rowCount() > 0) {
  $newAttendee = [];
  while ($row = $result->fetch(PDO::FETCH_OBJ)) {
    array_push($newAttendee, $row);
  }
  echo json_encode(["message" => "משתמש נוסף בהצלחה"]);
} else {
  echo json_encode(
    ["error" => "משתמש לא נמצא"]
  );
  // http_response_code(401);
}
