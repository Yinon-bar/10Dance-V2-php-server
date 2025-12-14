<?php

class Database
{
  private PDO $connection;

  public function connect(): PDO
  {
    // טוען קונפיג (מחוץ ל-public_html או מוגן עם .htaccess)
    $config = require __DIR__ . '/../../private/db.php';

    $host = $config['DB_HOST'];
    $db   = $config['DB_NAME'];
    $user = $config['DB_USER'];
    $pass = $config['DB_PASS'];
    $charset = $config['DB_CHARSET'] ?? 'utf8mb4';

    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";

    if (!is_array($config)) {
      throw new Exception("DB config not loaded");
    }


    $options = [
      PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
      PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    $this->connection = new PDO($dsn, $user, $pass, $options);
    return $this->connection;
  }
}
