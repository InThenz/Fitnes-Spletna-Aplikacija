<?php
require_once "db.php";

// Preverimo, ali so podatki poslani iz obrazca
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    exit("Neveljavna zahteva.");
}

// Pridobimo podatke iz obrazca
$ime = trim($_POST["ime_vaje"] ?? "");
$skupina = trim($_POST["misicna_skupina"] ?? "");
$opis = trim($_POST["opis_vaje"] ?? "");

// Preverimo obvezna polja
if ($ime === "" || $skupina === "") {
    exit("Ime vaje in mišična skupina sta obvezna.");
}

// Pripravimo SQL poizvedbo
$sql = "INSERT INTO vaja
        (ime_vaje, misicna_skupina, opis_vaje)
        VALUES (?, ?, ?)";

$stmt = $pdo->prepare($sql);
$stmt->execute([$ime, $skupina, $opis]);

// Vrnemo uporabnika na glavno stran
header("Location: index.php?dodano=1");
exit;
