<?php
$pageTitle = 'Galerie — SherWheels Festival';
include __DIR__ . '/includes/header.php';
require_once __DIR__ . '/config/db.php';

$pdo = db();

// récupérer les images
$images = $pdo->query("SELECT * FROM galerie ORDER BY created_at DESC")->fetchAll();
?>

<section class="section" aria-labelledby="page-title">
  <div class="container">
    <h1 id="page-title">Galerie</h1>
    <p class="muted">Découvrez les moments forts du SherWheels Festival à travers notre galerie photo.</p>

    <div class="gallery">

      <?php if (empty($images)): ?>
        <p>Aucune image pour le moment.</p>
      <?php else: ?>

        <?php foreach ($images as $img): ?>
          <div class="gallery-item">
            <img src="/assets/img/<?= htmlspecialchars($img['image']) ?>" alt="image galerie">
          </div>
        <?php endforeach; ?>

      <?php endif; ?>

    </div>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>