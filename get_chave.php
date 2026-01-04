<?php
echo OPENSSL_VERSION_TEXT . PHP_EOL;
echo 'deploy-003';

$host = 'a8484c4s4cgoskk0g8scsk08';
$port = 3306;

$db   = 'b2x_sefaz';
$user = 'b2x_sefaz';
$pass = '@Pl68267713210';

$dsn = "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4";

try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);

    echo "<br>✅ Conectado com sucesso!<br><br>";

    // 🔹 Listar todas as tabelas
    $stmt = $pdo->query("SHOW TABLES");
    $tabelas = $stmt->fetchAll();

    echo "📋 Tabelas encontradas:<br>";

    foreach ($tabelas as $row) {
        // O nome da coluna vem como Tables_in_nome_do_banco
        echo "- " . array_values($row)[0] . "<br>";
    }

} catch (PDOException $e) {
    echo "❌ Erro na conexão: " . $e->getMessage();
}
