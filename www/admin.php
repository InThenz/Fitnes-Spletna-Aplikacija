<?php
session_start();

// Preverimo, ali je uporabnik prijavljen
if (!isset($_SESSION["id_uporabnik"])) {
    header("Location: prijava.php");
    exit;
}

require_once "db.php";

$id_uporabnik = (int)$_SESSION["id_uporabnik"];

// Preverimo administratorsko vlogo v podatkovni bazi
$stmt = $pdo->prepare(
    "SELECT vloga FROM uporabnik WHERE id_uporabnik = ?"
);

$stmt->execute([$id_uporabnik]);
$vloga = $stmt->fetchColumn();

if ($vloga !== "admin") {
    http_response_code(403);
    exit("Nimaš dovoljenja za dostop do administratorske plošče.");
}

// Pridobimo vse uporabnike in njihovo statistiko
$sql = "SELECT
            u.id_uporabnik,
            u.uporabnisko_ime,
            u.email,
            u.vloga,
            (SELECT COUNT(*)
             FROM vaja v
             WHERE v.id_uporabnik = u.id_uporabnik) AS st_vaj,
            (SELECT COUNT(*)
             FROM zapis_vaje z
             WHERE z.id_uporabnik = u.id_uporabnik) AS st_treningov
        FROM uporabnik u
        ORDER BY u.id_uporabnik";

$uporabniki = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);


$steviloUporabnikov = count($uporabniki);

$steviloVaj = array_sum(
    array_column($uporabniki, "st_vaj")
);

$steviloTreningov = array_sum(
    array_column($uporabniki, "st_treningov")
);

?>



<!DOCTYPE html>
<html lang="sl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administracija | Fitnes zapisnik</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<main class="container mt-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Administratorska plošča</h1>

        <a href="index.php" class="btn btn-outline-primary">
            Nazaj na zapisnik
        </a>
    </div>


    <div class="row g-3 mb-4">

        <div class="col-md-4">
            <div class="card p-3">
                <h5>Uporabniki</h5>
                <h2><?= $steviloUporabnikov ?></h2>
            </div>
        </div>
        

        <div class="col-md-4">
            <div class="card p-3">
                <h5>Vaje</h5>
                <h2><?= $steviloVaj ?></h2>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card p-3">
                <h5>Treningi</h5>
                <h2><?= $steviloTreningov ?></h2>
            </div>
        </div>

    </div>


    <h2 class="mb-3">Registrirani uporabniki</h2>

    <div class="table-responsive">
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Uporabniško ime</th>
                    <th>E-pošta</th>
                    <th>Vloga</th>
                    <th>Število vaj</th>
                    <th>Število treningov</th>
                    <th>Dejanje</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($uporabniki as $uporabnik): ?>
                    <tr>
                        <td><?= (int)$uporabnik["id_uporabnik"] ?></td>
                        <td><?= htmlspecialchars($uporabnik["uporabnisko_ime"]) ?></td>
                        <td><?= htmlspecialchars($uporabnik["email"]) ?></td>
                        <td><?= htmlspecialchars($uporabnik["vloga"]) ?></td>
                        <td><?= (int)$uporabnik["st_vaj"] ?></td>
                        <td><?= (int)$uporabnik["st_treningov"] ?></td>

                        <td>
                            <a href="admin-uporabnik.php?id=<?= (int)$uporabnik['id_uporabnik'] ?>"
                            class="btn btn-outline-primary btn-sm">
                                Podrobnosti
                            </a>
                        </td>

                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</main>

</body>
</html>
