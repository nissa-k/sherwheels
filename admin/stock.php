<?php
require_once __DIR__.'/auth.php';
require_once __DIR__.'/../config/db.php';
$pdo = db();

$adminTitle = 'Stock';
$totalUsers         = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$commandesEnAttente = $pdo->query("SELECT COUNT(*) FROM commandes WHERE status = 'en_attente'")->fetchColumn();

require_once __DIR__.'/layout.php';
?>

<div class="section-title">Gestion du stock</div>
<p style="color:var(--admin-muted);padding:20px 0;">
  La gestion du stock est désactivée pour le moment.
</p>

<?php require_once __DIR__.'/layout_end.php'; ?>
