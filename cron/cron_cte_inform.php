<?php
$mensagem = '';
$dadosCertificado = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['certificado']) && $_FILES['certificado']['error'] === UPLOAD_ERR_OK) {
        $senha = $_POST['senha'] ?? '';

        $arquivoTemporario = $_FILES['certificado']['tmp_name'];
        $conteudo = file_get_contents($arquivoTemporario);

        $certs = [];
        if (openssl_pkcs12_read($conteudo, $certs, $senha)) {
            $certInfo = openssl_x509_parse($certs['cert']);

            // Razão Social
            $razao = $certInfo['subject']['CN'] ?? 'Desconhecido';

            // Validade
            $validade = date('Y-m-d H:i:s', $certInfo['validTo_time_t']);

            // CNPJ (procurar OID 2.16.76.1.3.3)
            $cnpj = 'Não encontrado';
            if (isset($certInfo['subject']['2.16.76.1.3.3'])) {
                $cnpj = $certInfo['subject']['2.16.76.1.3.3'];
            } else {
                foreach ($certInfo['subject'] as $key => $value) {
                    if (strpos($key, '2.16.76.1.3.3') !== false) {
                        $cnpj = $value;
                        break;
                    }
                }
            }

            $dadosCertificado = [
                'Razão Social' => $razao,
                'Validade' => $validade,
                'CNPJ' => $cnpj
            ];

        } else {
            $mensagem = '❌ Não foi possível ler o certificado. Verifique a senha.';
        }

        // ✅ Remover o arquivo temporário após o processamento
        if (file_exists($arquivoTemporario)) {
            unlink($arquivoTemporario);
        }

    } else {
        $mensagem = '❌ Erro ao enviar o certificado.';
    }
}
?>

<h2>🔐 Enviar Certificado Digital A1 (.pfx)</h2>
<form method="POST" enctype="multipart/form-data">
    <label>Certificado (.pfx):</label><br>
    <input type="file" name="certificado" accept=".pfx" required><br><br>

    <label>Senha do certificado:</label><br>
    <input type="password" name="senha" required><br><br>

    <button type="submit">Enviar</button>
</form>

<?php if (!empty($mensagem)): ?>
    <p style="color:red;"><?= $mensagem ?></p>
<?php endif; ?>

<?php if (!empty($dadosCertificado)): ?>
    <h3>📄 Dados do Certificado:</h3>
    <ul>
        <?php foreach ($dadosCertificado as $campo => $valor): ?>
            <li><strong><?= $campo ?>:</strong> <?= htmlspecialchars($valor) ?></li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>
