<?php

/**
 *
 * @param string|null $param
 * @return string
 */

function aes_encrypt($text, $passphrase) {
    $salt = openssl_random_pseudo_bytes(8);

    $salted = $dx = '';
    while (strlen($salted) < 48) {
        $dx = md5($dx . $passphrase . $salt, true);
        $salted .= $dx;
    }

    $key = substr($salted, 0, 32);
    $iv = substr($salted, 32, 16);

    $encrypted = openssl_encrypt($text, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);
    return base64_encode('Salted__' . $salt . $encrypted);
}

function aes_decrypt($encryptedBase64, $passphrase) {
    $data = base64_decode($encryptedBase64);

    if (substr($data, 0, 8) !== 'Salted__') {
        return null; // formato inválido
    }

    $salt = substr($data, 8, 8);
    $encrypted = substr($data, 16);

    $salted = $dx = '';
    while (strlen($salted) < 48) {
        $dx = md5($dx . $passphrase . $salt, true);
        $salted .= $dx;
    }

    $key = substr($salted, 0, 32);
    $iv = substr($salted, 32, 16);

    return openssl_decrypt($encrypted, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);
}



function site(string $param = null): string {

    if ($param && !empty(SITE[$param])) {
        return SITE[$param];
    }

    return SITE["root"];
}

function linhaDigitavelParaCodigoBarras($linha) {
    // Remove os espaços e pontos da linha digitável
    $linha = str_replace(['.', ' '], '', $linha);

    // Extrai os campos da linha digitável
    $campo1 = substr($linha, 0, 9);    // Banco + Moeda + Campo Livre
    $campo2 = substr($linha, 10, 10);  // Campo Livre
    $campo3 = substr($linha, 21, 10);  // Campo Livre
    $campo4 = substr($linha, 32, 1);   // Dígito Verificador Geral
    $campo5 = substr($linha, 33, 14);  // Fator de Vencimento + Valor

    // Concatena os campos para formar o código de barras
    $codigo_barras = substr($campo1, 0, 4) . $campo4 . substr($campo5, 0, 14) . substr($campo1, 4, 5) . $campo2 . $campo3;

    return $codigo_barras;
}


/**
 * @param float $value
 * @return string
 */
function format_money(float $value): string {
    return number_format($value, 2, ',', '.');
}

function format_date_br($date) {
    return date('d/m/Y', strtotime($date));
}

function format_date_usa($date) {
    return date('Y-m-d', strtotime($date));
}

function format_date_bb($date) {
    return date('d.m.Y', strtotime($date));
}

function generateUUID() {
    return sprintf(
        '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        // 32 bits para o timestamp inferior
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),

        // 16 bits para o timestamp intermediário
        mt_rand(0, 0xffff),

        // 12 bits para o timestamp superior
        mt_rand(0, 0x0fff) | 0x4000,

        // 8 bits para as variantes / versão
        mt_rand(0, 0x3fff) | 0x8000,

        // 48 bits para o nodo
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff)
    );
}


function decode_brcode($brcode) {
    $n = 0;
    while ($n < strlen($brcode)) {
        $codigo = substr($brcode, $n, 2);
        $n += 2;
        $tamanho = intval(substr($brcode, $n, 2));
        if (!is_numeric($tamanho)) {
            return false;
        }
        $n += 2;
        $valor = substr($brcode, $n, $tamanho);
        $n += $tamanho;
        if (preg_match("/^[0-9]{4}.+$/", $valor) && ($codigo != 54)) {
            $retorno[$codigo] = decode_brcode($valor);
        } else {
            $retorno[$codigo] = "$valor";
        }
    }
    return $retorno;
}

function clearDoc($value) {
    $arrayInit = ['.', '/', '-'];
    $arrayFinish = ['', '', ''];
    return str_replace($arrayInit, $arrayFinish, $value);
}

function getCopyPaste($payload) {
    $url = 'https://gerarqrcodepix.com.br/api/v1?nome=.&cidade=.&saida=br&location=' . $payload;

    $json = json_decode(file_get_contents($url), true);

    return $json['brcode'];
}

