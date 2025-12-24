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

  public function getEventsNames()
  {
    $query = 'SELECT * FROM events';
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

  public function createEvent(string $eventName, string $eventTitle, string $institute): int
  {
    $query = "
      INSERT INTO events (name, title, institute, created_at)
      VALUES (:name, :title, :institute, NOW())
    ";
    $stmt = $this->conn->prepare($query);
    $stmt->execute([
      ':name'  => $eventName,
      ':title' => $eventTitle,
      ':institute' => $institute,
    ]);
    return (int) $lastId = $this->conn->lastInsertId();
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
    $eventId = $user["event_id"];
    $query = "INSERT INTO attendees (event_id, tz_id, first_name, last_name, institute, is_arrive)
              VALUES ('$eventId', '$user[tz_id]', '$user[first_name]', '$user[last_name]', '$user[institute]', 1)";
    $stmt = $this->conn->prepare($query);
    $stmt->execute();

    $newId = $this->conn->lastInsertId();

    $stmt2 = $this->conn->prepare("SELECT * FROM attendees WHERE id = :id");
    $stmt2->execute([":id" => $newId]);

    return $stmt2;
  }

  public function updateAttendee($attendee)
  {
    // print_r($attendee);
    $query = "UPDATE attendees
              SET
              event_id   = :event_id,
              tz_id      = :tz_id, 
              first_name = :first_name,
              last_name  = :last_name,
              institute  = :institute
              WHERE id   = :id";
    $stmt = $this->conn->prepare($query);
    $stmt->bindValue(':event_id',  (int)$attendee['event_id']);
    $stmt->bindValue(':tz_id', $attendee['tz_id']);
    $stmt->bindValue(':first_name', $attendee['first_name']);
    $stmt->bindValue(':last_name',  $attendee['last_name']);
    $stmt->bindValue(':institute',  $attendee['institute']);
    $stmt->bindValue(':id', (int)$attendee['id']);
    $stmt->execute();

    $stmt2 = $this->conn->prepare("SELECT * FROM attendees WHERE id = :id");
    $stmt2->execute([":id" => $attendee['id']]);
    return $stmt2;
  }

  public function updateEvent($event)
  {
    // print_r($attendee);
    $query = "UPDATE events
              SET
              name = :name,
              title  = :title,
              institute  = :institute
              WHERE id   = :id";
    $stmt = $this->conn->prepare($query);
    $stmt->bindValue(':name',  $event['name']);
    $stmt->bindValue(':title',  $event['title']);
    $stmt->bindValue(':institute',  $event['institute']);
    $stmt->bindValue(':id', (int)$event['id']);
    $stmt->execute();
    return $stmt;
  }

  public function deleteSingleAttendee($id)
  {
    $query = "DELETE FROM attendees WHERE id = :id LIMIT 1";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(":id", $id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt;
  }

  public function deleteSingleEvent($id)
  {
    $query = "DELETE FROM events WHERE id = :id";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(":id", $id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt;
  }

  public function getAllFromTable($tableId)
  {
    $query = "SELECT * FROM attendees WHERE event_id = $tableId";
    $stmt = $this->conn->prepare($query);
    $stmt->execute();
    return $stmt;
  }

  public function getAttendeeByTz($tz)
  {
    $query = "SELECT * FROM attendees WHERE tz_id = '$tz'";
    $stmt = $this->conn->prepare($query);
    $stmt->execute();
    return $stmt;
  }

  public function getUserByEmail($userEmail)
  {
    $query = "SELECT * FROM users WHERE user_email = :userEmail";
    $stmt = $this->conn->prepare($query);
    $stmt->execute([":userEmail" => $userEmail]);
    return $stmt->fetch(PDO::FETCH_OBJ);
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
      $insertNewUser = "INSERT INTO users (user_name, user_email, user_password, role, institute_id) VALUES('$user[user_name]', '$user[user_email]', '$user[user_password]', 2, '$user[institute_id]')";
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
  public function savePasswordResetForUser($userId, $tokenHash, $expiresAt)
  {
    $query = "UPDATE users
              SET reset_token_hash = :token_hash,
                  reset_expires_at = :expires_at
              WHERE id = :id
              LIMIT 1";

    $stmt = $this->conn->prepare($query);
    $stmt->execute([
      ":token_hash" => $tokenHash,
      ":expires_at" => $expiresAt,
      ":id" => $userId
    ]);

    return $stmt->rowCount() > 0;
  }
}
