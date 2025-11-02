<?php
// On force l'affichage de toutes les erreurs PHP pour le débogage.
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Démarrage de la session pour pouvoir stocker les informations de l'utilisateur
session_start();

// Vérifier que la méthode de requête est POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // Si ce n'est pas le cas, on redirige l'utilisateur
    header('Location: ../login.php');
    exit();
}

// 1. VALIDATION DES DONNÉES
// Vérifier que les champs email et mot de passe ne sont pas vides
if (empty($_POST['email']) || empty($_POST['password'])) {
    header('Location: ../login.php?error=missing_fields');
    exit();
}

// Nettoyer l'email
$email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
$password = $_POST['password'];

// 2. VÉRIFICATION DANS LA BASE DE DONNÉES
try {
    // Inclusion du fichier de connexion à la BDD
    include_once 'db.php';

    // Préparer la requête pour trouver l'utilisateur par son email
    // On se base sur la structure de la table `Users` (`user` pour l'email, `pass` pour le mot de passe)
    $stmt = $pdo->prepare("SELECT id, pass, role FROM Users WHERE user = ?");
    $stmt->execute([$email]);

    // Récupérer l'utilisateur
    $user = $stmt->fetch();

    // 3. VÉRIFICATION DU MOT DE PASSE ET GESTION DE LA SESSION
    // Si un utilisateur a été trouvé ET que le mot de passe correspond au hash stocké
    if ($user && password_verify($password, $user['pass'])) {

        // Le mot de passe est correct, on démarre la session
        session_regenerate_id(true); // Sécurité : régénère l'ID de session

        // Stocker les informations de l'utilisateur dans la session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_role'] = $user['role'];

        // Rediriger vers le tableau de bord
        header('Location: ../dashboard.php');
        exit();
    } else {
        // L'utilisateur n'a pas été trouvé ou le mot de passe est incorrect
        header('Location: ../login.php?error=invalid_credentials');
        exit();
    }
} catch (PDOException $e) {
    // En cas d'erreur de connexion à la base de données
    error_log("DB Error in login_process.php: " . $e->getMessage());
    header('Location: ../login.php?error=db_error');
    exit();
}
