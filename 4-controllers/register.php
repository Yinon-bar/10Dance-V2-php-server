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
use Firebase\JWT\Key;

$database = new Database();
$db = $database->connect();
$appLogic = new Logic($db);
$jwtLogic = new JwtLogic();

// שלב ראשון, יש לחלץ את הטוקן מהבקשה
$token = $jwtLogic->getBearerToken();
if (!$token) {
  http_response_code(401);
  echo json_encode(["error" => "Missing token"]);
  exit;
}
// print_r($token);
// בשלב השני יש לפענח את ה הטוקן שקיבלנו ולהפוך אותו לאובייקט
$userObj = $jwtLogic->decodeJwt($token);
// print_r($userObj->data->id);
// בשלשב השלישי יש לוודא שמי ששלח את הבקשה הוא סופר אדמין
if ($userObj->data->role !== 3) {
  http_response_code(403);
  echo json_encode(["error" => "אינך מורשה לבצע פעולה זו"]);
  exit;
}
// שלב רביעי ואחרון יש ליצור את האדמין החדש
try {
  // לאסוף את הנתונים מגוף הבקשה למערך חדש
  $dataJson = json_decode(file_get_contents("php://input"));
  $user = [
    "institute_id"  => $dataJson->institute_id,
    "user_email"    => $dataJson->user_email,
    "user_password" => $dataJson->user_password,
    "user_name"     => $dataJson->user_name,
    "role"          => $dataJson->role
  ];
  // print_r($user);
  // Bcrypt the password
  $user['user_password'] = password_hash($user['user_password'], PASSWORD_BCRYPT);
  // print_r($user['user_password']);
  $result = $appLogic->registerUser($user);
  if ($result) {
    // if email not taken continue
    http_response_code(201);
    echo json_encode($result);
  } else {
    echo json_encode(
      ["error" => "שגיאה ביצירת המשתמש - או שהמשתמש קיים"]
    );
    // http_response_code(401);
  }
} catch (\Throwable $th) {
  throw $th;
}
