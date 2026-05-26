<?php
declare(strict_types=1);

require_once __DIR__.'/includes/bootstrap.php';
require_once __DIR__.'/config/db.php';

$pdo = db();

$error = '';

$_SESSION['register_attempts'] = $_SESSION['register_attempts'] ?? 0;
$_SESSION['last_register'] = $_SESSION['last_register'] ?? time();

if ($_SESSION['register_attempts'] >= 5 && time() - $_SESSION['last_register'] < 300) {
    http_response_code(429);
    exit("Trop de tentatives. Réessayez plus tard.");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    verify_csrf();

    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $email = $email ? strtolower(trim($email)) : null;

    $password = trim($_POST['password'] ?? '');
    $password_confirm = trim($_POST['password_confirm'] ?? '');

    if (!$email) {
        $error = "Email invalide";
    }

    elseif (!preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d).{8,}$/', $password)) {
        $error = "Mot de passe trop faible (majuscule, minuscule, chiffre, 8+ caractères)";
    }

    elseif ($password !== $password_confirm) {
        $error = "Les mots de passe ne correspondent pas";
    }

    else {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("
            INSERT INTO users(email, password) 
            VALUES(?, ?)
        ");

        try {
            $stmt->execute([$email, $hash]);

            $_SESSION['register_attempts'] = 0;

            header("Location: /login.php");
            exit;

        } catch (PDOException $e) {

            if ($e->getCode() == 23000) {
                $error = "Email déjà utilisé";
            } else {
                throw $e;
            }
        }
    }

    $_SESSION['register_attempts']++;
    $_SESSION['last_register'] = time();
}
?>

<?php $pageTitle="Créer un compte"; include __DIR__.'/includes/header.php'; ?>

<section class="container">
<section class="connexion">

<h1>Créer un compte</h1>

<?php if($error): ?>
<p style="color:red"><?= e($error) ?></p>
<?php endif; ?>

<form method="POST">

<input type="hidden" name="csrf" value="<?= csrf_token() ?>">

<label>Email</label>
<input type="email" name="email" required>

<label>Mot de passe</label>
<input type="password" name="password" required>

<label>Confirmer mot de passe</label>
<input type="password" name="password_confirm" required>

<button type="submit">Valider</button>

</form>

</section>
</section>

<?php include __DIR__.'/includes/footer.php'; ?>