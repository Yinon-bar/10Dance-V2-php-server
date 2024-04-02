<?php

class GetAllFromTable
{
  private $conn;
  private $tableName;

  public function __construct($db, $tableName)
  {
    $this->tableName = $tableName;
    $this->conn = $db;
  }

  public function read()
  {
    // print_r($this->tableName);
    $query = "SELECT * FROM $this->tableName";
    $stmt = $this->conn->prepare($query);
    // $stmt->bindParam(1, $this->tableName);
    $stmt->execute();
    return $stmt;
  }
}
