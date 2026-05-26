<?php
require_once __DIR__.'/auth.php';
require_once __DIR__.'/../config/db.php';
$pdo = db();

$adminTitle = 'Dashboard';

// Stats
$totalUsers     = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$totalCommandes = $pdo->query("SELECT COUNT(*) FROM commandes")->fetchColumn();
$commandesEnAttente = $pdo->query("SELECT COUNT(*) FROM commandes WHERE status = 'en_attente'")->fetchColumn();
$commandesPayees    = $pdo->query("SELECT COUNT(*) FROM commandes WHERE status = 'payee'")->fetchColumn();
$totalCA            = $pdo->query("SELECT COALESCE(SUM(total),0) FROM commandes WHERE status = 'payee'")->fetchColumn();
$totalProduits      = $pdo->query("SELECT COUNT(*) FROM produits WHERE is_active = 1")->fetchColumn();
$totalBillets       = $pdo->query("SELECT COALESCE(SUM(places_disponibles),0) FROM billeterie WHERE is_active = 1")->fetchColumn();

// Dernières commandes
$dernieresCommandes = $pdo->query("SELECT c.id, c.email, c.total, c.status, c.created_at FROM commandes c ORDER BY c.created_at DESC LIMIT 8")->fetchAll();

require_once __DIR__.'/layout.php';
?>

<div class="stat-grid">
  <div class="stat-card gold">
    <div class="stat-label">Chiffre d'affaires</div>
    <div class="stat-value"><?= number_format((float)$totalCA, 2, ',', ' ') ?> €</div>
    <div class="stat-sub">Commandes payées</div>
  </div>
  <div class="stat-card">
    <div class="stat-label">Commandes totales</div>
    <div class="stat-value"><?= $totalCommandes ?></div>
    <div class="stat-sub"><?= $commandesPayees ?> payées</div>
  </div>
  <div class="stat-card red">
    <div class="stat-label">En attente</div>
    <div class="stat-value"><?= $commandesEnAttente ?></div>
    <div class="stat-sub">À traiter</div>
  </div>
  <div class="stat-card">
    <div class="stat-label">Utilisateurs</div>
    <div class="stat-value"><?= $totalUsers ?></div>
    <div class="stat-sub">Comptes inscrits</div>
  </div>
  <div class="stat-card">
    <div class="stat-label">Produits actifs</div>
    <div class="stat-value"><?= $totalProduits ?></div>
    <div class="stat-sub">En boutique</div>
  </div>
  <div class="stat-card green">
    <div class="stat-label">Places restantes</div>
    <div class="stat-value"><?= $totalBillets ?></div>
    <div class="stat-sub">Billetterie</div>
  </div>
</div>

<div class="section-title">Dernières commandes</div>
<div class="admin-table-wrap">
  <table class="admin-table">
    <thead>
      <tr>
        <th>#</th>
        <th>Email</th>
        <th>Total</th>
        <th>Statut</th>
        <th>Date</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($dernieresCommandes as $c): ?>
      <tr>
        <td>#<?= $c['id'] ?></td>
        <td><?= htmlspecialchars($c['email']) ?></td>
        <td><?= number_format($c['total'], 2, ',', ' ') ?> €</td>
        <td><span class="status <?= $c['status'] ?>"><?= $c['status'] ?></span></td>
        <td><?= date('d/m/Y H:i', strtotime($c['created_at'])) ?></td>
        <td><a href="commandes.php?id=<?= $c['id'] ?>" class="btn-admin primary">Voir</a></td>
      </tr>
      <?php endforeach; ?>
      <?php if (empty($dernieresCommandes)): ?>
      <tr><td colspan="6" style="text-align:center;color:var(--admin-muted);padding:30px;">Aucune commande</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<?php require_once __DIR__.'/layout_end.php'; ?>
