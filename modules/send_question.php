<?php

/**
 * Ce script reçoit les données du formulaire de question via AJAX,
 * les valide et les insère dans la base de données.
 * Il renvoie une réponse au format JSON pour indiquer le succès ou l'échec.
 */

// On inclut le fichier de connexion à la base de données
// Assurez-vous que le chemin vers db.php est correct.
include_once 'db.php';

// On définit le type de contenu de la réponse comme JSON
header('Content-Type: application/json');

// On vérifie que la méthode de requête est bien POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée.']);
    exit;
}

// anti-spam honeypot check
if (!empty($_POST['contact_email'])) {
    // On fait semblant que tout s'est bien passé
    echo json_encode(['success' => true, 'message' => 'Votre question a bien été envoyée !']);
    // On arrête le script ici pour ne pas polluer la BDD.
    exit;
}

// --- Récupération et validation des données ---
$niveau = $_POST['choice'] ?? null;
$departement = $_POST['depart'] ?? null;
$question = $_POST['ask'] ?? null;
// L'id_capsule sera envoyé par le JavaScript
$id_capsule = $_POST['id_capsule'] ?? 1; // Mettre une valeur par défaut si besoin

// Validation simple : on vérifie que les champs ne sont pas vides
if (empty($niveau) || empty($departement) || empty($question) || empty($id_capsule)) {
    echo json_encode(['success' => false, 'message' => 'Tous les champs sont obligatoires.']);
    exit;
}

// --- Insertion dans la base de données ---
try {
    // La requête SQL pour insérer les données.
    // NOTE : J'ai ajouté les colonnes `niveau` et `departement` qui manquaient dans votre SELECT.
    $sql = "INSERT INTO questions (id_capsule, niveau, departement, texte_question) VALUES (:id_capsule, :niveau, :departement, :question)";

    // On prépare la requête pour éviter les injections SQL
    $stmt = $pdo->prepare($sql);

    // On lie les valeurs aux paramètres de la requête
    $stmt->execute([
        ':id_capsule' => $id_capsule,
        ':niveau' => $niveau,
        ':departement' => $departement,
        ':question' => $question
    ]);

    // Si tout s'est bien passé, on renvoie une réponse de succès
    echo json_encode(['success' => true, 'message' => 'Votre question a bien été envoyée !']);
} catch (PDOException $e) {
    // En cas d'erreur avec la base de données, on renvoie un message d'erreur
    error_log('Erreur BDD: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Une erreur est survenue lors de l\'envoi. Veuillez réessayer.']);
}