function getQRCode($name, $city, $payload, $type) {
    $explodeName = explode(' ', $name);
    $explodeCity = explode(' ', $city);
    $url = 'https://gerarqrcodepix.com.br/api/v1?nome=' . trim($explodeName['0']) . '&cidade=' . trim($explodeCity['0']) . '&saida=qr&location=' . $payload;

    if ($type == 'png') {
        if (!is_dir(__DIR__ . '/../shared/qrcode/' . date('Y'))) {
            mkdir(__DIR__ . '/../shared/qrcode/' . date('Y'), 0755, true);
        }
        if (!is_dir(__DIR__ . '/../shared/qrcode/' . date('Y') . '/' . date('m'))) {
            mkdir(__DIR__ . '/../shared/qrcode/' . date('Y') . '/' . date('m'), 0755, true);
        }
        if (!is_dir(__DIR__ . '/../shared/qrcode/' . date('Y') . '/' . date('m') . '/' . date('d'))) {
            mkdir(__DIR__ . '/../shared/qrcode/' . date('Y') . '/' . date('m') . '/' . date('d'), 0755, true);
        }
        $nameArchive = md5(date('YmdHis') . rand(100000, 999999)) . '.png';
        $urlArchive = file_get_contents($url);

        file_put_contents(__DIR__ . '/../shared/qrcode/' . date('Y') . '/' . date('m') . '/' . date('d') . '/' . $nameArchive, $urlArchive);

        return URL_BASE . '/shared/qrcode/' . date('Y') . '/' . date('m') . '/' . date('d') . '/' . $nameArchive;
    }

    if ($type == 'base64') {
        $urlArchive = file_get_contents($url);
        return base64_encode($urlArchive);
    }

    $urlArchive = file_get_contents($url);
    return base64_encode($urlArchive);
}

function line_barcode($line) {
    $init = array('.', ' ');
    $finish = array('', '');
    return preg_replace('/(\d{4})(\d{5})\d{1}(\d{10})\d{1}(\d{10})\d{1}(\d{15})/', '$1$5$2$3$4', str_replace($init, $finish, $line));
}

function mask($mask, $str) {

    $str = str_replace(" ", "", $str);

    for ($i = 0; $i < strlen($str); $i++) {
        $mask[strpos($mask, "#")] = $str[$i];
    }

    return $mask;
}

function mask_line($str) {
    $mask = '#####.##### #####.###### #####.###### # ##############';
    $str = str_replace(" ", "", $str);
    for ($i = 0; $i < strlen($str); $i++) {
        $mask[strpos($mask, "#")] = $str[$i];
    }
    return $mask;
}

function fbarcode($line) {
    $init = new \sleifer\boleto\Boleto($line);

    if (!$init->getError()) {
        return $init->getBarCode();
    } else {
        return $init->getError();
    }
}

function modulo100($num) {
    $soma = 0;
    $peso = 2;

    for ($i = strlen($num) - 1; $i >= 0; $i--) {
        $calc = $num[$i] * $peso;
        $soma += array_sum(str_split($calc));
        $peso = ($peso == 2) ? 1 : 2;
    }

    $resto = $soma % 10;
    return ($resto == 0) ? 0 : 10 - $resto;
}

function codigoBarrasParaLinhaDigitavel(string $codigo) {
    // Remove qualquer caractere não numérico
    $codigo = preg_replace('/\D/', '', $codigo);

    if (strlen($codigo) !== 44) {
        throw new Exception('Código de barras inválido. Deve conter 44 dígitos.');
    }

    // Campo 1
    $campo1 = substr($codigo, 0, 4) . substr($codigo, 19, 5);
    $campo1 .= modulo100($campo1);

    // Campo 2
    $campo2 = substr($codigo, 24, 10);
    $campo2 .= modulo100($campo2);

    // Campo 3
    $campo3 = substr($codigo, 34, 10);
    $campo3 .= modulo100($campo3);

    // Campo 4 (DV geral)
    $campo4 = substr($codigo, 4, 1);

    // Campo 5 (fator vencimento + valor)
    $campo5 = substr($codigo, 5, 14);

    return
        substr($campo1, 0, 5) . '.' . substr($campo1, 5) . ' ' .
        substr($campo2, 0, 5) . '.' . substr($campo2, 5) . ' ' .
        substr($campo3, 0, 5) . '.' . substr($campo3, 5) . ' ' .
        $campo4 . ' ' . $campo5;
}

