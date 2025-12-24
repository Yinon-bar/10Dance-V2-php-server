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
    // שלב 1. יש להשיג את פרטי החיבור לדאטהבייס בגלל שאנחנו בפרודקשן
    // אסור לשים פה את הפרטי חיבור ולכן יש להכניס אותם לקובץ נפרד
    // בפרודקשן הקובץ נמצא במיקום מסויים ובסביבה הלוקאלית הוא נמצא במיקום אחר
    $localConfig = __DIR__ . '/config.php';              // C:\xampp\htdocs\10Dance-V2-php-server\2-utils/config.php  
    $prodConfig  = dirname(__DIR__, 2) . '/config.php';  // /domains/.../config.php  
    $configPath = file_exists($localConfig) ? $localConfig : $prodConfig;

    // יש לוודא שאחד מהקבצים נמצא
    if (!file_exists($configPath)) {
      throw new RuntimeException("config.php not found at: $configPath");
    }

    // לאחר שבררנו את מיקום הקובץ עם פרטי החיבור
    //יש לטעון את פרטי החיבור מתוך קובץ config.php
    $config = require $configPath;
    $host = $config['dbhost'];
    $db   = $config['dbname'];
    $user = $config['dbuser'];
    $pass = $config['dbpassword'];

    // פונקציית PDO רגילה לחלוטין ליצירת חיבור לדאטהבייס
    try {
      $connection = new PDO('mysql:host=' . $host . ';dbname=' . $db, $user, $pass);
      $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $error) {
      echo 'Connection error: ' . $error->getMessage();
    }
    return $connection;
  }
}
