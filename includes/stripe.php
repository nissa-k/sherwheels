<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Stripe\Stripe;

Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);