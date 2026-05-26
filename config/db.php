<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';

function db(): PDO {

    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $dsn = sprintf(
        'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
        DB_HOST,
        DB_PORT,
        DB_NAME
    );

    try {
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_PERSISTENT => false,
            PDO::ATTR_TIMEOUT => 5,
        ]);

        $pdo->exec("SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci");

    } catch (PDOException $e) {

        if (defined('APP_ENV') && APP_ENV === 'dev') {
            die("Erreur DB : " . $e->getMessage());
        }

        error_log($e->getMessage());

        http_response_code(500);

        exit('Erreur interne serveur');
    }

    return $pdo;
}