<?php
// Démarrage de la session pour la vérification des droits
session_start();

// SÉCURITÉ : Vérifier que l'utilisateur est connecté
// Pour cette action, un simple utilisateur connecté suffit, pas besoin d'être admin.
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
// Vérifier que l'ID de la capsule à supprimer est bien présent
if (empty($_POST['id_capsule'])) {
    header('Location: ../dashboard.php?error=missing_id');
    exit();
}

$capsule_to_delete_id = $_POST['id_capsule'];

// 3. SUPPRESSION DANS LA BASE DE DONNÉES
try {
    // Inclusion du fichier de connexion à la BDD
    include_once 'db.php';

    // Préparer la requête de suppression pour éviter les injections SQL
    $stmt = $pdo->prepare("DELETE FROM capsules WHERE id = ?");
    
    // Exécuter la requête en liant l'ID de la capsule
    $stmt->execute([$capsule_to_delete_id]);

    // Redirection vers la page du tableau de bord avec un message de succès
    header('Location: ../dashboard.php?success=capsule_deleted');
    exit();

} catch (PDOException $e) {
    // En cas d'erreur de base de données
    error_log("DB Error in delete_capsule.php: " . $e->getMessage());
    header('Location: ../dashboard.php?error=db_error');
    exit();
}
?>