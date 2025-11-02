<?php



session_start();


$error_message = '';
if (isset($_GET['error'])) {
    if ($_GET['error'] === 'invalid_credentials') {
        $error_message = 'Identifiant ou mot de passe incorrect.';
    } elseif ($_GET['error'] === 'missing_fields') {
        $error_message = 'Veuillez remplir tous les champs.';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/style.css">
    <title>Connexion - UFOLEP Gym</title>
</head>

<body>
    <nav>
        <div class="col">
            <img src="img/_logo_UFOLEP_Gym_Trampo.jpg" class="logo" alt="Logo UFOLEP Gym">
            <h3 style="color:white;text-align:center;">Nouveau Programme Technique</h3>
        </div>

        <div id="menu-toggle" class="menu-icon">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </nav>

    
    <main>
        <div class="container">

            
            <form action="modules/login_process.php" method="POST" id="login-form">
                <h2>Connexion à votre espace</h2>
                <br>

               
                <?php if (!empty($error_message)): ?>
                <div id="form-feedback" class="form-message error-message">
                    <?= htmlspecialchars($error_message) ?>
                </div>
                <?php endif; ?>

                <div class="form-group">
                    <label for="email">Adresse e-mail :</label>
                    <input type="email" id="email" name="email" required placeholder="exemple@domaine.com">
                </div>

                <div class="form-group">
                    <label for="password">Mot de passe :</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <button type="submit">Se connecter</button>
            </form>

        </div>
    </main>

   
    <script src="assets/js/main.js"></script>
</body>

</html>