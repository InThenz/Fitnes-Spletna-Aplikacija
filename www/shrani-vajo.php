<?php

session_start();

// Preverimo, ali je uporabnik prijavljen
if (!isset($_SESSION["id_uporabnik"])) {
    header("Location: prijava.php");
    exit;
}

require_once "db.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    exit("Neveljavna zahteva.");
}

$datum = $_POST["datum"] ?? "";
$teza = $_POST["teza"] ?? "";

if ($datum === "" || !is_numeric($teza) || $teza < 0) {
    exit("Vnesi veljaven datum in težo.");
}

$opis = trim($_POST["opis"] ?? "");

$id_uporabnik = (int)$_SESSION["id_uporabnik"];
$id_vaja = filter_input(INPUT_POST, "id_vaja", FILTER_VALIDATE_INT);

$sqlPreveri = "SELECT id_vaja FROM vaja
               WHERE id_vaja = ? AND id_uporabnik = ?";

$stmtPreveri = $pdo->prepare($sqlPreveri);
$stmtPreveri->execute([$id_vaja, $id_uporabnik]);

if (!$stmtPreveri->fetch()) {
    http_response_code(403);
    exit("Nimaš dostopa do te vaje.");
}

if ($id_vaja === false || $id_vaja === null || $id_vaja <= 0) {
    exit("Neveljavna vaja.");
}

$sql = "INSERT INTO zapis_vaje
        (id_uporabnik, id_vaja, datum, teza_kg, opis)
        VALUES (?, ?, ?, ?, ?)";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    $id_uporabnik,
    $id_vaja,
    $datum,
    $teza,
    $opis
]);

$_SESSION["uspesno_shranjeno"] = true;

header("Location: index.php?shranjeno=1&id_vaja=" . $id_vaja); //pošlje nazaj na index.php in naredi, da je vse odprto.
exit;

