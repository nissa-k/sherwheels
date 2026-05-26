<?php
require_once __DIR__ . '/includes/bootstrap.php';

// vider le panier après paiement
unset($_SESSION['cart']);
?>

<h1>Paiement réussi</h1>
<p>Merci pour ta commande !</p>

<a href="index.php">Retour à l'accueil</a>