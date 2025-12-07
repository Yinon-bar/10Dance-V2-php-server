<?php

// Headers
header("Access-Control-Allow-Origin: *"); // Allow requests from any origin
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS"); // Allow the specified methods
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization"); // Allow the specified headers

include_once '../2-utils/Database.php';
include_once '../3-logic/server-logic.php';

$database = new Database();
$db = $database->connect();

$attendees = new Logic($db);

$result = $attendees->read();

if ($result->rowCount() > 0) {
  $attendees_arr = [];
  while ($row = $result->fetch(PDO::FETCH_OBJ)) {
    array_push($attendees_arr, $row);
  }

  // Turn it into Json
  echo json_encode($attendees_arr);
} else {
  // http_response_code(200);
  echo json_encode(
    [
      "message" => 'לא נמצאו משתתפים לאירוע זה'
    ]
  );
}
