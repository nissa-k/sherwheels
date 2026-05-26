<?php
require_once __DIR__.'/auth.php';
require_once __DIR__.'/../config/db.php';
$pdo = db();

$adminTitle = 'Utilisateurs';
$msg = '';

// Suppression
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    // Vérifier que c'est pas un admin
    $check = $pdo->prepare("SELECT role FROM users WHERE id = ?");
    $check->execute([$id]);
    $target = $check->fetch();

    if (!$target) {
        $msg = ['type' => 'error', 'text' => 'Utilisateur introuvable.'];
    } elseif ($target['role'] === 'admin') {
        $msg = ['type' => 'error', 'text' => 'Impossible de supprimer un admin.'];
    } elseif ($id === (int)$_SESSION['user_id']) {
        $msg = ['type' => 'error', 'text' => 'Impossible de se supprimer soi-même.'];
    } else {
        $pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$id]);
        $msg = ['type' => 'success', 'text' => 'Utilisateur supprimé.'];
    }
}

$users = $pdo->query("SELECT id, email, role, created_at FROM users ORDER BY created_at DESC")->fetchAll();

$totalUsers = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$commandesEnAttente = $pdo->query("SELECT COUNT(*) FROM commandes WHERE status = 'en_attente'")->fetchColumn();

require_once __DIR__.'/layout.php';
?>

<?php if ($msg): ?>
  <div class="alert <?= $msg['type'] ?>"><?= htmlspecialchars($msg['text']) ?></div>
<?php endif; ?>

<div class="section-title">Tous les utilisateurs (<?= count($users) ?>)</div>

<div class="admin-table-wrap">
  <table class="admin-table">
    <thead>
      <tr>
        <th>#</th>
        <th>Email</th>
        <th>Rôle</th>
        <th>Inscrit le</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($users as $u): ?>
      <tr>
        <td><?= $u['id'] ?></td>
        <td><?= htmlspecialchars($u['email']) ?></td>
        <td><span class="status <?= $u['role'] ?>"><?= $u['role'] ?></span></td>
        <td><?= date('d/m/Y H:i', strtotime($u['created_at'])) ?></td>
        <td>
          <?php if ($u['role'] !== 'admin' && $u['id'] !== (int)$_SESSION['user_id']): ?>
            <a href="?delete=<?= $u['id'] ?>"
               class="btn-admin danger"
               onclick="return confirm('Supprimer <?= htmlspecialchars($u['email']) ?> ?')">Supprimer</a>
          <?php else: ?>
            <span style="color:var(--admin-muted);font-size:.75rem;">Protégé</span>
          <?php endif; ?>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<?php require_once __DIR__.'/layout_end.php'; ?>
