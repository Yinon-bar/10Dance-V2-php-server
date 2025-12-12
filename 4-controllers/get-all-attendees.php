<?php

// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include_once '../2-utils/Database.php';
include_once '../3-logic/server-logic.php';

// var_dump($_GET);

$database = new Database();
$db = $database->connect();

$tableId = $_GET['tableId'];

$attendees = new Logic($db);

$result = $attendees->getAllFromTable($tableId);

if ($result->rowCount() > 0) {
  $attendees_arr = [];
  while ($row = $result->fetch(PDO::FETCH_OBJ)) {
    array_push($attendees_arr, $row);
  }

  // Torn it into Json
  echo json_encode($attendees_arr);
} else {
  http_response_code(404);
  echo json_encode(
    [
      "message" => 'לא נמצאו רשומות לאירוע זה'
    ]
  );
}
