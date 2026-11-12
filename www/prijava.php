
<!DOCTYPE html>
<html lang="sl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prijava | Fitnes zapisnik</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<main class="container mt-5" style="max-width: 500px;">

    <h2 class="mb-4">Prijava</h2>

    <?php if (isset($_GET["napaka"])): ?>
    <div class="alert alert-danger" role="alert">
        Napačna e-pošta ali geslo.
    </div>
<?php endif; ?>

    <form action="preveri-prijavo.php" method="POST">

        <div class="mb-3">
            <label for="email" class="form-label">E-pošta</label>
            <input type="email"
                   id="email"
                   name="email"
                   class="form-control"
                   autocomplete="email"
                   required>
        </div>

        <div class="mb-3">
            <label for="geslo" class="form-label">Geslo</label>
            <input type="password"
                   id="geslo"
                   name="geslo"
                   class="form-control"
                   autocomplete="current-password"
                   required>
        </div>

        <button type="submit" class="btn btn-primary">
            Prijavi se
        </button>

    </form>

    <p class="mt-3">
        Še nimaš računa?
        <a href="registracija.php">Registriraj se</a>
    </p>

</main>

</body>
</html>
