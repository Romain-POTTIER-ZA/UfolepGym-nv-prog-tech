<?php

session_start();


if (!isset($_SESSION['user_id'])) {
    
    http_response_code(403); 
    die('Accès refusé. Vous devez être connecté pour effectuer cette action.');
}


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    
    header('Location: ../dashboard.php');
    exit();
}


if (empty($_POST['id_capsule'])) {
    header('Location: ../dashboard.php?error=missing_id');
    exit();
}

$capsule_to_delete_id = $_POST['id_capsule'];


try {
    
    include_once 'db.php';

    
    $stmt = $pdo->prepare("DELETE FROM capsules WHERE id = ?");
    
   
    $stmt->execute([$capsule_to_delete_id]);

    
    header('Location: ../dashboard.php?success=capsule_deleted');
    exit();

} catch (PDOException $e) {
   
    error_log("DB Error in delete_capsule.php: " . $e->getMessage());
    header('Location: ../dashboard.php?error=db_error');
    exit();
}
?>