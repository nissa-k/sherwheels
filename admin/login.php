<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../config/db.php';

$pdo = db();

$error = null;
$_SESSION['login_attempts'] = $_SESSION['login_attempts'] ?? 0;
$_SESSION['last_attempt'] = $_SESSION['last_attempt'] ?? time();

if ($_SESSION['login_attempts'] >= 5 && time() - $_SESSION['last_attempt'] < 300) {
    http_response_code(429);
    exit("Trop de tentatives. Réessayez dans 5 minutes.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    verify_csrf();

    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $email = $email ? strtolower(trim($email)) : null;

    $password = $_POST['password'] ?? '';

    if (!$email || empty($password)) {
        $error = "Champs invalides";
    } else {

        $stmt = $pdo->prepare("
            SELECT id, email, password_hash, role
            FROM users 
            WHERE email = ?
            LIMIT 1
        ");
        $stmt->execute([$email]);

        $user = $stmt->fetch();
        $fakeHash = '$2y$10$usesomesillystringfore7hnbRJHxXVLeakoG8K30oukPsA.ztMG';

        $hashToCheck = $user ? $user['password_hash'] : $fakeHash;

        $valid = password_verify($password, $hashToCheck);

        if ($valid && $user && $user['role'] === 'admin') {
            session_regenerate_id(true);

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['login_attempts'] = 0;

            header("Location: /admin/dashboard.php");
            exit;

        } else {
            $error = "Identifiants incorrects";

            $_SESSION['login_attempts']++;
            $_SESSION['last_attempt'] = time();
        }
    }
}