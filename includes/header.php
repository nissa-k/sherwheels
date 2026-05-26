<?php
declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

$pageTitle = $pageTitle ?? 'SherWheels Festival';
$pageDescription = $pageDescription ?? "SherWheels Festival : le rendez-vous des passionnés auto/moto — invités, stands, ateliers et masterclass.";
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <meta name="description" content="<?= htmlspecialchars($pageDescription, ENT_QUOTES, 'UTF-8') ?>">
  <title><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></title>

  <meta property="og:title" content="<?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?>">
  <meta property="og:description" content="<?= htmlspecialchars($pageDescription, ENT_QUOTES, 'UTF-8') ?>">
  <meta property="og:type" content="website">

  <!-- Police Montserrat -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="/assets/css/style.css">

  <style>
    body{
      font-family: 'Montserrat', sans-serif;
    }
  </style>
</head>

<body>

<header class="site-header" data-header>
  <div class="container header-inner">

    <?php if(isset($_SESSION['user_id'])): ?>
      <div class="header-left">
        <img src="/assets/img/icone.png" alt="" class="header-icon">

        <span class="header-left-text">
          <?= htmlspecialchars($_SESSION['user_email'], ENT_QUOTES, 'UTF-8') ?>

          <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
            <span class="admin-badge">ADMIN</span>
          <?php endif; ?>
        </span>
      </div>

    <?php else: ?>
      <a class="header-left" href="/login.php" aria-label="Mon compte">
        <img src="/assets/img/icone.png" alt="icone de connexion" class="header-icon" aria-hidden="true" style="filter:brightness(0) invert(1);">
        <span class="header-left-text">Mon compte</span>
      </a>
    <?php endif; ?>

    <a class="header-brand" href="/index.php" aria-label="Retour à l'accueil">
      <img src="/assets/img/logo/logo.png" alt="Logo Sher Wheels" style="width:52px;height:52px;object-fit:contain;display:block; filter:brightness(0) invert(1);">
      <span class="brand-stack">
        <span class="brand-title">SherWheels</span>
        <span class="brand-sub">Festival</span>
      </span>
    </a>

<div class="header-right">
  <a class="header-panier" href="/cart.php" aria-label="Mon panier" style="position:relative;">
    <img src="/assets/img/panier.png" alt="icone du panier" class="header-icon" aria-hidden="true">
    <?php 
    $cartCount = 0;
    if (!empty($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $item) {
            $cartCount += $item['quantite'];
        }
    }
    ?>
    <?php if ($cartCount > 0): ?>
      <span style="
                position: absolute;
                top: -4px;
                right: -4px;
                width: 18px;
                height: 18px;
                background: #cdb485;
                color: #111;
                font-size: 0.65rem;
                font-weight: 900;
                border-radius: 999px;
                display: flex;
                align-items: center;
                justify-content: center;
                line-height: 1; "><?= $cartCount ?></span>

    <?php endif; ?>
  </a>
  <button id="menuBtn" type="button" aria-expanded="false" aria-controls="mainMenu">
    <span class="burger" aria-hidden="true">
      <span></span><span></span><span></span>
    </span>
  </button>
</div>

  </div>

  <nav class="menu-panel" id="mainMenu" aria-label="Navigation principale" hidden>
    <div class="container menu-panel-inner">

      <a href="/programme.php">Programme</a>
      <a href="/billetterie.php">Billetterie</a>
      <a href="/shop.php">Boutique</a>
      <a href="/map.php">Carte</a>
      <a href="/galerie.php">Galerie</a>

      <?php if(isset($_SESSION['user_id'])): ?>

        <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
          <a href="/admin/index.php">Admin</a>
        <?php endif; ?>

        <a href="/logout.php">Déconnexion</a>

      <?php endif; ?>

    </div>
  </nav>
</header>

<main id="contenu" tabindex="-1">