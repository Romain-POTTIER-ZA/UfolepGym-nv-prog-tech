<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    
    header('Location: ../login.php');
    exit();
}


if (empty($_POST['email']) || empty($_POST['password'])) {
    header('Location: ../login.php?error=missing_fields');
    exit();
}


$email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
$password = $_POST['password'];


try {
    
    include_once 'db.php';

   
    $stmt = $pdo->prepare("SELECT id, pass, role FROM Users WHERE user = ?");
    $stmt->execute([$email]);

    
    $user = $stmt->fetch();

   
    if ($user && password_verify($password, $user['pass'])) {

        
        session_regenerate_id(true); 

        
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_role'] = $user['role'];

        
        header('Location: ../dashboard.php');
        exit();
    } else {
        
        header('Location: ../login.php?error=invalid_credentials');
        exit();
    }
} catch (PDOException $e) {
    
    error_log("DB Error in login_process.php: " . $e->getMessage());
    header('Location: ../login.php?error=db_error');
    exit();
}
