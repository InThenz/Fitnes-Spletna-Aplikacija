<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    exit("Neveljavna zahteva.");
}

$token = $_POST["csrf_token"] ?? "";

if (
    !is_string($token) ||
    !isset($_SESSION["csrf_token"]) ||
    !hash_equals($_SESSION["csrf_token"], $token)
) {
    http_response_code(403);
    exit("Neveljaven varnostni žeton.");
}

// Izpraznimo podatke uporabnikove seje
$_SESSION = [];

// Uničimo sejo
session_destroy();

// Uporabnika preusmerimo na prijavo
header("Location: prijava.php");
exit;
