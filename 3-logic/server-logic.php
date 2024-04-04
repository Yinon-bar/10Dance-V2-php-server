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

  public function checkEventExisting($eventName)
  {
    $query = "SELECT * FROM event_mapping WHERE event_name = '$eventName'";
    $stmt = $this->conn->prepare($query);
    $stmt->execute();
    if ($stmt->rowCount() > 0) {
      return true;
    }
    return false;
  }

  public function createNewEvent($customTableName)
  {
    $query = "CREATE TABLE IF NOT EXISTS $customTableName (
      id int AUTO_INCREMENT PRIMARY KEY,
      tz_id varchar(255),
      fName varchar(255),
      lName varchar(255),
      institute varchar(255),
      isArrived int,
      event_id int,
      FOREIGN KEY (event_id) REFERENCES event_mapping(id)
  )";
    $stmt = $this->conn->prepare($query);
    $stmt->execute();
    return $stmt;
  }

  public function createNewEventHebName($hebName)
  {
    $checkExistingQuery = "SELECT * FROM event_mapping WHERE event_name = '$hebName'";
    $chkStmt = $this->conn->prepare($checkExistingQuery);
    $chkStmt->execute();
    if ($chkStmt->rowCount() > 0) {
      $result = $chkStmt->fetchAll(PDO::FETCH_ASSOC);
      return $result;
    } else {
      $insertHebEventNameQuery = "INSERT INTO event_mapping (event_name) VALUES('$hebName')";
      $stmt = $this->conn->prepare($insertHebEventNameQuery);
      $stmt->execute();
      if ($stmt->rowCount() > 0) {
        $insertHebEventNameQuery = "SELECT * FROM event_mapping WHERE event_name = '$hebName' LIMIT 1";
        $stmt = $this->conn->prepare($insertHebEventNameQuery);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
      }
    }
  }

  public function insertAttendees($data, $fileNamePure, $eventID)
  {
    foreach ($data as $row) {
      $tz_id = $row['0'];
      $fName = $row['1'];
      $lName = $row['2'];
      $institute = $row['3'];
      $isArrived = $row['4'];
      $event_id = $eventID;

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
