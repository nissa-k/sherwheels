<?php
declare(strict_types=1);

function env(string $key, string $default = ''): string {

    // Variables Render / système
    $systemValue = getenv($key);

    if ($systemValue !== false && $systemValue !== '') {
        return $systemValue;
    }

    // Variables locales .env
    static $vars = null;

    if ($vars === null) {

        $vars = [];

        $path = __DIR__ . '/../.env';

        if (is_file($path)) {

            $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

            foreach ($lines as $line) {

                $line = trim($line);

                if ($line === '' || str_starts_with($line, '#')) {
                    continue;
                }

                [$k, $v] = array_pad(explode('=', $line, 2), 2, '');

                $vars[trim($k)] = trim($v);
            }
        }
    }

    return $vars[$key] ?? $default;
}

define('APP_ENV', env('APP_ENV', 'dev'));

define('DB_HOST', env('DB_HOST', 'localhost'));
define('DB_NAME', env('DB_NAME', 'sherwheels'));
define('DB_USER', env('DB_USER', 'root'));
define('DB_PASS', env('DB_PASS', ''));
define('DB_PORT', env('DB_PORT', '3306'));