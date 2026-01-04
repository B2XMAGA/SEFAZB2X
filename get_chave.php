<?php
echo OPENSSL_VERSION_TEXT . PHP_EOL;

echo 'deploy-001';


$host = 'mariadb'; // nome do service
$port = 3306;

$db   = getenv('MARIADB_DATABASE');
$user = getenv('MARIADB_USER');
$pass = getenv('MARIADB_PASSWORD');

$dsn = "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4";

try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);

    echo "✅ Conectado com sucesso!";
} catch (PDOException $e) {
    echo "❌ Erro na conexão: " . $e->getMessage();
}
