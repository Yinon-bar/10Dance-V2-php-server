<?php
header("Content-Type: application/json; charset=utf-8");

// תראה שגיאות על המסך (רק לפיתוח!)
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require_once __DIR__ . "/../vendor/autoload.php"; // תתאים אם צריך
$config = require __DIR__ . "/../2-utils/config.php";      // תתאים אם צריך

use SendGrid\Mail\Mail;

try {
  if (!isset($config["SENDGRID_API_KEY"])) {
    throw new Exception("Missing SENDGRID_API_KEY in config");
  }

  $key = trim($config["SENDGRID_API_KEY"]);
  echo json_encode(["step" => "config_loaded", "key_len" => strlen($key)]) . "\n";

  $mail = new Mail();
  // חשוב: שים כאן מייל FROM מאומת ב-SendGrid (Single Sender)
  $mail->setFrom($config["MAIL_FROM_EMAIL"] ?? "yinonbar1988@gmail.com", "10Dance Test");
  $mail->setSubject("SendGrid API Test");
  $mail->addTo("yinonbar1988@gmail.com"); // תחליף למייל שלך
  $mail->addContent("text/plain", "Hello from SendGrid test 2");

  $sendgrid = new \SendGrid($key);
  $response = $sendgrid->send($mail);

  echo json_encode([
    "step" => "send_called",
    "status" => $response->statusCode(),
    "body" => $response->body(),
    "headers" => $response->headers()
  ]);
} catch (Throwable $e) {
  http_response_code(500);
  echo json_encode([
    "step" => "exception",
    "message" => $e->getMessage(),
    "file" => $e->getFile(),
    "line" => $e->getLine()
  ]);
}
