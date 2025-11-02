<?php

session_start();


if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
   
    http_response_code(403); // Forbidden
    die('Accès refusé. Vous devez être administrateur pour effectuer cette action.');
}


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // Rediriger si le script est accédé directement par l'URL
    header('Location: ../admin.php');
    exit();
}


if (empty($_POST['email']) || empty($_POST['password']) || empty($_POST['role'])) {
    // Rediriger avec un message d'erreur si des champs sont manquants
    header('Location: ../admin.php?error=missing_fields');
    exit();
}


$email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: ../admin.php?error=invalid_email');
    exit();
}


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

    
    $stmt = $pdo->prepare("INSERT INTO Users (user, pass, date, role) VALUES (?, ?, ?, ?)");

    
    $current_date = date('Y-m-d H:i:s');

    
    $stmt->execute([$email, $hashed_password, $current_date, $role]);

    
    header('Location: ../admin.php?success=user_added');
    exit();
} catch (PDOException $e) {
   
    if ($e->getCode() == '23000') {
        header('Location: ../admin.php?error=email_exists');
    } else {
       
        error_log("DB Error in add_user.php: " . $e->getMessage()); // Logger l'erreur réelle pour le débogage
        header('Location: ../admin.php?error=db_error');
    }
    exit();

}
