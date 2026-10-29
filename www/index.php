<?php
require_once "db.php";

// Pridobimo vse vaje iz podatkovne baze
$stmtVaje = $pdo->query("SELECT * FROM vaja ORDER BY ime_vaje");
$vaje = $stmtVaje->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="sl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fitnes zapisnik</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
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


        <?php if (isset($_GET["dodano"])): ?>
            <div class="alert alert-success" role="alert">
                Nova vaja je bila uspešno dodana!
            </div>
        <?php endif; ?>



        <section>
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2>Moje vaje</h2>

                <button class="btn btn-primary"
                        type="button"
                        data-bs-toggle="collapse"
                        data-bs-target="#dodajVajo">
                    + Dodaj vajo
                </button>
            </div>

            
            <div class="collapse mb-4" id="dodajVajo">
                <div class="card card-body">

                    <h4>Ustvari novo vajo</h4>

                    <form action="dodaj-vajo.php" method="POST">

                        <div class="mb-3">
                            <label for="ime_vaje" class="form-label">
                                Ime vaje
                            </label>
                            <input type="text"
                                class="form-control"
                                id="ime_vaje"
                                name="ime_vaje"
                                placeholder="Npr. Squat"
                                maxlength="100"
                                required>
                        </div>

                        <div class="mb-3">
                            <label for="misicna_skupina" class="form-label">
                                Mišična skupina
                            </label>

                            <select class="form-select"
                                    id="misicna_skupina"
                                    name="misicna_skupina"
                                    required>
                                <option value="">Izberi mišično skupino</option>
                                <option value="Prsa">Prsa</option>
                                <option value="Hrbet">Hrbet</option>
                                <option value="Noge">Noge</option>
                                <option value="Ramena">Ramena</option>
                                <option value="Roke">Roke</option>
                                <option value="Trebušne mišice">Trebušne mišice</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="opis_vaje" class="form-label">
                                Opis (neobvezno)
                            </label>
                            <textarea class="form-control"
                                    id="opis_vaje"
                                    name="opis_vaje"
                                    rows="3"
                                    placeholder="Kratek opis vaje"></textarea>
                        </div>

                        <button type="submit" class="btn btn-success">
                            Ustvari vajo
                        </button>

                    </form>
                </div>
            </div>

            
<div class="accordion mb-4" id="dinamicneVaje">

    <?php foreach ($vaje as $vaja): ?>

        <?php
$stmtZapisi = $pdo->prepare(
    "SELECT datum, teza_kg, opis
     FROM zapis_vaje
     WHERE id_uporabnik = ? AND id_vaja = ?
     ORDER BY datum DESC, id_zapis DESC"
);

$stmtZapisi->execute([1, $vaja["id_vaja"]]);

$zgodovina = $stmtZapisi->fetchAll(PDO::FETCH_ASSOC);


$grafZgodovina = array_reverse($zgodovina);

$grafDatumi = array_column($grafZgodovina, "datum");

$grafTeze = array_map(
    "floatval",
    array_column($grafZgodovina, "teza_kg")
);

?>

        <div class="accordion-item">

            <h3 class="accordion-header">
                <button class="accordion-button collapsed"
                        type="button"
                        data-bs-toggle="collapse"
                        data-bs-target="#vaja<?= (int)$vaja['id_vaja'] ?>">

                    <?= htmlspecialchars($vaja["ime_vaje"]) ?>

                </button>
            </h3>

            <div id="vaja<?= (int)$vaja['id_vaja'] ?>"
                 class="accordion-collapse collapse"
                 data-bs-parent="#dinamicneVaje">

                
<div class="accordion-body">

    <p>
        <strong>Mišična skupina:</strong>
        <?= htmlspecialchars($vaja["misicna_skupina"]) ?>
    </p>

    <p>
        <?= htmlspecialchars($vaja["opis_vaje"] ?? "") ?>
    </p>

    <h5 class="mt-4">Dodaj trening</h5>

    <form action="shrani-vajo.php" method="POST">

        <input type="hidden"
               name="id_vaja"
               value="<?= (int)$vaja['id_vaja'] ?>">

        <div class="mb-3">
            <label class="form-label">Datum treninga</label>
            <input type="date"
                   name="datum"
                   class="form-control"
                   required>
        </div>

        <div class="mb-3">
            <label class="form-label">Teža (kg)</label>
            <input type="number"
                   name="teza"
                   class="form-control"
                   min="0"
                   step="0.5"
                   required>
        </div>

        <div class="mb-3">
            <label class="form-label">Opis (neobvezno)</label>
            <textarea name="opis"
                      class="form-control"
                      rows="3"></textarea>
        </div>

        <button type="submit" class="btn btn-primary">
            Shrani trening
        </button>

    </form>

    
<h5 class="mt-4">Zgodovina treningov</h5>

<?php if (empty($zgodovina)): ?>

    <p class="text-muted">
        Za to vajo še ni shranjenih treningov.
    </p>

<?php else: ?>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>Datum</th>
                <th>Teža (kg)</th>
                <th>Opis</th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($zgodovina as $zapis): ?>
                <tr>
                    <td><?= htmlspecialchars($zapis["datum"]) ?></td>
                    <td><?= htmlspecialchars($zapis["teza_kg"]) ?> kg</td>
                    <td><?= htmlspecialchars($zapis["opis"] ?? "") ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

<?php endif; ?>

<?php if (!empty($zgodovina)): ?>

    <h5 class="mt-4">Graf napredka</h5>

    <div class="card p-3 mb-4">
        <canvas id="grafVaja<?= (int)$vaja['id_vaja'] ?>"></canvas>
    </div>

    <script>
        new Chart(
            document.getElementById("grafVaja<?= (int)$vaja['id_vaja'] ?>"),
            {
                type: "line",
                data: {
                    labels: <?= json_encode($grafDatumi) ?>,
                    datasets: [{
                        label: "Teža (kg)",
                        data: <?= json_encode($grafTeze) ?>,
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
                            beginAtZero: false
                        }
                    }
                }
            }
        );
    </script>

<?php endif; ?>
</div>


            </div>

        </div>

    <?php endforeach; ?>

</div>

</section>
</main>

    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    
<script>
    const datumi = <?= json_encode($datumi, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
    const teze = <?= json_encode($teze) ?>;

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