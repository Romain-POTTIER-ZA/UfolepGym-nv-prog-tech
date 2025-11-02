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


if (empty($_POST['nom']) || empty($_POST['lien_youtube'])) {
    header('Location: ../dashboard.php?error=missing_fields');
    exit();
}


$nom = htmlspecialchars(trim($_POST['nom']));
$lien_youtube = filter_var(trim($_POST['lien_youtube']), FILTER_SANITIZE_URL);


if (!filter_var($lien_youtube, FILTER_VALIDATE_URL)) {
    header('Location: ../dashboard.php?error=invalid_url');
    exit();
}


try {
    
    include_once 'db.php';

    
    $stmt = $pdo->prepare("INSERT INTO capsules (nom, lien_youtube) VALUES (?, ?)");

    
    $stmt->execute([$nom, $lien_youtube]);

    
    header('Location: ../dashboard.php?success=capsule_added');
    exit();

} catch (PDOException $e) {
    
    if ($e->getCode() == '23000') {
        header('Location: ../dashboard.php?error=name_exists');
    } else {
        
        error_log("DB Error in add_capsule.php: " . $e->getMessage());
        header('Location: ../dashboard.php?error=db_error');
    }
    exit();
}
?>