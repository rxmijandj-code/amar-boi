
<?php

function envv(string $key, ?string $default = null): ?string {
    $value = getenv($key);
    return ($value === false || $value === '') ? $default : $value;
}

function db(): PDO {
    static $pdo = null;

    if ($pdo instanceof PDO) return $pdo;

    $host = envv('DB_HOST');
    $name = envv('DB_NAME');
    $user = envv('DB_USER');
    $pass = envv('DB_PASSWORD');
    $port = envv('DB_PORT', '3306');

    if (!$host || !$name || !$user || !$pass) {
        throw new RuntimeException('Database configuration is incomplete.');
    }

    $dsn = "mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4";

    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::MYSQL_ATTR_SSL_CA => __DIR__ . '/ca.pem',
        PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => true,
    ];

    $pdo = new PDO($dsn, $user, $pass, $options);

    return $pdo;
}
