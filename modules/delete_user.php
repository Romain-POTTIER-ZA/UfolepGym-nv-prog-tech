<?php

session_start();


if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
  
    http_response_code(403); 
    die('Accès refusé. Vous devez être administrateur pour effectuer cette action.');
}


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    
    header('Location: ../admin.php');
    exit();
}



if (empty($_POST['id_user'])) {
    header('Location: ../admin.php?error=missing_id');
    exit();
}

$user_to_delete_id = $_POST['id_user'];
$admin_id = $_SESSION['user_id'];


if ($user_to_delete_id == $admin_id) {
    header('Location: ../admin.php?error=self_delete');
    exit();
}


try {
    
    include_once 'db.php';

    
    $stmt = $pdo->prepare("DELETE FROM Users WHERE id = ?");
    
   
    $stmt->execute([$user_to_delete_id]);

   
    header('Location: ../admin.php?success=user_deleted');
    exit();

} catch (PDOException $e) {
    
    error_log("DB Error in delete_user.php: " . $e->getMessage());
    header('Location: ../admin.php?error=db_error');
    exit();
}
?>