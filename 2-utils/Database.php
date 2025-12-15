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
    $localConfig = __DIR__ . '/config.php';                 // /public_html/2-utils/config.php
    $prodConfig  = dirname(__DIR__, 2) . '/config.php';     // /domains/.../config.php
    $configPath = file_exists($localConfig) ? $localConfig : $prodConfig;

    if (!file_exists($configPath)) {
      throw new RuntimeException("config.php not found at: $configPath");
    }


    $config = require $configPath;
    // טעינת פרטי החיבור מתוך קובץ config.php
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
