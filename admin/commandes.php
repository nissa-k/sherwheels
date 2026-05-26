<?php
require_once __DIR__.'/auth.php';
require_once __DIR__.'/../config/db.php';
$pdo = db();

$adminTitle = 'Commandes';
$msg = '';

// Changer le statut
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['statut'], $_POST['commande_id'])) {
    $statuts = ['en_attente', 'payee', 'annulee'];
    $newStatut = $_POST['statut'];
    $cid = (int)$_POST['commande_id'];

    if (in_array($newStatut, $statuts)) {
        $pdo->prepare("UPDATE commandes SET status = ? WHERE id = ?")->execute([$newStatut, $cid]);
        $msg = ['type' => 'success', 'text' => 'Statut mis à jour.'];
    }
}

$totalUsers = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$commandesEnAttente = $pdo->query("SELECT COUNT(*) FROM commandes WHERE status = 'en_attente'")->fetchColumn();

// Détail d'une commande
$detail = null;
$lignes = [];
if (isset($_GET['id'])) {
    $cid = (int)$_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM commandes WHERE id = ?");
    $stmt->execute([$cid]);
    $detail = $stmt->fetch();

    if ($detail) {
        $lignes = $pdo->prepare("
            SELECT lc.*, p.nom, p.image, t.nom as taille_nom
            FROM lignes_commande lc
            LEFT JOIN produits p ON p.id = lc.id_produit
            LEFT JOIN tailles t ON t.id = lc.taille_id
            WHERE lc.id_commande = ?
        ");
        $lignes->execute([$cid]);
        $lignes = $lignes->fetchAll();
    }
}

// Liste commandes
$filtre = $_GET['status'] ?? '';
$where = $filtre ? "WHERE status = " . $pdo->quote($filtre) : '';
$commandes = $pdo->query("SELECT * FROM commandes $where ORDER BY created_at DESC")->fetchAll();

require_once __DIR__.'/layout.php';
?>

<?php if ($msg): ?>
  <div class="alert <?= $msg['type'] ?>"><?= htmlspecialchars($msg['text']) ?></div>
<?php endif; ?>

<?php if ($detail): ?>
  <!-- Détail commande -->
  <div style="margin-bottom:16px;">
    <a href="commandes.php" class="btn-admin">← Retour</a>
  </div>

  <div class="section-title">Commande #<?= $detail['id'] ?></div>

  <div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;margin-bottom:24px;">
    <div class="admin-form" style="max-width:none;">
      <label>Email client</label>
      <p style="color:var(--admin-text)"><?= htmlspecialchars($detail['email']) ?></p>
      <label>Total</label>
      <p style="color:var(--admin-gold);font-weight:900;font-size:1.2rem"><?= number_format($detail['total'], 2, ',', ' ') ?> €</p>
      <label>Date</label>
      <p><?= date('d/m/Y à H:i', strtotime($detail['created_at'])) ?></p>
      <?php if ($detail['stripe_payment_id']): ?>
      <label>Stripe ID</label>
      <p style="font-size:.75rem;color:var(--admin-muted)"><?= htmlspecialchars($detail['stripe_payment_id']) ?></p>
      <?php endif; ?>
    </div>

    <div class="admin-form" style="max-width:none;">
      <label>Statut actuel</label>
      <p><span class="status <?= $detail['status'] ?>"><?= $detail['status'] ?></span></p>
      <label>Modifier le statut</label>
      <form method="POST">
        <input type="hidden" name="commande_id" value="<?= $detail['id'] ?>">
        <select name="statut">
          <option value="en_attente" <?= $detail['status'] === 'en_attente' ? 'selected' : '' ?>>En attente</option>
          <option value="payee" <?= $detail['status'] === 'payee' ? 'selected' : '' ?>>Payée</option>
          <option value="annulee" <?= $detail['status'] === 'annulee' ? 'selected' : '' ?>>Annulée</option>
        </select>
        <button type="submit" class="btn-admin primary" style="margin-top:10px;">Mettre à jour</button>
      </form>
    </div>
  </div>

  <div class="section-title">Articles commandés</div>
  <div class="admin-table-wrap">
    <table class="admin-table">
      <thead><tr><th>Produit</th><th>Taille</th><th>Qté</th><th>Prix unitaire</th><th>Total</th></tr></thead>
      <tbody>
        <?php foreach ($lignes as $l): ?>
        <tr>
          <td><?= htmlspecialchars($l['nom'] ?? 'Produit supprimé') ?></td>
          <td><?= htmlspecialchars($l['taille_nom'] ?? '—') ?></td>
          <td><?= $l['quantite'] ?></td>
          <td><?= number_format($l['prix_unitaire'], 2, ',', ' ') ?> €</td>
          <td><?= number_format($l['prix_unitaire'] * $l['quantite'], 2, ',', ' ') ?> €</td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($lignes)): ?>
        <tr><td colspan="5" style="text-align:center;color:var(--admin-muted);padding:20px;">Aucun article</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

<?php else: ?>
  <!-- Liste commandes -->
  <div style="display:flex;gap:8px;margin-bottom:20px;flex-wrap:wrap;">
    <a href="commandes.php" class="btn-admin <?= !$filtre ? 'primary' : '' ?>">Toutes</a>
    <a href="?status=en_attente" class="btn-admin <?= $filtre === 'en_attente' ? 'primary' : '' ?>">En attente</a>
    <a href="?status=payee" class="btn-admin <?= $filtre === 'payee' ? 'primary' : '' ?>">Payées</a>
    <a href="?status=annulee" class="btn-admin <?= $filtre === 'annulee' ? 'primary' : '' ?>">Annulées</a>
  </div>

  <div class="admin-table-wrap">
    <table class="admin-table">
      <thead>
        <tr><th>#</th><th>Email</th><th>Total</th><th>Statut</th><th>Date</th><th>Action</th></tr>
      </thead>
      <tbody>
        <?php foreach ($commandes as $c): ?>
        <tr>
          <td>#<?= $c['id'] ?></td>
          <td><?= htmlspecialchars($c['email']) ?></td>
          <td><?= number_format($c['total'], 2, ',', ' ') ?> €</td>
          <td><span class="status <?= $c['status'] ?>"><?= $c['status'] ?></span></td>
          <td><?= date('d/m/Y H:i', strtotime($c['created_at'])) ?></td>
          <td><a href="?id=<?= $c['id'] ?>" class="btn-admin primary">Voir</a></td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($commandes)): ?>
        <tr><td colspan="6" style="text-align:center;color:var(--admin-muted);padding:30px;">Aucune commande</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
<?php endif; ?>

<?php require_once __DIR__.'/layout_end.php'; ?>
