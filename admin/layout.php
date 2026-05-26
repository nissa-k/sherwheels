<?php
// layout.php — inclure en début de chaque page admin
// Usage: $adminTitle = 'Titre'; require_once __DIR__.'/layout.php';
$adminTitle = $adminTitle ?? 'Dashboard';

// Compter stats rapides pour la sidebar
$totalUsers    = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$totalCommandes = $pdo->query("SELECT COUNT(*) FROM commandes")->fetchColumn();
$commandesEnAttente = $pdo->query("SELECT COUNT(*) FROM commandes WHERE status = 'en_attente'")->fetchColumn();
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($adminTitle) ?> — Admin SherWheels</title>
  <link rel="stylesheet" href="/assets/css/style.css">
  <style>
    :root {
      --admin-sidebar: 240px;
      --admin-bg: #07090d;
      --admin-surface: #0f1420;
      --admin-border: rgba(205,180,133,.14);
      --admin-gold: #cdb485;
      --admin-red: #e05555;
      --admin-green: #4caf82;
      --admin-text: #f7f4ee;
      --admin-muted: rgba(247,244,238,.6);
    }

    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: system-ui, sans-serif; background: var(--admin-bg); color: var(--admin-text); min-height: 100vh; display: flex; }

    /* Sidebar */
    .admin-sidebar {
      width: var(--admin-sidebar);
      min-height: 100vh;
      background: var(--admin-surface);
      border-right: 1px solid var(--admin-border);
      display: flex;
      flex-direction: column;
      position: fixed;
      top: 0; left: 0;
      z-index: 100;
    }

    .admin-logo {
      padding: 24px 20px;
      border-bottom: 1px solid var(--admin-border);
      display: flex;
      align-items: center;
      gap: 10px;
      text-decoration: none;
    }

    .admin-logo-mark {
      width: 36px; height: 36px;
      background: rgba(205,180,133,.15);
      border: 1px solid rgba(205,180,133,.35);
      display: flex; align-items: center; justify-content: center;
      font-weight: 900; font-size: .85rem; letter-spacing: .06em;
      color: var(--admin-gold);
      flex-shrink: 0;
    }

    .admin-logo-text { line-height: 1.1; }
    .admin-logo-title { font-size: .8rem; font-weight: 900; letter-spacing: .14em; text-transform: uppercase; color: var(--admin-text); }
    .admin-logo-sub { font-size: .65rem; letter-spacing: .2em; text-transform: uppercase; color: var(--admin-muted); }

    .admin-nav { flex: 1; padding: 16px 0; }

    .admin-nav-section {
      padding: 8px 20px 4px;
      font-size: .6rem;
      letter-spacing: .2em;
      text-transform: uppercase;
      color: var(--admin-muted);
      margin-top: 8px;
    }

    .admin-nav a {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 10px 20px;
      text-decoration: none;
      color: var(--admin-muted);
      font-size: .85rem;
      font-weight: 600;
      letter-spacing: .04em;
      transition: all .15s;
      border-left: 2px solid transparent;
    }

    .admin-nav a:hover { color: var(--admin-text); background: rgba(255,255,255,.03); }
    .admin-nav a.active { color: var(--admin-gold); border-left-color: var(--admin-gold); background: rgba(205,180,133,.06); }

    .admin-nav .badge {
      margin-left: auto;
      background: var(--admin-red);
      color: #fff;
      font-size: .6rem;
      font-weight: 900;
      padding: 2px 6px;
      border-radius: 999px;
    }

    .admin-nav .badge.gold { background: var(--admin-gold); color: #111; }

    .admin-footer {
      padding: 16px 20px;
      border-top: 1px solid var(--admin-border);
      font-size: .75rem;
      color: var(--admin-muted);
    }

    .admin-footer a { color: var(--admin-muted); text-decoration: none; display: block; margin-bottom: 6px; }
    .admin-footer a:hover { color: var(--admin-text); }

    /* Main */
    .admin-main {
      margin-left: var(--admin-sidebar);
      flex: 1;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }

    .admin-topbar {
      padding: 16px 32px;
      border-bottom: 1px solid var(--admin-border);
      display: flex;
      align-items: center;
      justify-content: space-between;
      background: rgba(15,20,32,.8);
      backdrop-filter: blur(8px);
      position: sticky;
      top: 0;
      z-index: 50;
    }

    .admin-topbar h1 { font-size: 1.1rem; font-weight: 900; letter-spacing: .08em; text-transform: uppercase; }

    .admin-topbar-right { display: flex; align-items: center; gap: 14px; font-size: .82rem; color: var(--admin-muted); }
    .admin-topbar-right a { color: var(--admin-muted); text-decoration: none; }
    .admin-topbar-right a:hover { color: var(--admin-text); }

    .admin-content { padding: 32px; flex: 1; }

    /* Cards stats */
    .stat-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 16px; margin-bottom: 32px; }

    .stat-card {
      background: var(--admin-surface);
      border: 1px solid var(--admin-border);
      padding: 20px;
    }

    .stat-card .stat-label { font-size: .7rem; letter-spacing: .16em; text-transform: uppercase; color: var(--admin-muted); margin-bottom: 8px; }
    .stat-card .stat-value { font-size: 2rem; font-weight: 1000; }
    .stat-card .stat-sub { font-size: .75rem; color: var(--admin-muted); margin-top: 4px; }
    .stat-card.gold .stat-value { color: var(--admin-gold); }
    .stat-card.red .stat-value { color: var(--admin-red); }
    .stat-card.green .stat-value { color: var(--admin-green); }

    /* Table */
    .admin-table-wrap { background: var(--admin-surface); border: 1px solid var(--admin-border); overflow-x: auto; }
    .admin-table { width: 100%; border-collapse: collapse; font-size: .85rem; }
    .admin-table th { padding: 12px 16px; text-align: left; font-size: .65rem; letter-spacing: .16em; text-transform: uppercase; color: var(--admin-muted); border-bottom: 1px solid var(--admin-border); }
    .admin-table td { padding: 12px 16px; border-bottom: 1px solid rgba(205,180,133,.07); vertical-align: middle; }
    .admin-table tr:last-child td { border-bottom: none; }
    .admin-table tr:hover td { background: rgba(255,255,255,.02); }

    /* Badges status */
    .status { display: inline-block; padding: 3px 10px; border-radius: 999px; font-size: .7rem; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; }
    .status.payee { background: rgba(76,175,130,.15); color: var(--admin-green); border: 1px solid rgba(76,175,130,.3); }
    .status.en_attente { background: rgba(205,180,133,.12); color: var(--admin-gold); border: 1px solid rgba(205,180,133,.3); }
    .status.annulee { background: rgba(224,85,85,.12); color: var(--admin-red); border: 1px solid rgba(224,85,85,.3); }
    .status.admin { background: rgba(224,85,85,.12); color: var(--admin-red); border: 1px solid rgba(224,85,85,.3); }
    .status.user { background: rgba(255,255,255,.06); color: var(--admin-muted); border: 1px solid rgba(255,255,255,.12); }

    /* Boutons */
    .btn-admin { display: inline-flex; align-items: center; gap: 6px; padding: 7px 14px; font-size: .78rem; font-weight: 700; letter-spacing: .06em; text-transform: uppercase; text-decoration: none; cursor: pointer; border: 1px solid var(--admin-border); background: rgba(255,255,255,.04); color: var(--admin-text); transition: all .15s; }
    .btn-admin:hover { background: rgba(255,255,255,.08); }
    .btn-admin.danger { border-color: rgba(224,85,85,.4); color: var(--admin-red); }
    .btn-admin.danger:hover { background: rgba(224,85,85,.1); }
    .btn-admin.primary { border-color: rgba(205,180,133,.4); color: var(--admin-gold); }
    .btn-admin.primary:hover { background: rgba(205,180,133,.08); }

    /* Formulaires admin */
    .admin-form { background: var(--admin-surface); border: 1px solid var(--admin-border); padding: 24px; max-width: 600px; }
    .admin-form label { display: block; font-size: .72rem; letter-spacing: .12em; text-transform: uppercase; color: var(--admin-muted); margin-bottom: 6px; margin-top: 16px; }
    .admin-form label:first-child { margin-top: 0; }
    .admin-form input, .admin-form select, .admin-form textarea {
      width: 100%; padding: 10px 12px;
      background: rgba(255,255,255,.04);
      border: 1px solid var(--admin-border);
      color: var(--admin-text);
      font-size: .9rem;
      outline: none;
    }
    .admin-form input:focus, .admin-form select:focus { border-color: rgba(205,180,133,.4); }
    .admin-form button[type=submit] { margin-top: 20px; }

    /* Alert */
    .alert { padding: 12px 16px; margin-bottom: 20px; font-size: .85rem; border-left: 3px solid; }
    .alert.success { background: rgba(76,175,130,.08); border-color: var(--admin-green); color: var(--admin-green); }
    .alert.error { background: rgba(224,85,85,.08); border-color: var(--admin-red); color: var(--admin-red); }

    .section-title { font-size: .7rem; letter-spacing: .2em; text-transform: uppercase; color: var(--admin-muted); margin-bottom: 16px; padding-bottom: 10px; border-bottom: 1px solid var(--admin-border); }
  </style>
