<?php

class Logic
{
  private $conn;

  public function __construct($db)
  {
    $this->conn = $db;
  }

  public function read()
  {
    $query = 'SELECT * FROM event_mapping';
    $stmt = $this->conn->prepare($query);
    $stmt->execute();
    return $stmt;
  }

  public function createNewEvent($tableName)
  {
    $query = "CREATE TABLE IF NOT EXISTS $tableName (
      id int AUTO_INCREMENT PRIMARY KEY,
      tz_id varchar(255),
      fName varchar(255),
      lName varchar(255),
      institute varchar(255),
      isArrived int,
      event_id int
  )";
    $stmt = $this->conn->prepare($query);
    $stmt->execute();
    return $stmt;
  }

  public function insertAttendees($data, $fileNamePure)
  {
    foreach ($data as $row) {
      $tz_id = $row['0'];
      $fName = $row['1'];
      $lName = $row['2'];
      $institute = $row['3'];
      $isArrived = $row['4'];
      $event_id = $row['5'];

      $insertAttendeeQuery = "INSERT INTO $fileNamePure (tz_id, fName, lName, institute, isArrived, event_id) VALUES('$tz_id', '$fName', '$lName', '$institute', $isArrived, $event_id)";
      $stmt = $this->conn->prepare($insertAttendeeQuery);
      $stmt->execute();
    }
    return $stmt;
  }

  public function getAllFromTable($tableName)
  {
    // print_r($this->tableName);
    $query = "SELECT * FROM $tableName";
    $stmt = $this->conn->prepare($query);
    // $stmt->bindParam(1, $this->tableName);
    $stmt->execute();
    return $stmt;
  }
}
