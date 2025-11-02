<?php

define('DB_HOST', '***************');


define('DB_PORT', '***************');


define('DB_NAME', '***************');


define('DB_USER', '*****************');


define('DB_PASS', '***************');


define('DB_CHARSET', 'utf8mb4');



$dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch (\PDOException $e) {
  
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}

/**

 * @param string $
 * @return string 
 */
function convertirLienYoutubeEnEmbed($url) {
  
    preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $url, $matches);

    
    if (isset($matches[1])) {
        return 'https://www.youtube.com/embed/' . $matches[1];
    }
    
   
    return $url;

}
