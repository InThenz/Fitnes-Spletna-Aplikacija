<?php

session_start();

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

$id_uporabnik = 1; // Testni uporabnik - nimam še prijavnega sistema
$id_vaja = filter_input(INPUT_POST, "id_vaja", FILTER_VALIDATE_INT);

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

