<?php
ini_set('display_errors', '0');
error_reporting(0);

$pageTitle = 'Panier — SherWheels';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/config/db.php';

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

    $produitsData = [];
    if (!empty($produitIds)) {
        $placeholders = implode(',', array_fill(0, count($produitIds), '?'));
        $stmt = $pdo->prepare("SELECT * FROM produits WHERE id IN ($placeholders)");
        $stmt->execute($produitIds);
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $p) {
            $produitsData[$p['id']] = $p;
        }
    }

    $billetsData = [];
    if (!empty($billetIds)) {
        $placeholders = implode(',', array_fill(0, count($billetIds), '?'));
        $stmt = $pdo->prepare("SELECT * FROM billeterie WHERE id_billet IN ($placeholders)");
        $stmt->execute($billetIds);
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $b) {
            $billetsData[$b['id_billet']] = $b;
        }
    }

    $taillesData = [];
    $tailleIds = array_unique(array_map(fn($i) => $i['taille_id'], $cart));
    if (!empty($tailleIds)) {
        $placeholders = implode(',', array_fill(0, count($tailleIds), '?'));
        $stmt = $pdo->prepare("SELECT id, nom FROM tailles WHERE id IN ($placeholders)");
        $stmt->execute(array_values($tailleIds));
        $taillesData = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    }

    foreach ($cart as $key => $item) {
        $type         = $item['type'] ?? 'produit';
        $concert      = $item['concert'] ?? 0;
        $concert_prix = (float)($item['prix_concert'] ?? 0);

        if ($type === 'billet' && isset($billetsData[$item['produit_id']])) {
            $b         = $billetsData[$item['produit_id']];
            $qty       = $item['quantite'];
            $prix_unit = (float)$b['prix'] + ($concert ? $concert_prix : 0);
            $lineTotal = $prix_unit * $qty;

            $products[] = [
                'key'         => $key,
                'id'          => $b['id_billet'],
                'name'        => $b['type_billet'],
                'price'       => $prix_unit,
                'qty'         => $qty,
                'total'       => $lineTotal,
                'taille_id'   => $item['taille_id'],
                'taille_nom'  => null,
                'type'        => 'billet',
                'concert'     => $concert,
                'concert_prix'=> $concert_prix > 0 ? $concert_prix : (float)$b['prix_concert'],
                'image'       => null
            ];
            $total += $lineTotal;

        } elseif ($type === 'produit' && isset($produitsData[$item['produit_id']])) {
            $p         = $produitsData[$item['produit_id']];
            $qty       = $item['quantite'];
            $lineTotal = $p['prix'] * $qty;

            $products[] = [
                'key'         => $key,
                'id'          => $p['id'],
                'name'        => $p['nom'],
                'price'       => $p['prix'],
                'qty'         => $qty,
                'total'       => $lineTotal,
                'taille_id'   => $item['taille_id'],
                'taille_nom'  => $taillesData[$item['taille_id']] ?? '',
                'type'        => 'produit',
                'concert'     => 0,
                'concert_prix'=> 0,
                'image'       => $p['image']
            ];
            $total += $lineTotal;
        }
    }
}
?>

<section class="section">
  <div class="container">
    <h1 class="page-title">Ton panier</h1>

    <?php if (empty($products)): ?>
      <p class="empty">Ton panier est vide.</p>
    <?php else: ?>

      <div class="cart-list">
        <?php foreach ($products as $p): ?>
          <div class="cart-item">

            <?php if ($p['type'] === 'billet'): ?>
              <div class="cart-img-placeholder"></div>
            <?php else: ?>
              <img src="/assets/img/<?= htmlspecialchars($p['image']) ?>" alt="">
            <?php endif; ?>

            <div class="cart-details">
              <h3><?= htmlspecialchars($p['name']) ?></h3>
              <?php if ($p['taille_nom']): ?>
                <p>Taille : <?= htmlspecialchars($p['taille_nom']) ?></p>
              <?php endif; ?>
              <?php if ($p['concert']): ?>
                <p class="muted">+ Option Concert (+<?= number_format($p['concert_prix'], 2, ',', ' ') ?> €)</p>
              <?php endif; ?>
              <p><?= number_format($p['price'], 2, ',', ' ') ?> €</p>
            </div>

            <div class="cart-actions">
              <form method="POST" action="/process/update_cart.php" style="display:inline;">
                <input type="hidden" name="key" value="<?= htmlspecialchars($p['key']) ?>">

                <select name="quantite" onchange="this.form.submit()">
                  <?php for ($i = 1; $i <= 10; $i++): ?>
                    <option value="<?= $i ?>" <?= $i == $p['qty'] ? 'selected' : '' ?>>
                      <?= $i ?>
                    </option>
                  <?php endfor; ?>
                </select>

                <?php if ($p['type'] === 'billet' && $p['concert_prix'] > 0): ?>
                  <label style="display:flex;align-items:center;gap:8px;margin-top:8px;cursor:pointer;">
                    <input type="checkbox" name="concert" value="1"
                           <?= $p['concert'] ? 'checked' : '' ?>
                           onchange="this.form.submit()">
                    Option Concert <span class="muted">(+<?= number_format($p['concert_prix'], 2, ',', ' ') ?> €)</span>
                  </label>
                <?php endif; ?>

                <noscript><button type="submit">Mettre à jour</button></noscript>
              </form>

              <div class="price"><?= number_format($p['total'], 2, ',', ' ') ?> €</div>

              <a class="remove-btn"
                 href="/process/remove_from_cart.php?id=<?= $p['id'] ?>&taille=<?= $p['taille_id'] ?>">
                Supprimer
              </a>
            </div>

          </div>
        <?php endforeach; ?>
      </div>

      <div class="cart-total">
        <h2>Total : <?= number_format($total, 2, ',', ' ') ?> €</h2>
        <a href="/checkout.php" class="btn">Commander</a>
        <a href="/shop.php" class="btn">← Continuer mes achats</a>
      </div>

    <?php endif; ?>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>