<?php
function getPDO() {
    $host = "localhost";
    $db   = "demo_db";
    $user = "root";
    $pass = "";

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(["error" => "DB connection failed", "details" => $e->getMessage()]);
        exit;
    }
}
