<?php
session_start();

if (!isset($_SESSION["id_uporabnik"])) {
    header("Location: prijava.php");
    exit;
}

require_once "db.php";

// Preverimo administratorske pravice
$stmt = $pdo->prepare(
    "SELECT vloga FROM uporabnik WHERE id_uporabnik = ?"
);
$stmt->execute([$_SESSION["id_uporabnik"]]);

if ($stmt->fetchColumn() !== "admin") {
    http_response_code(403);
    exit("Nimaš dovoljenja za dostop.");
}

// Pridobimo ID uporabnika iz URL-ja
$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$id || $id <= 0) {
    http_response_code(400);
    exit("Neveljaven ID uporabnika.");
}

// Podatki izbranega uporabnika
$stmt = $pdo->prepare(
    "SELECT uporabnisko_ime, email, vloga
     FROM uporabnik
     WHERE id_uporabnik = ?"
);
$stmt->execute([$id]);
$uporabnik = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$uporabnik) {
    http_response_code(404);
    exit("Uporabnik ne obstaja.");
}

// Vaje uporabnika
$stmt = $pdo->prepare(
    "SELECT ime_vaje, misicna_skupina
     FROM vaja
     WHERE id_uporabnik = ?
     ORDER BY ime_vaje"
);
$stmt->execute([$id]);
$vaje = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Treningi uporabnika
$stmt = $pdo->prepare(
    "SELECT v.ime_vaje, z.datum, z.teza_kg, z.opis
     FROM zapis_vaje z
     JOIN vaja v ON z.id_vaja = v.id_vaja
     WHERE z.id_uporabnik = ?
       AND v.id_uporabnik = ?
     ORDER BY z.datum DESC, z.id_zapis DESC"
);
$stmt->execute([$id, $id]);
$treningi = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html lang="sl" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Podrobnosti uporabnika</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="tema.js"></script>
</head>
<body>

<main class="container mt-4">

    <a href="admin.php" class="btn btn-outline-primary mb-4">
        Nazaj na administracijo
    </a>

    <h1>Podrobnosti uporabnika</h1>

    <div class="card p-3 my-4">
        <h4>
            <?= htmlspecialchars($uporabnik["uporabnisko_ime"]) ?>
        </h4>

        <p>E-pošta:
            <?= htmlspecialchars($uporabnik["email"]) ?>
        </p>

        <p>Vloga:
            <?= htmlspecialchars($uporabnik["vloga"]) ?>
        </p>
    </div>

    <h3>Vaje uporabnika</h3>

    <table class="table table-striped mb-4">
        <thead>
            <tr>
                <th>Ime vaje</th>
                <th>Mišična skupina</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($vaje as $vaja): ?>
                <tr>
                    <td><?= htmlspecialchars($vaja["ime_vaje"]) ?></td>
                    <td><?= htmlspecialchars($vaja["misicna_skupina"]) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <?php if (empty($vaje)): ?>
        <p>Uporabnik še nima ustvarjenih vaj.</p>
    <?php endif; ?>

    <h3>Zgodovina treningov</h3>

    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Vaja</th>
                    <th>Datum</th>
                    <th>Teža (kg)</th>
                    <th>Opis</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($treningi as $trening): ?>
                    <tr>
                        <td><?= htmlspecialchars($trening["ime_vaje"]) ?></td>
                        <td><?= htmlspecialchars($trening["datum"]) ?></td>
                        <td><?= htmlspecialchars($trening["teza_kg"]) ?> kg</td>
                        <td><?= htmlspecialchars($trening["opis"] ?? "") ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php if (empty($treningi)): ?>
        <p>Uporabnik še nima zabeleženih treningov.</p>
    <?php endif; ?>

</main>

</body>
</html>

