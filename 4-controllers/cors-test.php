<?php
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Content-Type: application/json; charset=utf-8");
include_once '../2-utils/Database.php';
include_once '../3-logic/server-logic.php';
echo json_encode(["ok" => true, "file" => __FILE__]);
