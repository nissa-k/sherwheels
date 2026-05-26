<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/config/db.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Sauvegarde panier avant destruction session
if (!empty($_SESSION['user_id']) && !empty($_SESSION['cart'])) {

    $pdo = db();

    $stmt = $pdo->prepare("
        INSERT INTO paniers (
            user_id,
            cart_data
        )
        VALUES (?, ?)

        ON DUPLICATE KEY UPDATE
            cart_data = VALUES(cart_data),
            updated_at = NOW()
    ");

    $stmt->execute([
        $_SESSION['user_id'],
        json_encode($_SESSION['cart'])
    ]);
}

// Nettoyage session
$_SESSION = [];

if (ini_get('session.use_cookies')) {

    $params = session_get_cookie_params();

    setcookie(
        session_name(),
        '',
        [
            'expires'  => time() - 42000,
            'path'     => $params['path'],
            'domain'   => $params['domain'],
            'secure'   => $params['secure'],
            'httponly' => $params['httponly'],
            'samesite' => $params['samesite'] ?? 'Strict',
        ]
    );
}

session_destroy();

header('Location: /index.php');
exit;