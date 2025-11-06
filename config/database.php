<?php
function getPDO() {
    $host = "localhost";
    $db   = "shoe_store";
    $user = "root";
    $pass = "";
    // $pass = "12345";
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(["error" => "DB connection failed", "details" => $e->getMessage()]);
        exit;
    }
}
