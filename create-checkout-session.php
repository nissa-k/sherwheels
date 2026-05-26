<?php
declare(strict_types=1);

ob_start();

require_once __DIR__ . '/includes/bootstrap.php'; // ← EN PREMIER, définit verify_csrf()
require_once __DIR__ . '/includes/stripe.php';
require_once __DIR__ . '/config/db.php';

use Stripe\Checkout\Session;

verify_csrf(); // ← APRÈS bootstrap, maintenant la fonction existe

$user_id = $_SESSION['user_id'] ?? null;
$cart    = $_SESSION['cart']    ?? [];

session_write_close();

if (empty($user_id)) {
    ob_end_clean();
    header('Location: /login.php');
    exit;
}

if (empty($cart)) {
    ob_end_clean();
    header('Location: /cart.php');
    exit;
}

$pdo = db();
$line_items = [];

foreach ($cart as $item) {

    $type = $item['type'] ?? 'produit';

    if ($type === 'billet') {
        $stmt = $pdo->prepare("SELECT * FROM billeterie WHERE id_billet = ?");
        $stmt->execute([$item['produit_id']]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$product) continue;

        $price = (float)$product['prix'];
        if (!empty($item['concert'])) {
            $price += (float)$product['prix_concert'];
        }
        $name = $product['type_billet'];

    } else {
        $stmt = $pdo->prepare("SELECT * FROM produits WHERE id = ?");
        $stmt->execute([$item['produit_id']]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$product) continue;

        $price = (float)$product['prix'];
        $name  = $product['nom'];
    }

    $line_items[] = [
        'price_data' => [
            'currency'     => 'eur',
            'product_data' => ['name' => $name],
            'unit_amount'  => (int)($price * 100),
        ],
        'quantity' => (int)$item['quantite'],
    ];
}

try {
    $checkout_session = Session::create([
        'payment_method_types' => ['card'],
        'line_items'           => $line_items,
        'mode'                 => 'payment',
        'success_url'          => 'http://sherwheels3/success.php',
        'cancel_url'           => 'http://sherwheels3/cancel.php',
        'metadata'             => ['user_id' => $user_id],
    ]);

    ob_end_clean();
    header('Location: ' . $checkout_session->url);
    exit;

} catch (Exception $e) {
    ob_end_clean();
    die('Erreur Stripe : ' . htmlspecialchars($e->getMessage()));
}
