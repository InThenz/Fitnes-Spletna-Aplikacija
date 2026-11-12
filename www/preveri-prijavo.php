<?php
session_start();
require_once "db.php";

// Dovolimo samo POST zahteve
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    exit("Neveljavna zahteva.");
}

// Pridobimo podatke iz obrazca
$email = trim($_POST["email"] ?? "");
$geslo = $_POST["geslo"] ?? "";

// Poiščemo uporabnika v podatkovni bazi
$sql = "SELECT id_uporabnik, uporabnisko_ime, geslo_hash
        FROM uporabnik
        WHERE email = ?";

$stmt = $pdo->prepare($sql);
$stmt->execute([$email]);

$uporabnik = $stmt->fetch(PDO::FETCH_ASSOC);

// Preverimo uporabnika in geslo
if (!$uporabnik || !password_verify($geslo, $uporabnik["geslo_hash"])) {
    header("Location: prijava.php?napaka=1");
    exit;
}

// Uspešna prijava
session_regenerate_id(true);

$_SESSION["id_uporabnik"] = (int)$uporabnik["id_uporabnik"];
$_SESSION["uporabnisko_ime"] = $uporabnik["uporabnisko_ime"];

// Preusmeritev na glavno stran
header("Location: index.php");
exit;
