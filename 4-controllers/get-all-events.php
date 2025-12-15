<?php
// Headers
header("Access-Control-Allow-Origin: *"); // Allow requests from any origin
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS"); // Allow the specified methods
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization"); // Allow the specified headers

// if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
//   http_response_code(200);
//   exit;
// }

include_once '../2-utils/Database.php';
include_once '../3-logic/server-logic.php';

$database = new Database();
$db = $database->connect();

$attendees = new Logic($db);

$result = $attendees->getEventsNames();

if ($result->rowCount() > 0) {
  $events_arr = [];
  while ($row = $result->fetch(PDO::FETCH_OBJ)) {
    array_push($events_arr, $row);
  }

  // Turn it into Json
  echo json_encode($events_arr);
} else {
  http_response_code(404);
  echo json_encode(
    [
      "message" => 'לא נמצאו אירועים'
    ]
  );
}
