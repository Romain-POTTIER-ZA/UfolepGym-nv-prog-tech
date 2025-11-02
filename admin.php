<?php

session_start();


if (!isset($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit();
}


if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    
    header('Location: dashboard.php');
    exit();
}




$pdo = null;
$users = [];
$db_error = null;

try {
   
    include_once 'modules/db.php';

    if (!isset($pdo) || $pdo === null) {
        throw new Exception("L'objet de connexion PDO n'a pas été initialisé.");
    }

    
    $stmt = $pdo->query("SELECT id, user, role FROM Users ORDER BY user ASC");
    $users = $stmt->fetchAll();
} catch (Throwable $e) {
    $db_error = "Erreur critique : Impossible de charger les utilisateurs. (" . $e->getMessage() . ")";
    error_log($e);
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./style/style.css">
    <title>Gestion Utilisateurs - UFOLEP Gym</title>
    <style>
   
    .dashboard-container {
        display: flex;
        flex-wrap: wrap;
        gap: 2rem;
        padding: 1rem;
    }

    .dashboard-col {
        flex: 1;
        min-width: 300px;
        background-color: #f9f9f9;
        padding: 1.5rem;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .user-list ul {
        list-style-type: none;
        padding: 0;
    }

    .user-list li {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.8rem;
        border-bottom: 1px solid #ddd;
    }

    .user-list li:last-child {
        border-bottom: none;
    }

    .user-info {
        display: flex;
        flex-direction: column;
    }

    .user-info .email {
        font-weight: bold;
    }

    .user-info .role {
        font-size: 0.8em;
        color: #555;
        text-transform: capitalize;
    }

    .delete-btn {
        background-color: #e74c3c;
        color: white;
        width: 150px;
        border: none;
        padding: 5px 5px;
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
            <img src="img/_logo_UFOLEP_Gym_Trampo.jpg" class="logo" alt="Logo UFOLEP Gym">
            <h3 style="color:white;text-align:center;">Administration</h3>
        </div>
        <div id="menu-toggle" class="menu-icon">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </nav>
    <aside>
        <a href="dashboard.php" style="color: black; padding: 1rem;">Tableau de Bord</a>
        <a href="modules/logout.php" style="color:black; padding: 1rem;">Déconnexion</a>
    </aside>
    <main>
        <div class="container">
            <h1>Gestion des Utilisateurs</h1>

            <div class="dashboard-container">
                
                <div class="dashboard-col user-list">
                    <h2>Utilisateurs Existants</h2>
                    <?php if (isset($db_error)): ?>
                    <p style="color: red;"><?= htmlspecialchars($db_error) ?></p>
                    <?php else: ?>
                    <ul>
                        <?php if (!empty($users)): ?>
                        <?php foreach ($users as $user): ?>
                        <li>
                            <div class="user-info">
                                <span class="email"><?= htmlspecialchars($user['user']) ?></span>
                                <span class="role">Rôle : <?= htmlspecialchars($user['role']) ?></span>
                            </div>
                            
                            <?php if ($_SESSION['user_id'] !== $user['id']): ?>
                            <form action="modules/delete_user.php" method="POST"
                                onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?');">
                                <input type="hidden" name="id_user" value="<?= htmlspecialchars($user['id']) ?>">
                                <button type="submit" class="delete-btn">Supprimer</button>
                            </form>
                            <?php endif; ?>
                        </li>
                        <?php endforeach; ?>
                        <?php else: ?>
                        <li>Aucun utilisateur trouvé.</li>
                        <?php endif; ?>
                    </ul>
                    <?php endif; ?>
                </div>

                
                <div class="dashboard-col">
                    <h2>Ajouter un Nouvel Utilisateur</h2>
                    <form action="modules/add_user.php" method="POST" id="add-user-form">
                        <div class="form-group">
                            <label for="email">Adresse e-mail :</label>
                            <input type="email" id="email" name="email" required placeholder="nom@exemple.com">
                        </div>
                        <div class="form-group">
                            <label for="password">Mot de passe :</label>
                            <input type="password" id="password" name="password" required>
                        </div>
                        <div class="form-group">
                            <label for="role">Rôle :</label>
                            <select name="role" id="role">
                                <option value="user">Utilisateur</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                        <button type="submit">Ajouter l'utilisateur</button>
                    </form>
                </div>
            </div>

        </div>
    </main>
    <script src="assets/js/main.js"></script>
</body>

</html>