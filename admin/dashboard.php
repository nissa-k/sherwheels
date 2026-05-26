<?php
declare(strict_types=1);

require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../config/db.php';

$pdo = db();

$stmt = $pdo->prepare("SELECT email FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

if (!$user) {
    session_destroy();
    header("Location: /login.php");
    exit;
}
?>

<h1>Dashboard Admin</h1>

<p>Bienvenue <?= htmlspecialchars($user['email']) ?></p>

<ul>
<li><a href="users.php">Utilisateurs</a></li>
<li><a href="/logout.php">Déconnexion</a></li>
</ul>