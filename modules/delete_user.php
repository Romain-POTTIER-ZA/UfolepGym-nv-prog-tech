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
// Vérifier que l'ID de l'utilisateur à supprimer est bien présent
if (empty($_POST['id_user'])) {
    header('Location: ../admin.php?error=missing_id');
    exit();
}

$user_to_delete_id = $_POST['id_user'];
$admin_id = $_SESSION['user_id'];

// 2. SÉCURITÉ : Empêcher un administrateur de se supprimer lui-même
if ($user_to_delete_id == $admin_id) {
    header('Location: ../admin.php?error=self_delete');
    exit();
}

// 3. SUPPRESSION DANS LA BASE DE DONNÉES
try {
    // Inclusion du fichier de connexion à la BDD
    include_once 'db.php';

    // Préparer la requête de suppression pour éviter les injections SQL
    $stmt = $pdo->prepare("DELETE FROM Users WHERE id = ?");
    
    // Exécuter la requête en liant l'ID de l'utilisateur
    $stmt->execute([$user_to_delete_id]);

    // Redirection vers la page de gestion avec un message de succès
    header('Location: ../admin.php?success=user_deleted');
    exit();

} catch (PDOException $e) {
    // En cas d'erreur de base de données
    error_log("DB Error in delete_user.php: " . $e->getMessage());
    header('Location: ../admin.php?error=db_error');
    exit();
}
?>