function replace_carne($bank, $billet, $html) {
    $pixData = '';
    $usePix = '';
    $imgBank = URL_BASE . '/templates/img/logobb.jpg';
    if ($bank == '104') {
        $imgBank = URL_BASE . '/templates/img/logocaixa.jpg';
    }
    if ($bank == '001') {
        $imgBank = URL_BASE . '/templates/img/logobb.jpg';
    }
    if ($bank == '999') {
        $imgBank = URL_BASE . '/templates/img/example-bank.jpg';
    }
    if ($bank == '748') {
        $imgBank = URL_BASE . '/templates/img/logosicredi.jpg';
    }
    if ($bank == '756') {
        $imgBank = URL_BASE . '/templates/img/logosicoob.png';
    }
    if ($bank == '004') {
        $imgBank = URL_BASE . '/templates/img/logonordeste.png';
    }

    $init   = [
        '{IMG_BANCO}',
        '{DATA_VENCIMENTO}',
        '{AGENCIA}',
        '{CODIGO_BENEFICIARIO}',
        '{VALOR}',
        '{NOSSO_NUMERO}',
        '{NUMERO_DOCUMENTO}',
        '{DADOS_SACADO}',
        '{LINHA_DIGITAVEL}',
        '{DATA_DOCUMENTO}',
        '{ESPECIE}',
        '{ACEITE}',
        '{DATA_PROCESSAMENTO}',
        '{INFO_PIX}',
        '{DADOS_INSTRUCAO}',
        '{DESCRICAO}',
        '{QRCODE_PIX}',
        '{DADOS_PAGADOR}',
        '{DADOS_BENEFICIARIO}',
        '{CODIGO_BARRAS}',
        '{URL_SISTEMA_VAR}',
        '{CODE_BANCO}',
        '{BENEFICIARIO_NOME}',
        '{SACADO_ENDERECO01}',
        '{SACADO_ENDERECO02}',
        '{BENEFICIARIO_DOC}',
        '{DESCONTO_ABATIMENTO}',
        '{OUTRAS_DEDUCOES}',
        '{MULTA}',
        '{OUTROS_ACRESCIMOS}',
        '{ENDERECO1_BENEFICIARIO}',
        '{ENDERECO2_BENEFICIARIO}',
        '{NOME_SACADO}',
        '{DOC_SACADO}',
        '{DESCRICAO_BOLETO}',
        '{JUROS_MULTA_BOLETO}'
    ];
    $finish = [
        $imgBank,
        $html['dataVencimento'],
        $html['agencia'],
        $html['cedente'],
        $html['valor'],
        $html['nossoNumero'],
        $html['numeroDocumento'],
        $html['dadosSacado'],
        mask_line($html['linhaDigitavel']),
        $html['dataDocumento'],
        $html['especie'],
        $html['aceite'],
        $html['dataProcessamento'],
        $pixData,
        $html['informativo'],
        $html['descricao'],
        $html['qrCodePix'],
        $html['dadosSacado'],
        $html['dadosBeneficiario'],
        str_replace('<?xml version="1.0" standalone="no" ?>', '', fbarcode($html['linhaDigitavel'])),
        URL_BASE,
        $bank,
        $html['dadosBeneficiario'],
        $html['endereco01Sacado'],
        $html['endereco02Sacado'],
        $html['docBeneficiario'],
        $html['descontoAbatimento'],
        $html['outrasDeducoes'],
        $html['multa'],
        $html['outrosAcrescimos'],
        $html['endereco01Beneficiario'],
        $html['endereco02Beneficiario'],
        $html['nameSacado'],
        $html['docSacado'],
        $html['descricaoBoleto'],
        $html['valorJurosMulta']
    ];

    return str_replace($init, $finish, $billet);
}


