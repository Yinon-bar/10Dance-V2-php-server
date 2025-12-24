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
    $this->fromEmail = $config["MAIL_FROM_EMAIL"] ?? "no-reply@10dance-api.yinon-bar.com";
    $this->fromName = $config["MAIL_FROM_NAME"] ?? "10Dance";
  }

  public function sendResetPasswordEmail($toEmail, $resetLink)
  {
    try {
      $email = new Mail();
      $email->setFrom($this->fromEmail, $this->fromName);
      $email->setSubject("איפוס סיסמה");
      $email->addTo($toEmail);

      $email->setTemplateId("d-159b1cfa907748d89c28accafee6b874");

      $email->addDynamicTemplateDatas([
        "reset_link" => $resetLink
      ]);

      $sendgrid = new \SendGrid($this->apiKey);
      $response = $sendgrid->send($email);

      return $response->statusCode() >= 200 && $response->statusCode() < 300;
    } catch (\Throwable $e) {
      error_log("MailService error: " . $e->getMessage());
      return false;
    }
    $response = $sendgrid->send($mail);
  }
}
