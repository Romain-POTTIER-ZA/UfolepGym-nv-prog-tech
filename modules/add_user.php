<?php
// Démarrage de la session pour la vérification des droits
session_start();

// SÉCURITÉ : Vérifier que l'utilisateur est un administrateur connecté
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    // Si l'utilisateur n'est pas un admin, on interdit l'accès
    http_response_code(403); // Forbidden
    die('Accès refusé. Vous devez être administrateur pour effectuer cette action.');
}

// Vérifier que la méthode de requête est POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // Rediriger si le script est accédé directement par l'URL
    header('Location: ../admin.php');
    exit();
}

// 1. VALIDATION DES DONNÉES
// Vérifier que les champs nécessaires ne sont pas vides
if (empty($_POST['email']) || empty($_POST['password']) || empty($_POST['role'])) {
    // Rediriger avec un message d'erreur si des champs sont manquants
    header('Location: ../admin.php?error=missing_fields');
    exit();
}

// Nettoyer et valider l'email
$email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: ../admin.php?error=invalid_email');
    exit();
}

// Valider le rôle
$role = $_POST['role'];
if ($role !== 'user' && $role !== 'admin') {
    header('Location: ../admin.php?error=invalid_role');
    exit();
}

// 2. HACHAGE DU MOT DE PASSE
// Utiliser l'algorithme de hachage moderne et sécurisé de PHP
$hashed_password = password_hash($_POST['password'], PASSWORD_DEFAULT);

// 3. INSERTION DANS LA BASE DE DONNÉES
try {
    // Inclusion du fichier de connexion à la BDD
    include_once 'db.php';

    // CORRECTION : La requête doit correspondre à la structure de votre table SQL.
    // Votre SQL est : INSERT INTO `Users`(`id`, `user`, `pass`, `date`, `role`)
    // On suppose que `id` est auto-incrémenté. On fournit donc `user`, `pass`, `date`, et `role`.
    $stmt = $pdo->prepare("INSERT INTO Users (user, pass, date, role) VALUES (?, ?, ?, ?)");

    // Créer la date actuelle au format SQL DATETIME (ex: 2025-10-18 11:16:00)
    $current_date = date('Y-m-d H:i:s');

    // Exécuter la requête en liant les 4 paramètres
    $stmt->execute([$email, $hashed_password, $current_date, $role]);

    // Redirection vers la page de gestion avec un message de succès
    header('Location: ../admin.php?success=user_added');
    exit();
} catch (PDOException $e) {
    // Gérer les erreurs, par exemple si l'email existe déjà (contrainte UNIQUE)
    // Le code '23000' est le code SQLSTATE pour une violation de contrainte d'intégrité
    if ($e->getCode() == '23000') {
        header('Location: ../admin.php?error=email_exists');
    } else {
        // Pour les autres erreurs de base de données
        error_log("DB Error in add_user.php: " . $e->getMessage()); // Logger l'erreur réelle pour le débogage
        header('Location: ../admin.php?error=db_error');
    }
    exit();
}