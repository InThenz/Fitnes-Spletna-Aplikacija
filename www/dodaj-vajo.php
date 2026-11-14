<?php
session_start();

// Preverimo, ali je uporabnik prijavljen
if (!isset($_SESSION["id_uporabnik"])) {
    header("Location: prijava.php");
    exit;
}

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

$id_uporabnik = (int)$_SESSION["id_uporabnik"];

// INSERT INTO SQL v database
$sql = "INSERT INTO vaja
        (ime_vaje, misicna_skupina, opis_vaje, id_uporabnik)
        VALUES (?, ?, ?, ?)";

$stmt = $pdo->prepare($sql);
$stmt->execute([$ime, $skupina, $opis, $id_uporabnik]);

// Vrnitev na glavno stran, da nisem na blank page.
header("Location: index.php?dodano=1");
exit;
