<?php

use SendGrid\Mail\Mail;

class MailService
{
  private $apiKey;
  private $fromEmail;
  private $fromName;

  public function __construct($config)
  {
    $this->apiKey = $config["SENDGRID_API_KEY"];
    $this->fromEmail = $config["MAIL_FROM_EMAIL"] ?? "no-reply@your-domain.com";
    $this->fromName = $config["MAIL_FROM_NAME"] ?? "10Dance";
  }

  public function sendResetPasswordEmail($toEmail, $resetLink)
  {
    try {
      $email = new Mail();
      $email->setFrom($this->fromEmail, $this->fromName);
      $email->setSubject("איפוס סיסמה");
      $email->addTo($toEmail);

      $email->addContent(
        "text/plain",
        "לאיפוס הסיסמה לחץ על הקישור:\n{$resetLink}"
      );

      $email->addContent(
        "text/html",
        "<p>לאיפוס הסיסמה לחץ על הקישור:</p>
          <p><a href='{$resetLink}'>איפוס סיסמה</a></p>"
      );

      $sendgrid = new \SendGrid($this->apiKey);
      $response = $sendgrid->send($email);

      return $response->statusCode() >= 200 && $response->statusCode() < 300;
    } catch (\Throwable $e) {
      error_log("MailService error: " . $e->getMessage());
      return false;
    }
  }
}
