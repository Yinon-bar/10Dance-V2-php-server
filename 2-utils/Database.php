<?php

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
