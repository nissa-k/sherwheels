<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/config/db.php';

$pdo = db();

$error = null;
$success = null;

// Token reçu dans l'URL
$token = $_GET['token'] ?? '';

// Vérification format token
if (empty($token) || !ctype_xdigit($token)) {

    http_response_code(400);

    exit("Token invalide");
}

// Hash du token pour comparer avec la BDD
$tokenHash = hash('sha256', $token);

// Vérification token en BDD
$stmt = $pdo->prepare("
    SELECT id
    FROM users
    WHERE token_reset = ?
    AND token_expire > NOW()
    LIMIT 1
");

$stmt->execute([$tokenHash]);

$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Token invalide
if (!$user) {

    http_response_code(400);

    exit("Token invalide ou expiré");
}

// Formulaire soumis
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    verify_csrf();

    $password = $_POST['password'] ?? '';

    $password_confirm = $_POST['password_confirm'] ?? '';

    // Vérification force mot de passe
    if (
        !preg_match(
            '/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d).{8,}$/',
            $password
        )
    ) {

        $error =
            "Mot de passe trop faible (majuscule, minuscule, chiffre, 8+ caractères)";

    }
    // Vérification confirmation
    elseif ($password !== $password_confirm) {

        $error =
            "Les mots de passe ne correspondent pas";

    }
    else {

        // Hash mot de passe
        $hash = password_hash(
            $password,
            PASSWORD_DEFAULT
        );

        // Mise à jour utilisateur
        $stmt = $pdo->prepare("
            UPDATE users
            SET password = ?,
                token_reset = NULL,
                token_expire = NULL
            WHERE id = ?
        ");

        $stmt->execute([
            $hash,
            $user['id']
        ]);

        $success =
            "Mot de passe modifié avec succès";

        session_regenerate_id(true);
    }
}

$pageTitle = "Réinitialisation mot de passe";
include __DIR__ . '/includes/header.php';
?>

<section class="container">
<section class="connexion">

<h1>
    Réinitialiser le mot de passe
</h1>

<?php if ($error): ?>

<p>
    <?= e($error) ?>
</p>

<?php endif; ?>

<?php if ($success): ?>

<p>
    <?= e($success) ?>
</p>

<?php else: ?>

<form method="POST">

    <input
        type="hidden"
        name="csrf"
        value="<?= csrf_token() ?>"
    >

    <label>
        Nouveau mot de passe
    </label>

    <input
        type="password"
        name="password"
        required
    >

    <label>
        Confirmer mot de passe
    </label>

    <input
        type="password"
        name="password_confirm"
        required
    >

    <button type="submit">
        Modifier
    </button>

</form>

<?php endif; ?>

</section>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>