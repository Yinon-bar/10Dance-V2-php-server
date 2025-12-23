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

$user = $appLogic->getUserByEmail($email);

// אם אין משתמש – לא עושים כלום מיוחד (אבטחה)
if (!$user) {
  http_response_code(200);
  echo json_encode([
    "message" => "אם המייל קיים במערכת, נשלח קישור לאיפוס סיסמה"
  ]);
  exit;
}

// שלב 2.1: יצירת token אקראי וחזק
$rawToken = bin2hex(random_bytes(32)); // token למייל
$tokenHash = hash('sha256', $rawToken); // מה שנשמור ב-DB

// תוקף (נניח 15 דקות)
$expiresAt = date("Y-m-d H:i:s", time() + (15 * 60));
// שלב 3: תמיד להחזיר תשובה גנרית (גם אם לא קיים משתמש)
http_response_code(200);
echo json_encode([
  "message" => "אם המייל קיים במערכת, נשלח קישור לאיפוס סיסמה"
]);
exit;
