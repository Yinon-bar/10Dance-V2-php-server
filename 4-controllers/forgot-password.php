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

$database = new Database();
$db = $database->connect();
$appLogic = new Logic($db);

$data = json_decode(file_get_contents("php://input"));

if (!isset($data->email) || !filter_var($data->email, FILTER_VALIDATE_EMAIL)) {
  http_response_code(400);
  echo json_encode(["error" => "Email is required"]);
  exit;
}

$email = strtolower(trim($data->email));

if ($userObj->data->role !== 3) {
  http_response_code(403);
  echo json_encode(["error" => "אינך מורשה לבצע פעולה זו"]);
  exit;
}
// שלב רביעי ואחרון יש ליצור את האדמין החדש
try {
} catch (\Throwable $th) {
  throw $th;
}
