<?php

// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include_once '../../config.php/Database.php';
include_once '../../models/Post.php';

if (isset($_POST)) {
  // print_r($_POST);
}
if (isset($_FILES)) {
  // print_r($_FILES['eventTable']);
}


// $database = new Database();
// $db = $database->connect();

// $attendees = new Post($db);

// $result = $attendees->read();



// Torn it into Json
echo json_encode(["status" => "ok", "data" => $_POST, "files" => $_FILES['eventTable']]);
// echo json_encode(
//   [
//     "message" => 'No posts found'
//   ]
// );
