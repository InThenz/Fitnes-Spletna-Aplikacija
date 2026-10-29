<?php

$host = "mysql";
$dbname = "fitnes_zapisnik";
$username = "root";
$password = "superVarnoGeslo";

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password
    );

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    echo "NAPAKA MYSQL: " . $e->getMessage();
    exit;
}
