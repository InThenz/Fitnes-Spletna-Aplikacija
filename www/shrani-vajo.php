
<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

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

// Začasni ID-ji, dokler ne izdelava prijave in izbire vaj.
$id_uporabnik = 1;
$id_vaja = 1;

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

echo "Trening je uspešno shranjen!";
