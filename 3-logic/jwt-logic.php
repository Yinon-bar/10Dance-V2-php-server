<?php

header("Access-Control-Allow-Origin: *"); // Allow requests from any origin
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS"); // Allow the specified methods
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization"); // Allow the specified headers

include_once '../vendor/autoload.php';

use \Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtLogic
{
  // Create secret key
  private $secret_key = "owt125";

  public function __construct() {}

  public function createJWTtoken($authUser)
  {
    // print_r($authUser);
    // Create payload info
    $payload_info = [
      "iss" => "localhost",
      "iat" => time(),
      "nbf" => time(),
      "exp" => time() + 259200,
      "aud" => "myusers",
      "data" => array(
        "id" => $authUser["id"],
        "name" => $authUser["user_name"],
        "email" => $authUser["user_email"],
        "role" => $authUser["role"]
      )
    ];

    // Generate JWT token
    $jwt = JWT::encode($payload_info, $this->secret_key, "HS256");
    $authUser["jwt"] = $jwt;
    return ($authUser);
  }

  public function getBearerToken(): ?string
  {
    $headers = getallheaders();
    $auth = $headers["Authorization"] ?? $headers["authorization"] ?? null;
    if (!$auth) return null;
    // מצפה לפורמט: "Bearer TOKEN"
    // פונקציה שמחזירה מערך שיופרד לאיברים ע"י ההגדרה " " שם סטרינג שיש לבצע עליו את הפונקציה - פרמטר שני
    $parts = explode(" ", $auth);
    // אנחנו אמורים לקבל מערך כזה
    // ["Bearer", "eyJhbGciOiJIUzI1NiJ9"]
    // אם לא קיבלנו 2 איברים יש להחזיר נאל כי משהו לא תקין
    if (count($parts) !== 2) return null;
    // אם החלק הראשון לא שווה ל ברר יש להחזיר נאל
    if ($parts[0] !== "Bearer") return null;

    return $parts[1];
  }
}
