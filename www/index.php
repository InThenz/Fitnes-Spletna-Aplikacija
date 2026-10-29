<?php
require_once "db.php";

$sql = "SELECT datum, teza_kg, opis
        FROM zapis_vaje
        WHERE id_uporabnik = 1 AND id_vaja = 1
        ORDER BY datum DESC, id_zapis DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute();

$zapisi = $stmt->fetchAll(PDO::FETCH_ASSOC);

$grafZapisi = array_reverse($zapisi);

$datumi = array_column($grafZapisi, "datum");

$teze = array_map(
    "floatval",
    array_column($grafZapisi, "teza_kg")
);
?>

<!DOCTYPE html>
<html lang="sl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fitnes zapisnik</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

    <header>
        <h1 class="text-primary text-center mt-4">ZAPISNIK</h1>
    </header>

    <main>

    <?php if (isset($_GET["shranjeno"])): ?>
    <div class="alert alert-success" role="alert">
        Trening je bil uspešno shranjen!
    </div>
<?php endif; ?>


        <section>
            <h2>Moje vaje</h2>
            <div class="accordion" id="seznamVaj">
                <div class="accordion-item">
                    <h3 class="accordion-header">
                        <button class="accordion-button collapsed"type="button"data-bs-toggle="collapse"data-bs-target="#benchPress">Bench Press</button>
                    </h3>

                    <div id="benchPress"
                        class="accordion-collapse collapse <?= isset($_GET['shranjeno']) ? 'show' : '' ?>"
                        data-bs-parent="#seznamVaj">
                        
                        <div class="accordion-body">
                            <form action="shrani-vajo.php" method="POST">
                                 <!-- Datum -->
                                <div class="mb-3">
                                    <label for="datum" class="form-label">Datum treninga</label>
                                    <input type="date" class="form-control" id="datum" name="datum" required>
                                </div>

                                <!-- Teža -->
                                <div class="mb-3">
                                    <label for="teza" class="form-label">Teža (kg)</label>
                                    <input type="number" class="form-control" id="teza" name="teza" min="0" step="0.5" placeholder="Npr. 80" required>
                                </div>

        <!-- Opis treninga -->
        <div class="mb-3">
            <label for="opis" class="form-label">Opis (neobvezno)</label>
            <textarea class="form-control" id="opis" name="opis"
                      rows="3" placeholder="Kako je potekala vaja?"></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Shrani zapis</button>
    </form>
    
<h4 class="mt-4">Zgodovina treningov</h4>

<table class="table table-striped">
    <thead>
        <tr>
            <th>Datum</th>
            <th>Teža (kg)</th>
            <th>Opis</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($zapisi as $zapis): ?>
            <tr>
                <td><?= htmlspecialchars($zapis["datum"]) ?></td>
                <td><?= htmlspecialchars($zapis["teza_kg"]) ?> kg</td>
                <td><?= htmlspecialchars($zapis["opis"] ?? "") ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<h4 class="mt-4">Graf napredka</h4>

<div class="card p-3 mb-4">
    <canvas id="grafNapredka"></canvas>
</div>


</div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    
<script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>

<script>
    const datumi = <?= json_encode($datumi, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
    const teze = <?= json_encode($teze) ?>;

    const ctx = document.getElementById("grafNapredka");

    new Chart(ctx, {
        type: "line",
        data: {
            labels: datumi,
            datasets: [{
                label: "Bench Press (kg)",
                data: teze,
                borderColor: "#0d6efd",
                backgroundColor: "rgba(13, 110, 253, 0.1)",
                borderWidth: 2,
                tension: 0.3,
                pointRadius: 5,
                fill: true
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: false,
                    title: {
                        display: true,
                        text: "Teža (kg)"
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: "Datum treninga"
                    }
                }
            }
        }
    });
</script>

</body>
</html>