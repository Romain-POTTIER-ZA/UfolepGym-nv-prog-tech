<?php
// Démarrage de la session pour accéder aux variables de session
session_start();

// VÉRIFICATION DE CONNEXION
// Si la variable de session 'user_id' (ou tout autre nom que vous utilisez) n'est pas définie,
// cela signifie que l'utilisateur n'est pas connecté.
if (!isset($_SESSION['user_id'])) {
    // Rediriger vers la page de connexion
    header('Location: login.php');
    // S'assurer que le script s'arrête ici pour ne pas exécuter le reste de la page
    exit();
}

// On force l'affichage de toutes les erreurs PHP pour le débogage.
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$pdo = null;
$capsules = [];
$db_error = null;

try {
    // Inclusion du fichier de connexion à la base de données
    include_once 'modules/db.php';

    if (!isset($pdo) || $pdo === null) {
        throw new Exception("L'objet de connexion PDO n'a pas été initialisé.");
    }

    // Récupération des capsules existantes
    $stmt = $pdo->query("SELECT id, nom, lien_youtube FROM capsules ORDER BY nom ASC");
    $capsules = $stmt->fetchAll();
} catch (Throwable $e) {
    $db_error = "Erreur critique : Impossible de charger les données. (" . $e->getMessage() . ")";
    error_log($e);
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/style.css">
    <title>Tableau de Bord - UFOLEP Gym</title>
    <style>
    /* Styles pour la mise en page du tableau de bord */
    .dashboard-container {
        display: flex;
        flex-wrap: wrap;
        /* Permet aux colonnes de passer à la ligne sur petits écrans */
        gap: 2rem;
        /* Espace entre les colonnes */
        padding: 1rem;
    }

    .dashboard-col {
        flex: 1;
        /* Chaque colonne prend une part égale de l'espace */
        min-width: 300px;
        /* Largeur minimale avant de passer à la ligne */
        background-color: #f9f9f9;
        padding: 1.5rem;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .capsule-list ul {
        list-style-type: none;
        padding: 0;
    }

    .capsule-list li {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.8rem;
        border-bottom: 1px solid #ddd;
    }

    .capsule-list li:last-child {
        border-bottom: none;
    }

    .capsule-info {
        display: flex;
        flex-direction: column;
    }

    .capsule-info .nom {
        font-weight: bold;
    }

    .capsule-info .lien {
        font-size: 0.8em;
        color: #555;
    }

    .delete-btn {
        background-color: #e74c3c;
        color: white;
        border: none;
        padding: 5px 10px;
        border-radius: 5px;
        cursor: pointer;
        font-size: 0.9em;
    }

    .delete-btn:hover {
        background-color: #c0392b;
    }
    </style>
</head>

<body>
    <nav>
        <div class="col">
            <img src="./img/_logo_UFOLEP_Gym_Trampo.jpg" class="logo" alt="Logo UFOLEP Gym">
            <h3 style="color:white;text-align:center;">Gestion du Programme</h3>
        </div>
        <!-- Ici, vous pourriez ajouter un lien de déconnexion -->
        <div id="menu-toggle" class="menu-icon">
            <span></span>
            <span></span>
            <span></span>
        </div>

    </nav>
    <aside>
        <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
        <a href="/admin.php" style="color: black; padding: 1rem;">Utilisateurs</a>
        <?php endif; ?>

        <a href="modules/logout.php" style="color: black; padding: 1rem;">Déconnexion</a>
    </aside>
    <main>
        <div class="container">
            <h1>Tableau de Bord</h1>

            <div class="dashboard-container">
                <!-- Colonne 1: Liste des données -->
                <div class="dashboard-col capsule-list">
                    <h2>Liste des Capsules Vidéo</h2>
                    <?php if (isset($db_error)): ?>
                    <p style="color: red;"><?= htmlspecialchars($db_error) ?></p>
                    <?php else: ?>
                    <ul>
                        <?php if (!empty($capsules)): ?>
                        <?php foreach ($capsules as $capsule): ?>
                        <li>
                            <div class="capsule-info">
                                <span class="nom"><?= htmlspecialchars($capsule['nom']) ?></span>
                                <span class="lien"><?= htmlspecialchars($capsule['lien_youtube']) ?></span>
                            </div>
                            <!-- Formulaire pour la suppression d'une capsule -->
                            <form action="modules/delete_capsule.php" method="POST"
                                onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette capsule ?');">
                                <input type="hidden" name="id_capsule" value="<?= htmlspecialchars($capsule['id']) ?>">
                                <button type="submit" class="delete-btn">Supprimer</button>
                            </form>
                        </li>
                        <?php endforeach; ?>
                        <?php else: ?>
                        <li>Aucune capsule vidéo trouvée.</li>
                        <?php endif; ?>
                    </ul>
                    <?php endif; ?>
                </div>

                <!-- Colonne 2: Formulaire pour ajouter des données -->
                <div class="dashboard-col">
                    <h2>Ajouter une Nouvelle Capsule</h2>
                    <form action="modules/add_capsule.php" method="POST" id="add-capsule-form">
                        <div class="form-group">
                            <label for="nom">Nom de la capsule :</label>
                            <input type="text" id="nom" name="nom" required placeholder="Ex: Mouvement au sol">
                        </div>
                        <div class="form-group">
                            <label for="lien_youtube">Lien YouTube :</label>
                            <input type="url" id="lien_youtube" name="lien_youtube" required
                                placeholder="https://www.youtube.com/watch?v=...">
                        </div>
                        <button type="submit">Ajouter la capsule</button>
                    </form>
                </div>
            </div>

        </div>
    </main>
    <script src="assets/js/main.js"></script>
</body>

</html>