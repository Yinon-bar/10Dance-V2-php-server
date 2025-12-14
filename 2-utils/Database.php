<?php

header("Access-Control-Allow-Origin: *"); // Allow requests from any origin
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS"); // Allow the specified methods
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization"); // Allow the specified headers

class Database
{
  // localhost
  // private $dbhost = "localhost";
  // private $dbuser = "root";
  // private $dbpassword = "";
  // private $dbname = "10dance";
  // private $connection;

  // שרת אמיתי
  private $dbhost = "srv1048.hstgr.io";
  private $dbuser = "u528206822_inon";
  private $dbpassword = "INONbar@053508384";
  private $dbname = "u528206822_10dance";
  private $connection;

  public function connect()
  {
    // טעינת פרטי החיבור מתוך קובץ ENV בשרת
    $config = require __DIR__ . '/../../private/db.php';
    $host = $config['DB_HOST'];
    $db   = $config['DB_NAME'];
    $user = $config['DB_USER'];
    $pass = $config['DB_PASS'];
    $this->connection = null;
    try {
      $this->connection = new PDO('mysql:host=' . $this->dbhost . ';dbname=' . $this->dbname, $this->dbuser, $this->dbpassword);
      $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $error) {
      echo 'Connection error: ' . $error->getMessage();
    }
    return $this->connection;
  }
}
