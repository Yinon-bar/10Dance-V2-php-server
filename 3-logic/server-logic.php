<?php

header("Access-Control-Allow-Origin: *"); // Allow requests from any origin
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS"); // Allow the specified methods
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization"); // Allow the specified headers

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

  public function checkEventExistingByName(string $eventName): bool
  {
    $query = "SELECT id FROM events WHERE name = :name LIMIT 1";
    $stmt  = $this->conn->prepare($query);
    $stmt->execute([':name' => $eventName]);
    return (bool) $stmt->fetch(PDO::FETCH_ASSOC);
  }

  public function createEvent(string $eventName, string $eventTitle): int
  {
    $query = "
      INSERT INTO events (name, title, created_at)
      VALUES (:name, :title, NOW())
    ";

    $stmt = $this->conn->prepare($query);
    $stmt->execute([
      ':name'  => $eventName,
      ':title' => $eventTitle,
    ]);

    return (int) $this->conn->lastInsertId();
  }

  public function insertAttendeesForEvent(array $rows, int $eventId): void
  {
    $isFirstRow = true;

    $query = "
      INSERT INTO attendees (event_id, tz_id, first_name, last_name, institute)
      VALUES (:event_id, :tz_id, :first_name, :last_name, :institute)
    ";

    $stmt = $this->conn->prepare($query);

    foreach ($rows as $row) {
      // דילוג על שורת כותרת
      if ($isFirstRow) {
        $isFirstRow = false;
        continue;
      }

      $tzId      = trim($row[0] ?? '');
      $firstName = trim($row[1] ?? '');
      $lastName  = trim($row[2] ?? '');
      $institute = trim($row[3] ?? '');

      // אפשר לעשות קצת ולידציה מינימלית
      if ($tzId === '' || $firstName === '' || $lastName === '') {
        continue;
      }

      $stmt->execute([
        ':event_id'   => $eventId,
        ':tz_id'      => $tzId,
        ':first_name' => $firstName,
        ':last_name'  => $lastName,
        ':institute'  => $institute,
      ]);
    }
  }


  public function createNewEventHebName($hebName, $hebTitle, $customTableName)
  {
    $checkExistingQuery = "SELECT * FROM event_mapping WHERE event_name = '$hebName'";
    $chkStmt = $this->conn->prepare($checkExistingQuery);
    $chkStmt->execute();
    if ($chkStmt->rowCount() > 0) {
      $result = $chkStmt->fetchAll(PDO::FETCH_ASSOC);
      return $result;
    } else {
      $insertHebEventNameQuery = "INSERT INTO event_mapping (event_name, event_title, event_table) VALUES('$hebName', '$hebTitle', '$customTableName')";
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

  public function addNewAttendee($user)
  {
    // שם הטבלה מתוך $user
    $table = $user["event_table"];
    $query = "INSERT INTO $table (tz_id, fName, lName, institute, isArrived, event_id)
              VALUES ('$user[tz_id]', '$user[first_name]', '$user[last_name]', '$user[institute]', 1, 1)";

    $stmt = $this->conn->prepare($query);
    $stmt->execute();
    return $stmt;
  }

  public function deleteSingleAttendee($id, $table)
  {
    $query = "DELETE FROM `$table` WHERE id = :id LIMIT 1";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(":id", $id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt;
  }

  public function getAllFromTable($tableName)
  {
    $query = "SELECT * FROM $tableName";
    $stmt = $this->conn->prepare($query);
    $stmt->execute();
    return $stmt;
  }

  public function getAttendeeByTz($tz, $tableName)
  {
    // print_r($tz);
    // print_r($tableName);
    $query = "SELECT * FROM $tableName WHERE tz_id = '$tz'";
    $stmt = $this->conn->prepare($query);
    $stmt->execute();

    // if ($stmt->rowCount() > 0) {

    return $stmt;
  }

  public function loginUser($user)
  {
    $query = "SELECT * FROM users WHERE user_email = '$user[user_email]' AND user_password = '$user[user_password]'";
    $stmt = $this->conn->prepare($query);
    $stmt->execute();
    return $stmt;
  }

  public function registerUser($user)
  {
    // print_r($user);
    $query = "SELECT * FROM users WHERE user_email = '$user[user_email]'";
    $stmt = $this->conn->prepare($query);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
      return false;
    } else {
      $insertNewUser = "INSERT INTO users (user_name, user_email, user_password, role) VALUES('$user[user_name]', '$user[user_email]', '$user[user_password]', 2)";
      $stmtNewUser = $this->conn->prepare($insertNewUser);
      $stmtNewUser->execute();
      if ($stmtNewUser->rowCount() > 0) {
        $query = "SELECT * FROM users WHERE user_email = '$user[user_email]'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        if ($stmt->execute() > 0) {
          $insertedUser = $stmt->fetchAll(PDO::FETCH_ASSOC);
          return $insertedUser;
        }
        return false;
      }
      return false;
    }
  }
}
