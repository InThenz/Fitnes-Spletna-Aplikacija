<?php
require_once "db.php";

// POST metoda preveri podatke poslane v obrazcu.
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    exit("Neveljavna zahteva.");
}

// Dobim podatke in jih shranim v custom vrednosti
$ime = trim($_POST["ime_vaje"] ?? "");
$skupina = trim($_POST["misicna_skupina"] ?? "");
$opis = trim($_POST["opis_vaje"] ?? "");

// Preverim obvezna polja
if ($ime === "" || $skupina === "") {
    exit("Ime vaje in mišična skupina sta obvezna.");
}

// INSERT INTO SQL v database
$sql = "INSERT INTO vaja
        (ime_vaje, misicna_skupina, opis_vaje)
        VALUES (?, ?, ?)";

$stmt = $pdo->prepare($sql);
$stmt->execute([$ime, $skupina, $opis]);

// Vrnitev na glavno stran, da nisem na blank page.
header("Location: index.php?dodano=1");
exit;
