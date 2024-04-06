<?php
// Headers
header("Access-Control-Allow-Origin: *"); // Allow requests from any origin
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS"); // Allow the specified methods
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization"); // Allow the specified headers

include_once '../2-utils/Database.php';
include_once '../3-logic/server-logic.php';
include_once '../vendor/autoload.php';

use \Firebase\JWT\JWT;

$database = new Database();
$db = $database->connect();
$appLogic = new Logic($db);

$dataJson = json_decode(file_get_contents("php://input"));

$user = ["user_email" => $dataJson->user_email, "user_password" => $dataJson->user_password];

$result = $appLogic->loginUser($user);

if ($result->rowCount() > 0) {
  $authUser = [];
  while ($row = $result->fetch(PDO::FETCH_OBJ)) {
    array_push($authUser, $row);
  }
  // print_r($authUser);
  // Create payload info
  $payload_info = [
    "iss" => "localhost",
    "iat" => time(),
    "nbf" => time() + 20,
    "exp" => time() + 40,
    "aud" => "myusers",
    "data" => array(
      "id" => $authUser[0]->id,
      "name" => $authUser[0]->user_name,
      "email" => $authUser[0]->user_email
    )
  ];

  // Create secret key
  $secret_key = "owt125";

  // Generate JWT token
  $jwt = JWT::encode($payload_info, $secret_key, "HS256");
  array_push($authUser, array("jwt" => $jwt));
  // Turn it into Json
  echo json_encode($authUser);
} else {
  echo json_encode(
    ["error" => 503]
  );
  // http_response_code(401);
}
