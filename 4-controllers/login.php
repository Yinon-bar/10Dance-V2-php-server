<?php
// Headers
header("Access-Control-Allow-Origin: *"); // Allow requests from any origin
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS"); // Allow the specified methods
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization"); // Allow the specified headers

include_once '../2-utils/Database.php';
include_once '../3-logic/server-logic.php';
include_once '../3-logic/jwt-logic.php';
include_once '../vendor/autoload.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
  http_response_code(200);
  exit;
}

use \Firebase\JWT\JWT;

$database = new Database();
$db = $database->connect();
$appLogic = new Logic($db);
$jwtLogic = new JwtLogic();

$dataJson = json_decode(file_get_contents("php://input"));
// print_r($dataJson->user_email);
$user = ["user_email" => $dataJson->user_email, "user_password" => $dataJson->user_password];
// $userJWT = $dataJson->jwt;

try {
  $result = $appLogic->loginUser($user);
  $row = $result->fetch(PDO::FETCH_ASSOC);

  if ($row) {
    // print_r($row);
    $authUser = $jwtLogic->createJWTtoken($row);
    // print_r($authUser);
    http_response_code(201);
    echo json_encode($authUser);
  } else {
    // משתמש לא נמצא
  }
} catch (PDOException $e) {
  http_response_code(500);
  echo json_encode([
    "error" => "שגיאת שרת"
  ]);
}
