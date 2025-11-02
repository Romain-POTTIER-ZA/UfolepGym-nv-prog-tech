<?php
// Démarrage de la session pour la vérification des droits
session_start();

// SÉCURITÉ : Vérifier que l'utilisateur est connecté
// Pour cette action, un simple utilisateur connecté suffit.
if (!isset($_SESSION['user_id'])) {
    // Si l'utilisateur n'est pas connecté, on interdit l'accès
    http_response_code(403); // Forbidden
    die('Accès refusé. Vous devez être connecté pour effectuer cette action.');
}

// Vérifier que la méthode de requête est POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // Rediriger si le script est accédé directement par l'URL
    header('Location: ../dashboard.php');
    exit();
}

// 1. VALIDATION DES DONNÉES
// Vérifier que les champs nécessaires ne sont pas vides
if (empty($_POST['nom']) || empty($_POST['lien_youtube'])) {
    header('Location: ../dashboard.php?error=missing_fields');
    exit();
}

// Nettoyer les données
$nom = htmlspecialchars(trim($_POST['nom']));
$lien_youtube = filter_var(trim($_POST['lien_youtube']), FILTER_SANITIZE_URL);

// Valider que le lien est une URL valide
if (!filter_var($lien_youtube, FILTER_VALIDATE_URL)) {
    header('Location: ../dashboard.php?error=invalid_url');
    exit();
}

// 2. INSERTION DANS LA BASE DE DONNÉES
try {
    // Inclusion du fichier de connexion à la BDD
    include_once 'db.php';

    // Préparer la requête pour éviter les injections SQL
    $stmt = $pdo->prepare("INSERT INTO capsules (nom, lien_youtube) VALUES (?, ?)");

    // Exécuter la requête en liant les paramètres
    $stmt->execute([$nom, $lien_youtube]);

    // Redirection vers la page du tableau de bord avec un message de succès
    header('Location: ../dashboard.php?success=capsule_added');
    exit();

} catch (PDOException $e) {
    // Gérer les erreurs, par exemple si un nom de capsule doit être unique
    if ($e->getCode() == '23000') {
        header('Location: ../dashboard.php?error=name_exists');
    } else {
        // Pour les autres erreurs de base de données
        error_log("DB Error in add_capsule.php: " . $e->getMessage());
        header('Location: ../dashboard.php?error=db_error');
    }
    exit();
}
?>