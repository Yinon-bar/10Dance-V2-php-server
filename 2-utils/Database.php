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

  public function connect()
  {
    $local = __DIR__ . "/config.php";
    file_exists($local)
      ? require $local
      // : require __DIR__ . '/../../config.php';
      : require dirname(__DIR__, 2) . '/config.php';

    // טעינת פרטי החיבור מתוך קובץ config.php
    $config = require $local;
    $host = $config['dbhost'];
    $db   = $config['dbname'];
    $user = $config['dbuser'];
    $pass = $config['dbpassword'];

    try {
      $connection = new PDO('mysql:host=' . $host . ';dbname=' . $db, $user, $pass);
      $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $error) {
      echo 'Connection error: ' . $error->getMessage();
    }
    return $connection;
  }
}
