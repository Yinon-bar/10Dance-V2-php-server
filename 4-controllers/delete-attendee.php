<?php

// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include_once '../2-utils/Database.php';
include_once '../3-logic/server-logic.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
  http_response_code(200);
  exit;
}

// var_dump($_GET);

$database = new Database();
$db = $database->connect();

$attendeeId = $_GET['id'];
$deleteLogic = new Logic($db);
$result = $deleteLogic->deleteSingleAttendee($attendeeId);

if ($result->rowCount() > 0) {
  $attendees_arr = [];
  while ($row = $result->fetch(PDO::FETCH_OBJ)) {
    array_push($attendees_arr, $row);
  }
  echo json_encode(["Message" => "האורח נמחק בהצלחה"]);

  // Turn it into Json
  echo json_encode($attendees_arr);
} else {
  echo json_encode(
    [
      "message" => 'אורח לא נמצא'
    ]
  );
}