function transformCnpj($arr) {

    $ret['atualizado']              = $arr['updated'];
    $ret['cnpj']                    = $arr['taxId'];
    $ret['nome_fantasia']           = $arr['alias'];
    $ret['data_abertura']           = $arr['founded'];
    $ret['matriz']                  = $arr['head'];
    $ret['data_situacao_cadastral'] = $arr['statusDate'];

    $ret['status']['id']        = $arr['status']['id'];
    $ret['status']['descricao'] = $arr['status']['text'];

    $ret['motivo_situacao']['id']        = $arr['reason']['id'];
    $ret['motivo_situacao']['descricao'] = $arr['reason']['text'];

    $ret['data_situacao_especial'] = $arr['specialDate'];

    $ret['motivo_especial']['id']        = $arr['special']['id'];
    $ret['motivo_especial']['descricao'] = $arr['special']['text'];

    $ret['endereco']['codigo_ibge']     = $arr['address']['municipality'];
    $ret['endereco']['cep']             = $arr['address']['zip'];
    $ret['endereco']['logradouro']      = $arr['address']['street'];
    $ret['endereco']['bairro']          = $arr['address']['district'];
    $ret['endereco']['cidade']          = $arr['address']['city'];
    $ret['endereco']['uf']              = $arr['address']['state'];
    $ret['endereco']['numero']          = $arr['address']['number'];
    $ret['endereco']['complemento']     = $arr['address']['details'];
    $ret['endereco']['latitude']        = $arr['address']['latitude'];
    $ret['endereco']['longitude']       = $arr['address']['longitude'];

    $ret['endereco']['pais']['id']          = $arr['address']['country']['id'];
    $ret['endereco']['pais']['descricao']   = $arr['address']['country']['name'];

    if (isset($arr['phones'])) {
        for ($x = 0; $x < count($arr['phones']); $x++) {
            $ret['telefones'][$x]['ddd']      = $arr['phones'][$x]['area'];
            $ret['telefones'][$x]['numero']   = $arr['phones'][$x]['number'];
        }
    }

    if (isset($arr['emails'])) {
        for ($x = 0; $x < count($arr['emails']); $x++) {
            $ret['emails'][$x]['email']     = $arr['emails'][$x]['address'];
            $ret['emails'][$x]['dominio']   = $arr['emails'][$x]['domain'];
        }
    }

    $ret['atividade_principal']['id']        = $arr['mainActivity']['id'];
    $ret['atividade_principal']['descricao'] = $arr['mainActivity']['text'];

    if (isset($arr['sideActivities'])) {
        for ($x = 0; $x < count($arr['sideActivities']); $x++) {
            $ret['cnae'][$x]['id']          = $arr['sideActivities'][$x]['id'];
            $ret['cnae'][$x]['descricao']   = $arr['sideActivities'][$x]['text'];
        }
    }

    if (isset($arr['registrations'])) {
        for ($x = 0; $x < count($arr['registrations']); $x++) {
            $ret['ie'][$x]['numero']                = $arr['registrations'][$x]['number'];
            $ret['ie'][$x]['uf']                    = $arr['registrations'][$x]['state'];
            $ret['ie'][$x]['habilitada']            = $arr['registrations'][$x]['enabled'];
            $ret['ie'][$x]['data_situacao']         = $arr['registrations'][$x]['statusDate'];
            $ret['ie'][$x]['status']['id']          = $arr['registrations'][$x]['status']['id'];
            $ret['ie'][$x]['status']['descricao']   = $arr['registrations'][$x]['status']['text'];

            $ret['ie'][$x]['tipo']['id']            = $arr['registrations'][$x]['type']['id'];
            $ret['ie'][$x]['tipo']['descricao']     = $arr['registrations'][$x]['type']['text'];
        }
    }

    $ret['empresa']['raiz']             = $arr['company']['id'];
    $ret['empresa']['razao_social']     = $arr['company']['name'];
    $ret['empresa']['jurisdicao']       = $arr['company']['jurisdiction'];
    $ret['empresa']['capital_social']   = $arr['company']['equity'];

    $ret['empresa']['natureza_juridica']['id']          = $arr['company']['nature']['id'];
    $ret['empresa']['natureza_juridica']['descricao']   = $arr['company']['nature']['text'];

    $ret['empresa']['porte']['id']          = $arr['company']['size']['id'];
    $ret['empresa']['porte']['sigla']       = $arr['company']['size']['acronym'];
    $ret['empresa']['porte']['descricao']   = $arr['company']['size']['text'];

    $ret['empresa']['simples']['optante']               = $arr['company']['simples']['optant'];
    $ret['empresa']['simples']['data_inclusao']         = $arr['company']['simples']['since'];
    if (isset($arr['company']['history'])) {
        for ($x = 0; $x < count($arr['company']['history']); $x++) {
            $ret['empresa']['simples']['historico'][$x]['de']           = $arr['company']['simples']['history'][$x]['from'];
            $ret['empresa']['simples']['historico'][$x]['ate']          = $arr['company']['simples']['history'][$x]['until'];
            $ret['empresa']['simples']['historico'][$x]['descricao']    = $arr['company']['simples']['history'][$x]['text'];
        }
    }

    $ret['empresa']['mei']['optante']               = $arr['company']['simei']['optant'];
    $ret['empresa']['mei']['data_inclusao']         = $arr['company']['simei']['since'];
    if (isset($arr['company']['history'])) {
        for ($x = 0; $x < count($arr['company']['history']); $x++) {
            $ret['empresa']['mei']['historico'][$x]['de']           = $arr['company']['simei']['history'][$x]['from'];
            $ret['empresa']['mei']['historico'][$x]['ate']          = $arr['company']['simei']['history'][$x]['until'];
            $ret['empresa']['mei']['historico'][$x]['descricao']    = $arr['company']['simei']['history'][$x]['text'];
        }
    }

    if (isset($arr['company']['members'])) {
        for ($x = 0; $x < count($arr['company']['members']); $x++) {
            $ret['empresa']['socios'][$x]['data_entrada']           = $arr['company']['members'][$x]['since'];

            $ret['empresa']['socios'][$x]['pessoa']['id']           = $arr['company']['members'][$x]['person']['id'];
            $ret['empresa']['socios'][$x]['pessoa']['tipo']         = $arr['company']['members'][$x]['person']['type'];
            $ret['empresa']['socios'][$x]['pessoa']['nome']         = $arr['company']['members'][$x]['person']['name'];
            $ret['empresa']['socios'][$x]['pessoa']['documento']    = $arr['company']['members'][$x]['person']['taxId'];
            $ret['empresa']['socios'][$x]['pessoa']['idade']        = $arr['company']['members'][$x]['person']['age'];

            $ret['empresa']['socios'][$x]['pessoa']['pais']['id']           = $arr['company']['members'][$x]['country']['id'];
            $ret['empresa']['socios'][$x]['pessoa']['pais']['descricao']    = $arr['company']['members'][$x]['country']['name'];

            $ret['empresa']['socios'][$x]['pessoa']['qualificacao']['id']           = $arr['company']['members'][$x]['role']['id'];
            $ret['empresa']['socios'][$x]['pessoa']['qualificacao']['descricao']    = $arr['company']['members'][$x]['role']['text'];
        }
    }

    return $ret;
}


function encryptString($data, $password) {
    $method = 'AES-256-CBC'; // Algoritmo de criptografia
    $key = hash('sha256', $password, true); // Deriva a chave da senha
    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($method)); // Gera um IV aleatório

    $encrypted = openssl_encrypt($data, $method, $key, 0, $iv);
    if ($encrypted === false) {
        throw new Exception('Falha na criptografia.');
    }

    // Retorna o IV concatenado com o texto criptografado, codificado em base64
    return base64_encode($iv . $encrypted);
}

function decryptString($data, $password) {
    $method = 'AES-256-CBC';
    $key = hash('sha256', $password, true);
    $data = base64_decode($data);

    $iv_length = openssl_cipher_iv_length($method);
    $iv = substr($data, 0, $iv_length); // Extrai o IV
    $encrypted_data = substr($data, $iv_length); // Extrai o texto criptografado

    $decrypted = openssl_decrypt($encrypted_data, $method, $key, 0, $iv);
    if ($decrypted === false) {
        throw new Exception('Falha na descriptografia.');
    }

    return $decrypted;
}
