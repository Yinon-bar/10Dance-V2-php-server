<?php
// Headers
header("Access-Control-Allow-Origin: *"); // Allow requests from any origin
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS"); // Allow the specified methods
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization"); // Allow the specified headers

include_once '../2-utils/Database.php';
include_once '../3-logic/server-logic.php';
include_once '../3-logic/jwt-logic.php';
include_once '../vendor/autoload.php';

use \Firebase\JWT\JWT;

$database = new Database();
$db = $database->connect();
$appLogic = new Logic($db);
$jwtLogic = new JwtLogic();

$dataJson = json_decode(file_get_contents("php://input"));
// print_r($dataJson->user_email);
$user = ["user_email" => $dataJson->user_email, "user_password" => $dataJson->user_password];
// $userJWT = $dataJson->jwt;

$result = $appLogic->loginUser($user);

if ($result->rowCount() > 0) {
  $authUser = [];
  while ($row = $result->fetch(PDO::FETCH_OBJ)) {
    array_push($authUser, $row);
  }
  // print_r($authUser);
  // $authUser = $jwtLogic->validateJWTtoken($userJWT);
  // Turn it into Json
  // print_r($authUser);
  echo json_encode($authUser);
} else {
  echo json_encode(
    ["error" => "משתמש לא נמצא"]
  );
  // http_response_code(401);
}
