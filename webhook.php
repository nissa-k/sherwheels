<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/includes/stripe.php';
require_once __DIR__ . '/config/db.php';

$pdo = db();

$payload = file_get_contents("php://input");
$sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';

try {
    $event = \Stripe\Webhook::constructEvent(
        $payload,
        $sig_header,
        $_ENV['STRIPE_WEBHOOK_SECRET']
    );
} catch (\UnexpectedValueException $e) {
    http_response_code(400);
    exit('Payload invalide');
} catch (\Stripe\Exception\SignatureVerificationException $e) {
    http_response_code(400);
    exit('Signature invalide');
}

if ($event->type === 'checkout.session.completed') {

    $session = $event->data->object;

    $stripeSessionId = $session->id;
    $stmt = $pdo->prepare("SELECT id FROM orders WHERE stripe_session_id = ?");
    $stmt->execute([$stripeSessionId]);

    if ($stmt->fetch()) {
        http_response_code(200);
        exit('Déjà traité');
    }
    $userId = $session->metadata->user_id ?? null;

    if (!$userId) {
        http_response_code(400);
        exit('User manquant');
    }

    $total = $session->amount_total;

    $stmt = $pdo->prepare("
        INSERT INTO orders (user_id, total, status, stripe_session_id)
        VALUES (?, ?, 'paid', ?)
    ");

    $stmt->execute([
        $userId,
        $total,
        $stripeSessionId
    ]);

}
http_response_code(200);
echo 'OK';