</head>
<body>

<aside class="admin-sidebar">
  <a class="admin-logo" href="/admin/index.php">
    <div class="admin-logo-mark">SW</div>
    <div class="admin-logo-text">
      <div class="admin-logo-title">SherWheels</div>
      <div class="admin-logo-sub">Admin</div>
    </div>
  </a>

  <nav class="admin-nav">
    <div class="admin-nav-section">Général</div>
    <a href="/admin/index.php" class="<?= basename($_SERVER['PHP_SELF']) === 'index.php' ? 'active' : '' ?>">
      Dashboard
    </a>

    <div class="admin-nav-section">Ventes</div>
    <a href="/admin/commandes.php" class="<?= basename($_SERVER['PHP_SELF']) === 'commandes.php' ? 'active' : '' ?>">
      Commandes
      <?php if ($commandesEnAttente > 0): ?>
        <span class="badge"><?= $commandesEnAttente ?></span>
      <?php endif; ?>
    </a>

    <div class="admin-nav-section">Catalogue</div>
    <a href="/admin/products.php" class="<?= basename($_SERVER['PHP_SELF']) === 'products.php' ? 'active' : '' ?>">
      Produits
    </a>
    <a href="/admin/stock.php" class="<?= basename($_SERVER['PHP_SELF']) === 'stock.php' ? 'active' : '' ?>">
      Stock
    </a>
    <a href="/admin/billeterie.php" class="<?= basename($_SERVER['PHP_SELF']) === 'billeterie.php' ? 'active' : '' ?>">
      Billetterie
    </a>

    <div class="admin-nav-section">Contenu</div>
    <a href="/admin/galerie.php" class="<?= basename($_SERVER['PHP_SELF']) === 'galerie.php' ? 'active' : '' ?>">
      Galerie
    </a>

    <div class="admin-nav-section">Utilisateurs</div>
    <a href="/admin/users.php" class="<?= basename($_SERVER['PHP_SELF']) === 'users.php' ? 'active' : '' ?>">
      Utilisateurs
      <span class="badge gold"><?= $totalUsers ?></span>
    </a>
  </nav>

  <div class="admin-footer">
    <a href="/index.php">← Voir le site</a>
    <a href="/logout.php">Déconnexion</a>
  </div>
</aside>

<div class="admin-main">
  <div class="admin-topbar">
    <h1><?= htmlspecialchars($adminTitle) ?></h1>
    <div class="admin-topbar-right">
      <span><?= htmlspecialchars($_SESSION['user_email'] ?? '') ?></span>
      <a href="/logout.php">Déconnexion</a>
    </div>
  </div>
  <div class="admin-content">
