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

  public function __construct()
  {
  }

  public function createJWTtoken($authUser)
  {
    // Create payload info
    $payload_info = [
      "iss" => "localhost",
      "iat" => time(),
      "nbf" => time(),
      "exp" => time() + 259200,
      "aud" => "myusers",
      "data" => array(
        "id" => $authUser[0]->id,
        "name" => $authUser[0]->user_name,
        "email" => $authUser[0]->user_email
      )
    ];

    // Generate JWT token
    $jwt = JWT::encode($payload_info, $this->secret_key, "HS256");
    array_push($authUser, array("jwt" => $jwt));
    return ($authUser);
  }

  public function valodateJWTtoken($jwt)
  {
    try {
      $decoded = JWT::decode($jwt, new Key($this->secret_key, 'HS256'));
      // If decoding successful, return decoded token
      // print_r($decoded);
      return ($decoded);
    } catch (Exception $e) {
      // If decoding failed (invalid token), return false
      echo $e;
      return false;
    }
  }
}
