<?php
declare(strict_types=1);

function getCart(): array {
    return $_SESSION['cart'] ?? [];
}

function addToCart(int $productId, int $tailleId, int $qty = 1): void {
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    foreach ($_SESSION['cart'] as &$item) {
        if ($item['produit_id'] === $productId && $item['taille_id'] === $tailleId) {
            $item['quantite'] += $qty;
            return;
        }
    }

    $_SESSION['cart'][] = [
        'produit_id' => $productId,
        'taille_id' => $tailleId,
        'quantite' => $qty
    ];
}

function removeFromCart(int $productId, int $tailleId): void {
    foreach ($_SESSION['cart'] as $key => $item) {
        if ($item['produit_id'] === $productId && $item['taille_id'] === $tailleId) {
            unset($_SESSION['cart'][$key]);
        }
    }
}