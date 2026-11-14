<?php
session_start();

if (!isset($_SESSION["id_uporabnik"])) {
    header("Location: prijava.php");
    exit;
}

require_once "db.php";

// Dovolimo samo POST zahtevke
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    exit("Neveljavna zahteva.");
}

// Pridobimo podatke iz obrazca
$id_zapis = filter_input(INPUT_POST, "id_zapis", FILTER_VALIDATE_INT);
$id_vaja = filter_input(INPUT_POST, "id_vaja", FILTER_VALIDATE_INT);

$datum = $_POST["datum"] ?? "";
$teza = $_POST["teza"] ?? "";
$opis = trim($_POST["opis"] ?? "");

// Preverimo vnesene podatke
if (!$id_zapis || !$id_vaja) {
    exit("Neveljaven zapis ali vaja.");
}

if (!DateTime::createFromFormat("!Y-m-d", $datum)
    || DateTime::createFromFormat("!Y-m-d", $datum)->format("Y-m-d") !== $datum
    || !is_numeric($teza)
    || $teza < 0
    || $teza > 9999.99) {
    exit("Vnesi veljaven datum in težo.");
}

$id_uporabnik = (int)$_SESSION["id_uporabnik"];

// Posodobimo samo izbrani trening
$sql = "UPDATE zapis_vaje
        SET datum = ?, teza_kg = ?, opis = ?
        WHERE id_zapis = ?
        AND id_uporabnik = ?
        AND id_vaja = ?";

$stmt = $pdo->prepare($sql);

$stmt->execute([
    $datum,
    $teza,
    $opis,
    $id_zapis,
    $id_uporabnik,
    $id_vaja
]);

// Prikažemo obvestilo po preusmeritvi
$_SESSION["uspesno_urejeno"] = true;

header("Location: index.php?id_vaja=" . $id_vaja);
exit;
