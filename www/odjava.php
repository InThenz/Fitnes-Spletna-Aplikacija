<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    exit("Neveljavna zahteva.");
}

// Izpraznimo podatke uporabnikove seje
$_SESSION = [];

// Uničimo sejo
session_destroy();

// Uporabnika preusmerimo na prijavo
header("Location: prijava.php");
exit;
