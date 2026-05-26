<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/config/db.php';

$pdo = db();

$error = '';

$_SESSION['login_attempts'] = $_SESSION['login_attempts'] ?? 0;
$_SESSION['last_attempt'] = $_SESSION['last_attempt'] ?? time();

if ($_SESSION['login_attempts'] >= 5 && time() - $_SESSION['last_attempt'] < 300) {
    http_response_code(429);
    exit("Trop de tentatives. Réessayez plus tard.");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    verify_csrf();

    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $email = $email ? strtolower(trim($email)) : null;

    $password = $_POST['password'] ?? '';

    if (!$email || !$password) {
        $error = "Veuillez remplir tous les champs.";
    } else {

        $stmt = $pdo->prepare("
            SELECT id, email, password, role 
            FROM users 
            WHERE email = ?
            LIMIT 1
        ");
        $stmt->execute([$email]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        $fakeHash = '$2y$10$usesomesillystringfore7hnbRJHxXVLeakoG8K30oukPsA.ztMG';

        $hashToCheck = $user ? $user['password'] : $fakeHash;

        $valid = password_verify($password, $hashToCheck);

        if ($valid && $user) {

            session_regenerate_id(true);

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['login_attempts'] = 0;
            
            // Restaurer panier sauvegardé

            $stmt = $pdo->prepare("
                SELECT cart_data
                FROM paniers
                WHERE user_id = ?
                LIMIT 1
            ");

            $stmt->execute([$user['id']]);

            $savedCart = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($savedCart && !empty($savedCart['cart_data'])) {

                $_SESSION['cart'] = json_decode(
                    $savedCart['cart_data'],
                    true
                );
            }

            header("Location: /index.php");
            exit;

        } else {
            $error = "Email ou mot de passe incorrect.";

            $_SESSION['login_attempts']++;
            $_SESSION['last_attempt'] = time();
        }
    }
}

$pageTitle = "Connexion";
include __DIR__.'/includes/header.php';
?>

<section class="container" style="max-width:500px;margin:80px auto;">
<section class="connexion">

<h1>Connexion</h1>

<?php if ($error): ?>
<p style="color:red"><?= e($error) ?></p>
<?php endif; ?>

<form method="POST">

<input type="hidden" name="csrf" value="<?= csrf_token() ?>">

<div>
<label>Email</label><br>
<input type="email" name="email" required>
</div>

<br>

<div>
<label>Mot de passe</label><br>
<input type="password" name="password" required>
</div>

<br>

<button type="submit">Se connecter</button>

</form>

<div class="login-links">
  <p>Pas de compte ? <a href="register.php">Créer un compte</a></p>
  <p><a href="forgot_passwd.php">Mot de passe oublié ?</a></p>
</div>

</section>
</section>

<?php include __DIR__.'/includes/footer.php'; ?>