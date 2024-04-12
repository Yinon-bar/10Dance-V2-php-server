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

$user = [
  "user_institute" => $dataJson->user_institute, "user_email" => $dataJson->user_email, "user_password" => $dataJson->user_password, "user_name" => $dataJson->user_name
];
// save the original password
$password_to_verify_later = $user["user_password"];
// Bcrypt the password
$user['user_password'] = password_hash($user['user_password'], PASSWORD_BCRYPT);

$result = $appLogic->registerUser($user);
// print_r($result);

if ($result) {
  // if email not taken continue
  $userWithToken = $jwtLogic->createJWTtoken($result);
  // encrypte password
  $correctPassword = password_verify($password_to_verify_later, $user["user_password"]);
  if ($correctPassword) {
    // Turn it into Json
    echo json_encode($userWithToken);
  }
  // print_r($authUser);
} else {
  echo json_encode(
    ["error" => 503]
  );
  // http_response_code(401);
}
