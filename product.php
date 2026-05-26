<?php
require_once __DIR__ . '/config/db.php';
include __DIR__ . '/includes/header.php';

$pdo = db();

$id = $_GET['id'] ?? null;

$stmt = $pdo->prepare("SELECT * FROM produits WHERE id = ?");
$stmt->execute([$id]);
$produit = $stmt->fetch();

if (!$produit) {
  die("Produit introuvable");
}
?>

<section class="section">
  <div class="container">

    <div style="display:flex; gap:50px;">

      <img src="assets/img/<?= $produit['image'] ?>" style="width:500px;">

      <div>
        <h1><?= $produit['nom'] ?></h1>
        <p><?= $produit['description_longue'] ?></p>
        <h2><?= $produit['prix'] ?> €</h2>

        <form method="POST" action="process/add_to_cart.php">
          <input type="hidden" name="produit_id" value="<?= $produit['id'] ?>">
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
            $t->execute([$produit['id']]);
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

  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
