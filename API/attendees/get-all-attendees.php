<?php

// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include_once '../../config.php/Database.php';
include_once '../../models/GetAllFromTable.php';

$database = new Database();
$db = $database->connect();

$tableName = $_GET['tableName'];

$attendees = new GetAllFromTable($db, $tableName);

$result = $attendees->read();

if ($result->rowCount() > 0) {
  $attendees_arr = [];
  while ($row = $result->fetch(PDO::FETCH_OBJ)) {
    array_push($attendees_arr, $row);
  }

  // Torn it into Json
  echo json_encode($attendees_arr);
} else {
  echo json_encode(
    [
      "message" => 'No posts found'
    ]
  );
}
