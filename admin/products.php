<?php
require_once __DIR__.'/auth.php';
require_once __DIR__.'/../config/db.php';
$pdo = db();

$adminTitle = 'Produits';
$msg = '';

// Ajouter un produit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $nom         = trim($_POST['nom'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $prix        = (float)($_POST['prix'] ?? 0);

    if (!$nom || $prix <= 0) {
        $msg = ['type' => 'error', 'text' => 'Nom et prix requis.'];
    } else {
        $pdo->prepare("INSERT INTO produits (nom, description, prix, is_active) VALUES (?,?,?,1)")
            ->execute([$nom, $description, $prix]);
        $msg = ['type' => 'success', 'text' => 'Produit ajouté.'];
    }
}

// Modifier le prix
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_prix') {
    $id   = (int)$_POST['id'];
    $prix = (float)$_POST['prix'];

    if ($prix <= 0) {
        $msg = ['type' => 'error', 'text' => 'Prix invalide.'];
    } else {
        $pdo->prepare("UPDATE produits SET prix = ? WHERE id = ?")
            ->execute([$prix, $id]);
        $msg = ['type' => 'success', 'text' => 'Prix mis à jour.'];
    }
}

// Activer / désactiver
if (isset($_GET['toggle'])) {
    $id = (int)$_GET['toggle'];
    $pdo->prepare("UPDATE produits SET is_active = NOT is_active WHERE id = ?")->execute([$id]);
    header('Location: products.php');
    exit;
}

// Supprimer
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $pdo->prepare("DELETE FROM produits WHERE id = ?")->execute([$id]);
    header('Location: products.php');
    exit;
}

$totalUsers         = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$commandesEnAttente = $pdo->query("SELECT COUNT(*) FROM commandes WHERE status = 'en_attente'")->fetchColumn();
$produits           = $pdo->query("SELECT * FROM produits ORDER BY id")->fetchAll();

require_once __DIR__.'/layout.php';
?>

<?php if ($msg): ?>
  <div class="alert <?= $msg['type'] ?>"><?= htmlspecialchars($msg['text']) ?></div>
<?php endif; ?>

<!-- Formulaire ajout -->
<div class="section-title">Ajouter un produit</div>
<form method="POST" class="admin-form" style="margin-bottom:32px;">
  <input type="hidden" name="action" value="add">
  <label>Nom du produit</label>
  <input type="text" name="nom" placeholder="Ex: T-shirt Rouge" required>
  <label>Description courte</label>
  <input type="text" name="description" placeholder="Ex: T-shirt officiel">
  <label>Prix (€)</label>
  <input type="number" step="0.01" min="0" name="prix" placeholder="Ex: 29.99" required>
  <button type="submit" class="btn-admin primary">Ajouter</button>
</form>

<!-- Liste produits -->
<div class="section-title">Produits (<?= count($produits) ?>)</div>
<div class="admin-table-wrap">
  <table class="admin-table">
    <thead>
      <tr>
        <th>#</th>
        <th>Image</th>
        <th>Nom</th>
        <th>Description</th>
        <th>Prix</th>
        <th>Statut</th>
        <th>Actions</th>
      </tr>
    </thead>

    <tbody>
      <?php foreach ($produits as $p): ?>
      <tr>
        <td><?= $p['id'] ?></td>
        <td>
          <?php if ($p['image']): ?>
            <img src="/assets/img/<?= htmlspecialchars($p['image']) ?>"
                 style="width:44px;height:44px;object-fit:contain;background:rgba(255,255,255,.04);">
          <?php else: ?>
            <span style="color:var(--admin-muted);">—</span>
          <?php endif; ?>
        </td>
        <td><strong><?= htmlspecialchars($p['nom']) ?></strong></td>
        <td style="color:var(--admin-muted);"><?= htmlspecialchars($p['description'] ?? '') ?></td>
        <td><?= number_format($p['prix'], 2, ',', ' ') ?> €</td>
        <td>
          <span class="status <?= $p['is_active'] ? 'payee' : 'annulee' ?>">
            <?= $p['is_active'] ? 'Actif' : 'Inactif' ?>
          </span>
        </td>
        <td>
          <!-- Modifier le prix -->
          <form method="POST" style="display:flex;gap:6px;align-items:center;margin-bottom:6px;">
            <input type="hidden" name="action" value="update_prix">
            <input type="hidden" name="id" value="<?= $p['id'] ?>">
            <input type="number" name="prix" value="<?= $p['prix'] ?>" step="0.01" min="0"
                   style="width:90px;padding:5px;background:rgba(255,255,255,.05);border:1px solid var(--admin-border);color:var(--admin-text);">
            <button type="submit" class="btn-admin primary" style="padding:5px 10px;">Prix</button>
          </form>
          <!-- Activer/désactiver et supprimer -->
          <div style="display:flex;gap:6px;">
            <a href="?toggle=<?= $p['id'] ?>" class="btn-admin">
              <?= $p['is_active'] ? 'Désactiver' : 'Activer' ?>
            </a>
            <a href="?delete=<?= $p['id'] ?>"
               class="btn-admin danger"
               onclick="return confirm('Supprimer <?= htmlspecialchars($p['nom']) ?> ?')">
                Supprimer
            </a>
          </div>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<?php require_once __DIR__.'/layout_end.php'; ?>
