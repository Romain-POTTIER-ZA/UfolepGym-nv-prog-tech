<?php

session_destroy();

// Rediriger vers la page de connexion après la déconnexion
header('Location: ../login.php?message=logged_out');
exit();