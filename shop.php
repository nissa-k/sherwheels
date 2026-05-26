<?php
require_once __DIR__ . '/config/db.php';
include __DIR__ . '/includes/header.php';

$pdo = db();
$stmt = $pdo->query("SELECT * FROM produits WHERE is_active = 1");
$produits = $stmt->fetchAll();
?>

<section class="section">
  <div class="container">
    <h1>Boutique SherWheels</h1>

    <div class="shop-grid">
      <?php foreach ($produits as $p): ?>
        <div class="product-card">

          <a href="product.php?id=<?= $p['id'] ?>">
            <img src="/assets/img/<?= htmlspecialchars($p['image']) ?>" class="product-img">
          </a>
          <div class="product-body">
            <h3 class="product-title"><?= htmlspecialchars($p['nom']) ?></h3>
            <p class="product-desc"><?= nl2br(htmlspecialchars($p['description'])) ?></p>
            <p class="product-price"><?= $p['prix'] ?> €</p>

            <form method="POST" action="process/add_to_cart.php" class="product-actions">
              <input type="hidden" name="produit_id" value="<?= $p['id'] ?>">
              <input type="hidden" name="type" value="produit">

              <select name="taille_id" required>
                <option value="">Taille</option>
                <?php
                $t = $pdo->prepare("
                  SELECT t.id, t.nom 
                  FROM tailles t
                  JOIN produits_tailles pt ON pt.taille_id = t.id
                  WHERE pt.produit_id = ?
                  ORDER BY t.id
                ");
                $t->execute([$p['id']]);
                foreach ($t as $taille): ?>
                  <option value="<?= $taille['id'] ?>"><?= $taille['nom'] ?></option>
                <?php endforeach; ?>
              </select>

              <select name="quantite">
                <?php for ($i = 1; $i <= 10; $i++): ?>
                  <option value="<?= $i ?>"><?= $i ?></option>
                <?php endfor; ?>
              </select>

              <button type="submit">Ajouter au panier</button>
            </form>

          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
