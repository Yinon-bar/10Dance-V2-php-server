<?php

// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include_once '../2-utils/Database.php';
include_once '../3-logic/server-logic.php';

// var_dump($_GET);

$database = new Database();
$db = $database->connect();
$appLogic = new Logic($db);

$eventId = $_GET['id'];
$result = $appLogic->deleteSingleEvent($eventId);

if ($result->rowCount() === 0) {
  http_response_code(404);
  echo json_encode(["message" => "אירוע לא נמצא"]);
  exit;
} else {
  http_response_code(204);
  echo json_encode(["message" => "האירוע נמחק בהצלחה"]);
}
