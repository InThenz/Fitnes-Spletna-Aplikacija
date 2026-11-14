<?php
session_start();
require_once "db.php";

// Preverimo, ali je bil obrazec poslan
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
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


// Pridobimo podatke iz obrazca
$uporabnisko_ime = trim($_POST["uporabnisko_ime"] ?? "");
$email = trim($_POST["email"] ?? "");
$geslo = $_POST["geslo"] ?? "";
$ponovi_geslo = $_POST["ponovi_geslo"] ?? "";

// Preverimo vnesene podatke
if (
    strlen($uporabnisko_ime) < 3 ||
    strlen($uporabnisko_ime) > 50 ||
    strlen($email) > 255 ||
    !filter_var($email, FILTER_VALIDATE_EMAIL)
) {
    exit("Vnesi veljavno uporabniško ime in e-pošto.");
}

if (strlen($geslo) < 8) {
    exit("Geslo mora vsebovati vsaj 8 znakov.");
}

if ($geslo !== $ponovi_geslo) {
    exit("Gesli se ne ujemata.");
}

// Geslo varno pretvorimo v hash z uporabo password_default
$geslo_hash = password_hash($geslo, PASSWORD_DEFAULT);

// Shranimo uporabnika v podatkovno bazo
$sql = "INSERT INTO uporabnik
        (uporabnisko_ime, email, geslo_hash)
        VALUES (?, ?, ?)";

$stmt = $pdo->prepare($sql);

try {
    $stmt->execute([
        $uporabnisko_ime,
        $email,
        $geslo_hash
    ]);
} catch (PDOException $e) {
    if ($e->getCode() === "23000") {
        header("Location: registracija.php?napaka=obstaja");
        exit;
    }

    throw $e;
}

// Preusmeritev po uspešni registraciji
header("Location: registracija.php?uspeh=1");
exit;
