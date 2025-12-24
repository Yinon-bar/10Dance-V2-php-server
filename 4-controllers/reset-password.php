<?php
// Headers
header("Access-Control-Allow-Origin: *"); // Allow requests from any origin
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS"); // Allow the specified methods
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization"); // Allow the specified headers

include_once '../2-utils/Database.php';
include_once '../3-logic/server-logic.php';
include_once '../3-logic/jwt-logic.php';
include_once '../vendor/autoload.php';
include_once '../3-logic/send-grid.php';
$config = require __DIR__ . "/../2-utils/config.php";


if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
  http_response_code(200);
  exit;
}

$database = new Database();
$db = $database->connect();
$appLogic = new Logic($db);

$data = json_decode(file_get_contents("php://input",), true);

$token = $data["token"] ?? null;
$newPassword = $data["new_password"] ?? null;

if (!$token || !$newPassword) {
  http_response_code(400);
  echo json_encode(["error" => "Missing token or new_password"]);
  exit;
}

if (strlen($newPassword) < 8) {
  http_response_code(400);
  echo json_encode(["error" => "Password must be at least 8 characters"]);
  exit;
}

try {
  // 1) מגבים את הטוקן שהגיע מהקליינט
  $tokenHash = hash('sha256', $token);
  // 2) מחפשים בטבלת הריסט טוקן תקף (לא used ולא פג תוקף)
  $resetRow = $appLogic->getValidPasswordResetRowByTokenHash($tokenHash);

  if (!$resetRow) {
    http_response_code(401);
    echo json_encode(["error" => "Invalid or expired token"]);
    exit;
  }
  $userEmail = $resetRow->user_email;
  // 3) מעדכנים סיסמה
  $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);

  $updated = $appLogic->updatePasswordByEmail($userEmail, $passwordHash);
  if (!$updated) {
    http_response_code(500);
    echo json_encode(["error" => "Failed to update password"]);
    exit;
  }

  // 4) מסמנים את הטוקן כ-used (או מוחקים אותו)
  $appLogic->deleteUsedToken($resetRow->id);

  http_response_code(200);
  echo json_encode(["message" => "הסיסמה עודכנה בהצלחה!"]);
} catch (Exception $e) {
  http_response_code(500);
  echo json_encode(["error" => "שגיאת שרת", "message" => $e]);
}
