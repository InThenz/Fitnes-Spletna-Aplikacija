<?php
session_start();


if (!isset($_SESSION["id_uporabnik"])) {
    header("Location: prijava.php");
    exit;
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

require_once "db.php";

// Brisanje dovolimo samo prek POST zahtevka
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    exit("Neveljavna zahteva.");
}

// Pridobimo ID zapisa in vaje
$id_zapis = filter_input(INPUT_POST, "id_zapis", FILTER_VALIDATE_INT);
$id_vaja = filter_input(INPUT_POST, "id_vaja", FILTER_VALIDATE_INT);

// Preverimo veljavnost podatkov
if (!$id_zapis || !$id_vaja) {
    exit("Neveljaven zapis ali vaja.");
}

$id_uporabnik = (int)$_SESSION["id_uporabnik"];

// Izbrišemo samo zapis, ki pripada uporabniku in vaji
$sql = "DELETE FROM zapis_vaje
        WHERE id_zapis = ?
        AND id_uporabnik = ?
        AND id_vaja = ?";

$stmt = $pdo->prepare($sql);
$stmt->execute([$id_zapis, $id_uporabnik, $id_vaja]);

if ($stmt->rowCount() === 1) {
    $_SESSION["uspesno_izbrisano"] = true;
}

// Vrnemo se na ustrezno vajo
header("Location: index.php?id_vaja=" . $id_vaja);
exit;
