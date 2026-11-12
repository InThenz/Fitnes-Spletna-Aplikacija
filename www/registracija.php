
<!DOCTYPE html>
<html lang="sl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registracija</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<main class="container mt-5" style="max-width: 500px;">

    <h2 class="mb-4">Registracija</h2>


<?php if (isset($_GET["uspeh"])): ?>
    <div class="alert alert-success">
        Registracija je bila uspešna!
    </div>
<?php endif; ?>

<?php if (($_GET["napaka"] ?? "") === "obstaja"): ?>
    <div class="alert alert-danger">
        Uporabniško ime ali e-pošta je že zasedena.
    </div>
<?php endif; ?>


    <form action="shrani-registracijo.php" method="POST">

        <div class="mb-3">
            <label class="form-label">Uporabniško ime</label>
            <input type="text" name="uporabnisko_ime"
                   class="form-control" maxlength="50" required>
        </div>

        <div class="mb-3">
            <label class="form-label">E-pošta</label>
            <input type="email" name="email"
                   class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Geslo</label>
            <input type="password" name="geslo"
                   class="form-control" minlength="8"
                   autocomplete="new-password" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Ponovi geslo</label>
            <input type="password" name="ponovi_geslo"
                   class="form-control" minlength="8"
                   autocomplete="new-password" required>
        </div>

        <button type="submit" class="btn btn-primary">
            Registriraj se
        </button>

    </form>

</main>

</body>
</html>
