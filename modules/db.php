<?php

/**
 * Fichier unique pour la configuration ET la connexion à la base de données.
 */

// --- 1. Paramètres de connexion ---
// Adresse du serveur de base de données (sans le port)
define('DB_HOST', 'pr642490-001.eu.clouddb.ovh.net');

// Port du serveur de base de données
define('DB_PORT', '35590');

// Nom de la base de données que vous avez créée dans phpMyAdmin
define('DB_NAME', 'UFOLEP-Gym');

// Nom d'utilisateur pour se connecter à la base de données
define('DB_USER', 'AdminUFO');

// Mot de passe de l'utilisateur
define('DB_PASS', 'KZqMxbz4emeu3Jn');

// Jeu de caractères à utiliser pour la connexion
define('DB_CHARSET', 'utf8mb4');


// --- 2. Logique de connexion ---

// On prépare le DSN (Data Source Name) pour PDO
$dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;

// Options pour PDO pour une connexion plus sûre et de meilleurs rapports d'erreurs
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    // On crée l'objet PDO qui représente la connexion à la base de données.
    // Cette variable $pdo sera disponible dans les scripts qui incluent ce fichier.
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch (\PDOException $e) {
    // En cas d'erreur de connexion, on arrête tout et on lève une exception.
    // Le bloc try/catch dans index.php pourra ainsi l'attraper et afficher une erreur propre.
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}

/**
 * Convertit n'importe quelle URL YouTube standard en une URL d'intégration (embed).
 * @param string $url L'URL YouTube à convertir (ex: /watch?v=... ou youtu.be/...)
 * @return string L'URL au format /embed/ prête à être utilisée dans un iframe.
 */
function convertirLienYoutubeEnEmbed($url) {
    // Regex pour extraire l'ID de la vidéo de différents formats d'URL YouTube
    preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $url, $matches);

    // Si un ID a été trouvé, on construit le lien d'intégration
    if (isset($matches[1])) {
        return 'https://www.youtube.com/embed/' . $matches[1];
    }
    
    // Si aucun ID n'est trouvé ou si c'est déjà un lien d'intégration, on retourne l'URL originale
    return $url;
}