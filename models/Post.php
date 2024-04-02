<?php

class Post
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
}
