<?php
session_start();
require_once __DIR__ . '/../includes/cart.php';

$id = (int)($_GET['id'] ?? 0);
$taille = (int)($_GET['taille'] ?? 0);

removeFromCart($id, $taille);

header('Location: /cart.php');
exit;