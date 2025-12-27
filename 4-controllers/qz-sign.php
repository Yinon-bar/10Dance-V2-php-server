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

$payload = file_get_contents("php://input");
$data = json_decode($payload, true);

$toSign = $data['toSign'] ?? '';
if (!$toSign) {
  http_response_code(400);
  echo json_encode(['error' => 'Nothing to sign']);
  exit;
}

// $privateKeyPath = __DIR__ . '/../config/qz/private-key.pem';
$privateKeyPath = __DIR__ . '/../2-utils/qz/private-key.pem';
$privateKey = openssl_pkey_get_private(file_get_contents($privateKeyPath));

openssl_sign($toSign, $signature, $privateKey, OPENSSL_ALGO_SHA512);

echo json_encode([
  'signature' => base64_encode($signature)
]);
