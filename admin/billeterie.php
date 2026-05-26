<?php
require_once __DIR__.'/auth.php';
require_once __DIR__.'/../config/db.php';
$pdo = db();

$adminTitle = 'Billetterie';
$msg = '';

// Ajouter un billet
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $type        = trim($_POST['type_billet'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $prix        = (float)($_POST['prix'] ?? 0);
    $places      = (int)($_POST['places'] ?? 0);

    if (!$type || $prix < 0 || $places <= 0) {
        $msg = ['type' => 'error', 'text' => 'Tous les champs sont requis.'];
    } else {
        $pdo->prepare("INSERT INTO billeterie (type_billet, description, prix, places_disponibles, is_active) VALUES (?,?,?,?,1)")
            ->execute([$type, $description, $prix, $places]);
        $msg = ['type' => 'success', 'text' => 'Billet ajouté.'];
    }
}

// Modifier billet
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {
    $id          = (int)$_POST['id'];
    $description = trim($_POST['description'] ?? '');
    $places      = (int)$_POST['places'];
    $prix        = (float)$_POST['prix'];
    $active      = isset($_POST['is_active']) ? 1 : 0;

    $pdo->prepare("UPDATE billeterie SET description = ?, places_disponibles = ?, prix = ?, is_active = ? WHERE id_billet = ?")
        ->execute([$description, $places, $prix, $active, $id]);
    $msg = ['type' => 'success', 'text' => 'Billet mis à jour.'];
}

// Supprimer
if (isset($_GET['delete'])) {
    $pdo->prepare("DELETE FROM billeterie WHERE id_billet = ?")->execute([(int)$_GET['delete']]);
    $msg = ['type' => 'success', 'text' => 'Billet supprimé.'];
}

$totalUsers         = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$commandesEnAttente = $pdo->query("SELECT COUNT(*) FROM commandes WHERE status = 'en_attente'")->fetchColumn();
$billets            = $pdo->query("SELECT * FROM billeterie ORDER BY id_billet")->fetchAll();

require_once __DIR__.'/layout.php';
?>

<?php if ($msg): ?>
  <div class="alert <?= $msg['type'] ?>"><?= htmlspecialchars($msg['text']) ?></div>
<?php endif; ?>

<!-- Ajouter -->
<div class="section-title">Ajouter un billet</div>
<form method="POST" class="admin-form" style="margin-bottom:32px;">
  <input type="hidden" name="action" value="add">
  <label>Type de billet</label>
  <input type="text" name="type_billet" placeholder="Ex: Pass VIP" required>
  <label>Description</label>
  <textarea name="description" placeholder="Ex: Accès 1 jour, parking inclus..." rows="3"
    style="padding:8px;background:rgba(255,255,255,.05);border:1px solid var(--admin-border);color:var(--admin-text);border-radius:6px;resize:vertical;"></textarea>
  <label>Prix (€)</label>
  <input type="number" name="prix" step="0.01" min="0" placeholder="Ex: 49.99" required>
  <label>Places disponibles</label>
  <input type="number" name="places" min="1" placeholder="Ex: 100" required>
  <button type="submit" class="btn-admin primary">Ajouter</button>
</form>

<!-- Liste -->
<div class="section-title">Billets existants</div>
<div class="admin-table-wrap">
  <table class="admin-table">
    <thead>
      <tr><th>Type</th><th>Description</th><th>Prix</th><th>Places</th><th>Actif</th><th>Modifier</th><th>Supprimer</th></tr>
    </thead>
    <tbody>
      <?php foreach ($billets as $b): ?>
      <tr>
        <td><strong><?= htmlspecialchars($b['type_billet']) ?></strong></td>
        <td style="color:var(--admin-muted);max-width:200px;"><?= htmlspecialchars($b['description'] ?? '') ?></td>
        <td><?= number_format($b['prix'], 2, ',', ' ') ?> €</td>
        <td>
          <span style="color:<?= $b['places_disponibles'] == 0 ? 'var(--admin-red)' : 'var(--admin-green)' ?>;font-weight:900;">
            <?= $b['places_disponibles'] ?>
          </span>
        </td>
        <td><span class="status <?= $b['is_active'] ? 'payee' : 'annulee' ?>"><?= $b['is_active'] ? 'Oui' : 'Non' ?></span></td>
        <td>
          <form method="POST" style="display:flex;gap:6px;align-items:flex-start;flex-wrap:wrap;flex-direction:column;">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="id" value="<?= $b['id_billet'] ?>">
            <textarea name="description" rows="2" placeholder="Description..."
              style="width:180px;padding:5px;background:rgba(255,255,255,.05);border:1px solid var(--admin-border);color:var(--admin-text);border-radius:4px;resize:vertical;"><?= htmlspecialchars($b['description'] ?? '') ?></textarea>
            <div style="display:flex;gap:6px;align-items:center;flex-wrap:wrap;">
              <input type="number" name="prix" value="<?= $b['prix'] ?>" step="0.01" min="0"
                style="width:80px;padding:5px;background:rgba(255,255,255,.05);border:1px solid var(--admin-border);color:var(--admin-text);">
              <input type="number" name="places" value="<?= $b['places_disponibles'] ?>" min="0"
                style="width:70px;padding:5px;background:rgba(255,255,255,.05);border:1px solid var(--admin-border);color:var(--admin-text);">
              <label style="display:flex;align-items:center;gap:4px;font-size:.75rem;color:var(--admin-muted);text-transform:none;letter-spacing:0;margin:0;">
                <input type="checkbox" name="is_active" <?= $b['is_active'] ? 'checked' : '' ?>> Actif
              </label>
              <button type="submit" class="btn-admin primary" style="padding:5px 10px;">✓</button>
            </div>
          </form>
        </td>
        <td>
          <a href="?delete=<?= $b['id_billet'] ?>" class="btn-admin danger"
             onclick="return confirm('Supprimer ce billet ?')">🗑</a>
        </td>
      </tr>
      <?php endforeach; ?>
      <?php if (empty($billets)): ?>
      <tr><td colspan="7" style="text-align:center;color:var(--admin-muted);padding:20px;">Aucun billet</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<?php require_once __DIR__.'/layout_end.php'; ?>
