<?php
$pageTitle = 'Billetterie — SherWheels Festival';
include __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/config/db.php';

$pdo = db();
$stmt = $pdo->query("SELECT id_billet, type_billet, description, prix, prix_concert, places_disponibles FROM billeterie WHERE is_active = 1");
$billets = $stmt->fetchAll();
?>

<section class="section" aria-labelledby="page-title">
  <div class="container">
    <h1 id="page-title">Billetterie</h1>
    <p class="muted">Réservez vos billets pour le SherWheels Festival.</p>

    <?php if (isset($_GET['error'])): ?>
      <?php if ($_GET['error'] === 'complet'): ?>
        <p class="alert alert--error">Ce billet est complet.</p>
      <?php elseif ($_GET['error'] === 'invalid'): ?>
        <p class="alert alert--error">Requête invalide.</p>
      <?php endif; ?>
    <?php endif; ?>

    <div class="cards" role="list">
      <?php if (empty($billets)): ?>
        <p class="muted">Aucun billet disponible pour le moment.</p>
      <?php else: ?>
        <?php foreach ($billets as $billet): ?>
          <article class="card" role="listitem">
            <h2 class="card-title"><?= htmlspecialchars($billet['type_billet']) ?></h2>
            <p class="card-price"><strong><?= number_format($billet['prix'], 2, ',', ' ') ?> €</strong></p>

            <?php if (!empty($billet['description'])): ?>
              <p class="muted"><?= nl2br(htmlspecialchars($billet['description'])) ?></p>
            <?php endif; ?>

            <form method="POST" action="/process/add_to_cart.php">
              <input type="hidden" name="produit_id" value="<?= $billet['id_billet'] ?>">
              <input type="hidden" name="taille_id" value="1">
              <input type="hidden" name="type" value="billet">

              <?php if ($billet['places_disponibles'] > 0): ?>
                <label for="quantite_<?= $billet['id_billet'] ?>">Quantité :</label>
                <select name="quantite" id="quantite_<?= $billet['id_billet'] ?>">
                  <?php for ($i = 1; $i <= 10; $i++): ?>
                    <option value="<?= $i ?>"><?= $i ?></option>
                  <?php endfor; ?>
                </select>

                <?php if ($billet['prix_concert'] > 0): ?>
                  <label style="display:flex;align-items:center;gap:8px;margin-top:10px;cursor:pointer;">
                    <input type="checkbox" name="concert" value="1">
                    Option Concert <span class="muted">(+<?= number_format($billet['prix_concert'], 2, ',', ' ') ?> €)</span>
                  </label>
                <?php endif; ?>

                <button type="submit" class="btn btn--primary" style="margin-top:12px;">Acheter</button>
              <?php else: ?>
                <button class="btn btn--primary" disabled>Complet</button>
              <?php endif; ?>
            </form>

          </article>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>

  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>