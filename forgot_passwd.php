<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/mailer.php';

$pdo = db();

$message = '';

$_SESSION['reset_attempts'] =
    $_SESSION['reset_attempts'] ?? 0;

$_SESSION['last_reset'] =
    $_SESSION['last_reset'] ?? time();

// Anti spam
if (
    $_SESSION['reset_attempts'] >= 5 &&
    time() - $_SESSION['last_reset'] < 300
) {

    http_response_code(429);

    exit("Trop de demandes. Réessayez plus tard.");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    verify_csrf();

    $email = filter_input(
        INPUT_POST,
        'email',
        FILTER_VALIDATE_EMAIL
    );

    $email = $email
        ? strtolower(trim($email))
        : null;

    $message =
        "Si cet email existe, un lien de réinitialisation a été envoyé.";

    if ($email) {

        $stmt = $pdo->prepare("
            SELECT id, email
            FROM users
            WHERE email = ?
            LIMIT 1
        ");

        $stmt->execute([$email]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {

            // Génération token
            $token =
                bin2hex(random_bytes(32));

            // Hash pour BDD
            $tokenHash =
                hash('sha256', $token);

            // Sauvegarde BDD (expiration gérée par MySQL)
            $stmt = $pdo->prepare("
                UPDATE users
                SET token_reset = ?,
                    token_expire = DATE_ADD(NOW(), INTERVAL 1 HOUR)
                WHERE id = ?
            ");

            $stmt->execute([
                $tokenHash,
                $user['id']
            ]);

            // Lien reset
            $resetLink =
                $_ENV['APP_URL'] .
                '/reset_psswd.php?token=' .
                $token;

            // Envoi mail
            send_reset_email(
                $user['email'],
                $resetLink
            );
        }
    }

    $_SESSION['reset_attempts']++;

    $_SESSION['last_reset'] = time();
}
?>

<?php
$pageTitle = "Mot de passe oublié";

include __DIR__ . '/includes/header.php';
?>

<section class="container">
<section class="connexion">

<h1>
    Mot de passe oublié
</h1>

<form method="POST">

<input
    type="hidden"
    name="csrf"
    value="<?= csrf_token() ?>"
>

<label>Email</label>

<input
    type="email"
    name="email"
    required
>

<button type="submit">
    Valider
</button>

</form>

<?php if ($message): ?>

<p>
    <?= e($message) ?>
</p>

<?php endif; ?>

</section>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>