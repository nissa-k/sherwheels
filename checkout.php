<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/stripe.php';

// Vérification connexion
if (empty($_SESSION['user_id'])) {
    header('Location: /login.php?redirect=checkout');
    exit;
}

// Vérification panier
if (empty($_SESSION['cart'])) {
    header('Location: /cart.php');
    exit;
}

$pdo = db();

$cart = $_SESSION['cart'] ?? [];

$products = [];
$total = 0;

if (!empty($cart)) {

    $billetIds  = [];
    $produitIds = [];

    foreach ($cart as $item) {

        if (($item['type'] ?? 'produit') === 'billet') {
            $billetIds[] = $item['produit_id'];
        } else {
            $produitIds[] = $item['produit_id'];
        }
    }

    // Produits boutique
    $produitsData = [];

    if (!empty($produitIds)) {

        $placeholders = implode(',', array_fill(0, count($produitIds), '?'));

        $stmt = $pdo->prepare("
            SELECT *
            FROM produits
            WHERE id IN ($placeholders)
        ");

        $stmt->execute($produitIds);

        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $p) {
            $produitsData[$p['id']] = $p;
        }
    }

    // Billets
    $billetsData = [];

    if (!empty($billetIds)) {

        $placeholders = implode(',', array_fill(0, count($billetIds), '?'));

        $stmt = $pdo->prepare("
            SELECT *
            FROM billeterie
            WHERE id_billet IN ($placeholders)
        ");

        $stmt->execute($billetIds);

        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $b) {
            $billetsData[$b['id_billet']] = $b;
        }
    }

    // Construction du récapitulatif
    foreach ($cart as $item) {

        $type = $item['type'] ?? 'produit';

        $concert = $item['concert'] ?? 0;

        // Billets
        if ($type === 'billet' && isset($billetsData[$item['produit_id']])) {

            $b = $billetsData[$item['produit_id']];

            $qty = (int)$item['quantite'];

            $price = (float)$b['prix'];

            if ($concert) {
                $price += (float)$b['prix_concert'];
            }

            $lineTotal = $price * $qty;

            $products[] = [
                'name'           => $b['type_billet'],
                'qty'            => $qty,
                'price'          => $price,
                'total'          => $lineTotal,
                'concert'        => $concert,
                'concert_prix'   => (float)$b['prix_concert'],
                'type'           => 'billet'
            ];

            $total += $lineTotal;

        // Produits boutique
        } elseif ($type === 'produit' && isset($produitsData[$item['produit_id']])) {

            $p = $produitsData[$item['produit_id']];

            $qty = (int)$item['quantite'];

            $lineTotal = (float)$p['prix'] * $qty;

            $products[] = [
                'name'           => $p['nom'],
                'qty'            => $qty,
                'price'          => (float)$p['prix'],
                'total'          => $lineTotal,
                'concert'        => 0,
                'concert_prix'   => 0,
                'type'           => 'produit'
            ];

            $total += $lineTotal;
        }
    }
}

$pageTitle = 'Commande — SherWheels';

include __DIR__ . '/includes/header.php';
?>

<section class="section">
    <div class="container">

        <h1 class="page-title">Récapitulatif de commande</h1>

        <?php if (!empty($products)): ?>

            <div class="cart-list">

                <?php foreach ($products as $p): ?>

                    <div class="cart-item">

                        <div class="cart-details">

                            <h3>
                                <?= htmlspecialchars($p['name']) ?>
                            </h3>

                            <p>
                                Quantité : <?= $p['qty'] ?>
                            </p>

                            <p>
                                Prix unitaire :
                                <?= number_format($p['price'], 2, ',', ' ') ?> €
                            </p>

                            <?php if ($p['concert']): ?>

                                <p class="muted">
                                    Option Concert :
                                    +<?= number_format($p['concert_prix'], 2, ',', ' ') ?> €
                                </p>

                            <?php endif; ?>

                            <p class="price">
                                Total :
                                <?= number_format($p['total'], 2, ',', ' ') ?> €
                            </p>

                        </div>

                    </div>

                <?php endforeach; ?>

            </div>

            <div class="cart-total">

                <h2>
                    Total :
                    <?= number_format($total, 2, ',', ' ') ?> €
                </h2>

                <form action="/create-checkout-session.php" method="POST" style="margin-top:30px;">
                    <!-- Token CSRF — indispensable pour passer la vérification dans bootstrap.php -->
                    <input type="hidden" name="csrf" value="<?= csrf_token() ?>">

                    <button type="submit" class="btn">
                        Payer
                    </button>

                </form>

                <p style="margin-top:20px;">
                    <a href="/cart.php" class="btn">
                        ← Retour au panier
                    </a>
                </p>

            </div>

        <?php endif; ?>

    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
