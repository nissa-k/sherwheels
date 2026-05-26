<?php
require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../cart.php');
    exit;
}

$key      = $_POST['key'] ?? null;
$quantite = filter_input(INPUT_POST, 'quantite', FILTER_VALIDATE_INT);

if ($key && $quantite && $quantite >= 1 && isset($_SESSION['cart'][$key])) {
    $_SESSION['cart'][$key]['quantite'] = $quantite;

    if (($_SESSION['cart'][$key]['type'] ?? '') === 'billet') {
        $concert = isset($_POST['concert']) && $_POST['concert'] == '1' ? 1 : 0;
        $_SESSION['cart'][$key]['concert'] = $concert;

        if ($concert) {
            $pdo  = db();
            $stmt = $pdo->prepare("SELECT prix_concert FROM billeterie WHERE id_billet = ?");
            $stmt->execute([$_SESSION['cart'][$key]['produit_id']]);
            $row = $stmt->fetch();
            $_SESSION['cart'][$key]['prix_concert'] = (float)($row['prix_concert'] ?? 0);
        } else {
            $_SESSION['cart'][$key]['prix_concert'] = 0;
        }
    }
}

header('Location: ../cart.php');
exit;