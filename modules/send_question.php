<?php


include_once 'db.php';


header('Content-Type: application/json');


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée.']);
    exit;
}


if (!empty($_POST['contact_email'])) {
    
    echo json_encode(['success' => true, 'message' => 'Votre question a bien été envoyée !']);
    
    exit;
}


$niveau = $_POST['choice'] ?? null;
$departement = $_POST['depart'] ?? null;
$question = $_POST['ask'] ?? null;

$id_capsule = $_POST['id_capsule'] ?? 1; 


if (empty($niveau) || empty($departement) || empty($question) || empty($id_capsule)) {
    echo json_encode(['success' => false, 'message' => 'Tous les champs sont obligatoires.']);
    exit;
}


try {
  
    $sql = "INSERT INTO questions (id_capsule, niveau, departement, texte_question) VALUES (:id_capsule, :niveau, :departement, :question)";


    $stmt = $pdo->prepare($sql);


    $stmt->execute([
        ':id_capsule' => $id_capsule,
        ':niveau' => $niveau,
        ':departement' => $departement,
        ':question' => $question
    ]);

    echo json_encode(['success' => true, 'message' => 'Votre question a bien été envoyée !']);
} catch (PDOException $e) {
   
    error_log('Erreur BDD: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Une erreur est survenue lors de l\'envoi. Veuillez réessayer.']);
}
