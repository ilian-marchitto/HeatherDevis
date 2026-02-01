<?php

/**
 * Charge les variables d'un fichier .env dans l'environnement PHP
 */
function loadEnv($path) {
    if (!file_exists($path)) {
        return false;
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        // Ignorer les commentaires
        if (strpos(trim($line), '#') === 0) continue;

        // Séparer Clé=Valeur
        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);

        // Définir la variable d'environnement
        if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
            putenv(sprintf('%s=%s', $name, $value));
            $_ENV[$name] = $value;
            $_SERVER[$name] = $value;
        }
    }
}

// Appel de la fonction (le chemin remonte d'un cran depuis /config)
loadEnv(__DIR__ . '/../.env');

// Définir les variables d'environnement en local
$host = getenv('DB_HOST');
$port = getenv('DB_PORT');
$db   = getenv('DB_NAME');
$user = getenv('DB_USER');
$pwd  = getenv('DB_PASS');

try {
    $dsn = "mysql:host=$host; port=$port; dbname=$db";

    // Options de configuration PDO
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Lance des erreurs SQL
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Retourne des tableaux propres
        PDO::ATTR_EMULATE_PREPARES   => false,                  // Utilise les vraies requêtes préparées
    ];

    $connection = new PDO($dsn, $user, $pwd, $options);

} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
?>