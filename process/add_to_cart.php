<?php
require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../includes/cart.php';
require_once __DIR__ . '/../config/db.php';

$pdo = db();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../shop.php');
    exit;
}

$produit_id = filter_input(INPUT_POST, 'produit_id', FILTER_VALIDATE_INT);
$taille_id  = filter_input(INPUT_POST, 'taille_id', FILTER_VALIDATE_INT);
$type       = $_POST['type'] ?? 'produit';
$quantite   = filter_input(INPUT_POST, 'quantite', FILTER_VALIDATE_INT) ?? 1;
$concert    = isset($_POST['concert']) && $_POST['concert'] == '1' ? 1 : 0;
if ($quantite < 1) $quantite = 1;

if (!$produit_id || !$taille_id) {
    header('Location: ../shop.php?error=invalid');
    exit;
}

if ($type === 'billet') {
    $stmt = $pdo->prepare("SELECT id_billet, places_disponibles, prix_concert FROM billeterie WHERE id_billet = ? AND is_active = 1");
    $stmt->execute([$produit_id]);
    $billet = $stmt->fetch();

    if (!$billet) {
        header('Location: ../billetterie.php?error=invalid');
        exit;
    }

    if ($billet['places_disponibles'] <= 0) {
        header('Location: ../billetterie.php?error=complet');
        exit;
    }

    if ($quantite > $billet['places_disponibles']) {
        header('Location: ../billetterie.php?error=complet');
        exit;
    }

    $prix_concert = $concert ? (float)$billet['prix_concert'] : 0;

} else {
    $stmt = $pdo->prepare("SELECT id FROM produits WHERE id = ?");
    $stmt->execute([$produit_id]);

    if (!$stmt->fetch()) {
        header('Location: ../shop.php?error=product');
        exit;
    }

    $prix_concert = 0;
}

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$key = $produit_id . '_' . $taille_id . ($concert ? '_concert' : '');

if (isset($_SESSION['cart'][$key])) {
    $_SESSION['cart'][$key]['quantite'] += $quantite;
} else {
    $_SESSION['cart'][$key] = [
        'produit_id'   => $produit_id,
        'taille_id'    => $taille_id,
        'quantite'     => $quantite,
        'type'         => $type,
        'concert'      => $concert,
        'prix_concert' => $prix_concert
    ];
}

header('Location: ../cart.php');
exit;