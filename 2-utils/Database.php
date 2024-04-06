<?php

header("Access-Control-Allow-Origin: *"); // Allow requests from any origin
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS"); // Allow the specified methods
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization"); // Allow the specified headers

class Database
{
  private $dbhost = "localhost";
  private $dbuser = "root";
  private $dbpassword = "";
  private $dbname = "10dance";
  private $connection;

  public function connect()
  {
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
