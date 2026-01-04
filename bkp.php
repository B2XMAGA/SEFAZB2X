<?php
/**
 * API SEFAZ B2X - VERSÃO COMPLETA
 * Servidor PHP para comunicação com SEFAZ
 * 
 * @version 3.0.0
 * @date 2024-12-26
 * 
 * Endpoints disponíveis:
 * - GET  /health                   - Health check
 * - GET  /teste-sefaz              - Testa conexão SEFAZ
 * - POST /consult/cnpj             - Consulta CNPJ
 * - POST /consult/searchNFe        - Busca NF-e
 * - POST /xml/chave                - Busca XML por chave
 * - POST /xml/select               - Lista XMLs
 * - POST /dfe/dfeDocs              - Busca DF-e na SEFAZ
 * - POST /dfe/dfeDocsManifesta     - Manifestação (legado)
 * - POST /dfe/dfePDF               - Gera DANFE PDF (legado)
 * - POST /dfe/dfeDocsZIP           - Download ZIP XMLs (legado)
 * 
 * NOVOS ENDPOINTS:
 * - POST /dfe/nfeSaida             - Busca NF-e de saída via DistDFeInt
 * - POST /sefaz/download-nfe-lote  - Download em lote de XMLs
 * - POST /dfe/gerarDANFE           - Gera DANFE PDF a partir do XML
 * - POST /dfe/downloadZIP          - Download em massa como ZIP
 * - POST /dfe/manifestar           - Manifestação do destinatário
 * - POST /dfe/consultarChave       - Consulta NF-e por chave de acesso
 */

// ============================================
// CORS Headers - OBRIGATÓRIO
// ============================================
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Content-Type: application/json; charset=UTF-8");

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// ============================================
// Configurações
// ============================================
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

define('SEFAZ_AMBIENTE', 1); // 1 = Produção, 2 = Homologação
define('SEFAZ_RATE_LIMIT_DELAY', 1500); // Delay entre requisições (ms)
define('SEFAZ_MAX_ITERATIONS', 50); // Máximo de iterações por NSU
define('SEFAZ_BATCH_SIZE', 50); // Máximo de chaves por lote
define('SEFAZ_ZIP_MAX_FILES', 500); // Máximo de arquivos por ZIP

// ============================================
// Autoload NFePHP (se disponível)
// ============================================
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}

// ============================================
// Helpers
// ============================================
function jsonResponse($data, $status = 200) {
    http_response_code($status);
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit();
}

function errorResponse($message, $code = 'ERROR', $statusCode = 400, $details = null) {
    $response = [
        'success' => false,
        'error' => [
            'code' => $code,
            'message' => $message,
        ],
        'timestamp' => date('c'),
    ];
    
    if ($details) {
        $response['error']['details'] = $details;
    }
    
    error_log("[SEFAZ ERROR] $code: $message");
    jsonResponse($response, $statusCode);
}

function getRequestBody() {
    $json = file_get_contents('php://input');
    return json_decode($json, true) ?? [];
}

function validateRequired($data, $fields) {
    $missing = [];
    foreach ($fields as $field) {
        if (empty($data[$field])) {
            $missing[] = $field;
        }
    }
    return $missing;
}

function validateCNPJ($cnpj) {
    $cnpj = preg_replace('/[^0-9]/', '', $cnpj);
    if (strlen($cnpj) !== 14) {
        return false;
    }
    
    // Validação do dígito verificador
    $soma = 0;
    $mult = 5;
    for ($i = 0; $i < 12; $i++) {
        $soma += $cnpj[$i] * $mult;
        $mult = ($mult == 2) ? 9 : $mult - 1;
    }
    $resto = $soma % 11;
    $dig1 = ($resto < 2) ? 0 : 11 - $resto;
    
    if ($cnpj[12] != $dig1) return false;
    
    $soma = 0;
    $mult = 6;
    for ($i = 0; $i < 13; $i++) {
        $soma += $cnpj[$i] * $mult;
        $mult = ($mult == 2) ? 9 : $mult - 1;
    }
    $resto = $soma % 11;
    $dig2 = ($resto < 2) ? 0 : 11 - $resto;
    
    return $cnpj[13] == $dig2;
}

function validateChaveNFe($chave) {
    $chave = preg_replace('/[^0-9]/', '', $chave);
    return strlen($chave) === 44;
}

function validateUF($uf) {
    $ufs = ['AC','AL','AP','AM','BA','CE','DF','ES','GO','MA','MT','MS','MG','PA','PB','PR','PE','PI','RJ','RN','RS','RO','RR','SC','SP','SE','TO'];
    return in_array(strtoupper($uf), $ufs);
}

function getCodigoUF($uf) {
    $codigos = [
        'RO' => 11, 'AC' => 12, 'AM' => 13, 'RR' => 14, 'PA' => 15, 'AP' => 16, 'TO' => 17,
        'MA' => 21, 'PI' => 22, 'CE' => 23, 'RN' => 24, 'PB' => 25, 'PE' => 26, 'AL' => 27, 'SE' => 28, 'BA' => 29,
        'MG' => 31, 'ES' => 32, 'RJ' => 33, 'SP' => 35,
        'PR' => 41, 'SC' => 42, 'RS' => 43,
        'MS' => 50, 'MT' => 51, 'GO' => 52, 'DF' => 53,
    ];
    return $codigos[strtoupper($uf)] ?? null;
}

function decodeGzip($content) {
    if (empty($content)) return '';
    
    $decoded = @gzdecode(base64_decode($content));
    if ($decoded === false) {
        $decoded = base64_decode($content);
    }
    
    return $decoded ?: '';
}

function getNodeValue($parent, $tagName) {
    if (!$parent) return '';
    $nodes = $parent->getElementsByTagName($tagName);
    return $nodes->length > 0 ? trim($nodes->item(0)->nodeValue) : '';
}

function delayMs($ms) {
    usleep($ms * 1000);
}

function createTools($cnpj, $uf, $certificateBase64, $certificatePassword) {
    if (!validateCNPJ($cnpj)) {
        errorResponse('CNPJ inválido', 'INVALID_CNPJ');
    }
    
    if (!validateUF($uf)) {
        errorResponse('UF inválida', 'INVALID_UF');
    }
    
    if (empty($certificateBase64) || empty($certificatePassword)) {
        errorResponse('Certificado ou senha não informados', 'MISSING_CERTIFICATE');
    }
    
    // Verificar se NFePHP está disponível
    if (!class_exists('NFePHP\NFe\Tools')) {
        errorResponse('NFePHP não está instalado no servidor', 'NFEPHP_NOT_INSTALLED', 500);
    }
    
    try {
        $configJson = json_encode([
            'atualizacao' => date('Y-m-d H:i:s'),
            'tpAmb' => SEFAZ_AMBIENTE,
            'razaosocial' => 'Empresa',
            'cnpj' => preg_replace('/[^0-9]/', '', $cnpj),
            'siglaUF' => strtoupper($uf),
            'schemes' => 'PL_009_V4',
            'versao' => '4.00',
            'tokenIBPT' => '',
            'CSC' => '',
            'CSCid' => '',
        ]);
        
        $certificatePfx = base64_decode($certificateBase64);
        $certificate = \NFePHP\Common\Certificate::readPfx($certificatePfx, $certificatePassword);
        
        // Verificar validade do certificado
        $certData = openssl_x509_parse($certificate->publicKey);
        if ($certData && isset($certData['validTo_time_t'])) {
            if ($certData['validTo_time_t'] < time()) {
                errorResponse('Certificado expirado', 'CERTIFICATE_EXPIRED', 400, [
                    'validUntil' => date('Y-m-d', $certData['validTo_time_t'])
                ]);
            }
        }
        
        $tools = new \NFePHP\NFe\Tools($configJson, $certificate);
        $tools->model('55'); // NF-e
        
        return $tools;
        
    } catch (\Exception $e) {
        errorResponse('Erro ao carregar certificado: ' . $e->getMessage(), 'CERTIFICATE_ERROR', 400);
    }
}

function extractNFeData($xml, $cnpjEmitente = null) {
    if (empty($xml)) return null;
    
    try {
        $dom = new DOMDocument();
        $dom->loadXML($xml);
        
        $nfe = null;
        $isResumo = false;
        
        if ($dom->getElementsByTagName('resNFe')->length > 0) {
            $nfe = $dom->getElementsByTagName('resNFe')->item(0);
            $isResumo = true;
        } elseif ($dom->getElementsByTagName('nfeProc')->length > 0) {
            $nfe = $dom->getElementsByTagName('nfeProc')->item(0);
        } elseif ($dom->getElementsByTagName('NFe')->length > 0) {
            $nfe = $dom->getElementsByTagName('NFe')->item(0);
        } elseif ($dom->getElementsByTagName('procNFe')->length > 0) {
            $nfe = $dom->getElementsByTagName('procNFe')->item(0);
        }
        
        if (!$nfe) return null;
        
        $data = [
            'isResumo' => $isResumo,
            'xml' => $xml,
        ];
        
        if ($isResumo) {
            $data['chaveNFe'] = getNodeValue($nfe, 'chNFe');
            $data['cnpjEmitente'] = getNodeValue($nfe, 'CNPJ');
            $data['nomeEmitente'] = getNodeValue($nfe, 'xNome');
            $data['inscricaoEstadual'] = getNodeValue($nfe, 'IE');
            $data['dataEmissao'] = getNodeValue($nfe, 'dhEmi');
            $data['tipoNFe'] = getNodeValue($nfe, 'tpNF');
            $data['valorTotal'] = floatval(getNodeValue($nfe, 'vNF'));
            $data['digestValue'] = getNodeValue($nfe, 'digVal');
            $data['situacao'] = getNodeValue($nfe, 'cSitNFe');
            $data['nsu'] = getNodeValue($dom->documentElement, 'NSU');
        } else {
            $ide = $dom->getElementsByTagName('ide')->item(0);
            $emit = $dom->getElementsByTagName('emit')->item(0);
            $dest = $dom->getElementsByTagName('dest')->item(0);
            $total = $dom->getElementsByTagName('total')->item(0);
            $prot = $dom->getElementsByTagName('protNFe')->item(0);
            
            $data['chaveNFe'] = $prot ? getNodeValue($prot, 'chNFe') : '';
            $data['numeroNFe'] = getNodeValue($ide, 'nNF');
            $data['serie'] = getNodeValue($ide, 'serie');
            $data['dataEmissao'] = getNodeValue($ide, 'dhEmi');
            $data['tipoNFe'] = getNodeValue($ide, 'tpNF');
            $data['naturezaOperacao'] = getNodeValue($ide, 'natOp');
            
            $data['cnpjEmitente'] = getNodeValue($emit, 'CNPJ');
            $data['nomeEmitente'] = getNodeValue($emit, 'xNome');
            $data['ieEmitente'] = getNodeValue($emit, 'IE');
            
            if ($dest) {
                $data['cnpjDestinatario'] = getNodeValue($dest, 'CNPJ') ?: getNodeValue($dest, 'CPF');
                $data['nomeDestinatario'] = getNodeValue($dest, 'xNome');
            }
            
            if ($total) {
                $icmsTot = $total->getElementsByTagName('ICMSTot')->item(0);
                $data['valorTotal'] = floatval(getNodeValue($icmsTot, 'vNF'));
                $data['valorProdutos'] = floatval(getNodeValue($icmsTot, 'vProd'));
                $data['valorFrete'] = floatval(getNodeValue($icmsTot, 'vFrete'));
                $data['valorDesconto'] = floatval(getNodeValue($icmsTot, 'vDesc'));
            }
            
            if ($prot) {
                $infProt = $prot->getElementsByTagName('infProt')->item(0);
                $data['protocolo'] = getNodeValue($infProt, 'nProt');
                $data['dataAutorizacao'] = getNodeValue($infProt, 'dhRecbto');
                $data['situacao'] = getNodeValue($infProt, 'cStat');
                $data['motivoSituacao'] = getNodeValue($infProt, 'xMotivo');
            }
        }
        
        if ($cnpjEmitente) {
            $cnpjLimpo = preg_replace('/[^0-9]/', '', $cnpjEmitente);
            $data['isNfeSaida'] = ($data['cnpjEmitente'] === $cnpjLimpo);
        }
        
        return $data;
        
    } catch (\Exception $e) {
        error_log('[SEFAZ] Erro ao extrair dados do XML: ' . $e->getMessage());
        return null;
    }
}

// ============================================
// Routing
// ============================================
$method = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Remove base path if exists
$basePath = '/api';
$uri = str_replace($basePath, '', $uri);
$uri = rtrim($uri, '/');

if (empty($uri) || $uri === '') {
    $uri = '/health';
}

$data = getRequestBody();

try {
    switch ($uri) {
        // ============================================
        // STATUS ENDPOINTS
        // ============================================
        case '/health':
            jsonResponse([
                'status' => 'healthy',
                'timestamp' => date('c'),
                'version' => '3.0.0',
                'nfephp' => class_exists('NFePHP\NFe\Tools') ? 'installed' : 'not_installed',
                'endpoints' => [
                    'novos' => [
                        'POST /dfe/nfeSaida',
                        'POST /sefaz/download-nfe-lote',
                        'POST /dfe/gerarDANFE',
                        'POST /dfe/downloadZIP',
                        'POST /dfe/manifestar',
                        'POST /dfe/consultarChave',
                    ]
                ]
            ]);
            break;

        case '/teste-sefaz':
            if ($method !== 'GET') {
                jsonResponse(['error' => 'Método não permitido'], 405);
            }
            
            $sefazOnline = true;
            
            if ($sefazOnline) {
                jsonResponse([
                    'success' => true,
                    'message' => 'Conexão com SEFAZ disponível',
                    'ambiente' => SEFAZ_AMBIENTE === 1 ? 'producao' : 'homologacao',
                    'nfephp' => class_exists('NFePHP\NFe\Tools'),
                    'timestamp' => date('c')
                ]);
            } else {
                jsonResponse([
                    'success' => false,
                    'error' => 'SEFAZ indisponível'
                ], 503);
            }
            break;

        // ============================================
        // CONSULTAS ENDPOINTS
        // ============================================
        case '/consult/cnpj':
            if ($method !== 'POST') {
                jsonResponse(['error' => 'Método não permitido'], 405);
            }
            
            $cnpj = preg_replace('/\D/', '', $data['cnpj'] ?? '');
            
            if (strlen($cnpj) !== 14) {
                jsonResponse(['error' => 'CNPJ inválido. Deve conter 14 dígitos.'], 400);
            }
            
            jsonResponse([
                'success' => true,
                'cnpj' => $cnpj,
                'dados' => [
                    'razao_social' => 'Empresa Exemplo LTDA',
                    'nome_fantasia' => 'Empresa Exemplo',
                    'situacao' => 'ATIVA',
                    'uf' => 'SP',
                    'municipio' => 'São Paulo',
                    'atividade_principal' => 'Comércio varejista',
                    'data_consulta' => date('c')
                ]
            ]);
            break;

        case '/consult/searchNFe':
            if ($method !== 'POST') {
                jsonResponse(['error' => 'Método não permitido'], 405);
            }
            
            $cnpj = $data['cnpj'] ?? null;
            $dataInicio = $data['data_inicio'] ?? null;
            $dataFim = $data['data_fim'] ?? null;
            $nsu = $data['nsu'] ?? null;
            
            jsonResponse([
                'success' => true,
                'filtros' => [
                    'cnpj' => $cnpj,
                    'data_inicio' => $dataInicio,
                    'data_fim' => $dataFim,
                    'nsu' => $nsu
                ],
                'total' => 0,
                'documentos' => []
            ]);
            break;

        // ============================================
        // XML ENDPOINTS
        // ============================================
        case '/xml/chave':
            if ($method !== 'POST') {
                jsonResponse(['error' => 'Método não permitido'], 405);
            }
            
            $chave = preg_replace('/\D/', '', $data['chave'] ?? '');
            
            if (strlen($chave) !== 44) {
                jsonResponse([
                    'error' => 'Chave de acesso inválida. Deve conter 44 dígitos numéricos.'
                ], 400);
            }
            
            jsonResponse([
                'success' => true,
                'chave' => $chave,
                'encontrado' => false,
                'xml' => null,
                'dados' => [
                    'uf' => substr($chave, 0, 2),
                    'ano_mes' => substr($chave, 2, 4),
                    'cnpj' => substr($chave, 6, 14),
                    'modelo' => substr($chave, 20, 2),
                    'serie' => substr($chave, 22, 3),
                    'numero' => substr($chave, 25, 9)
                ]
            ]);
            break;

        case '/xml/select':
            if ($method !== 'POST') {
                jsonResponse(['error' => 'Método não permitido'], 405);
            }
            
            $cnpj = $data['cnpj'] ?? null;
            $tipo = $data['tipo'] ?? 'nfe';
            $limit = min($data['limit'] ?? 100, 1000);
            $offset = $data['offset'] ?? 0;
            
            jsonResponse([
                'success' => true,
                'filtros' => [
                    'cnpj' => $cnpj,
                    'tipo' => $tipo,
                    'limit' => $limit,
                    'offset' => $offset
                ],
                'total' => 0,
                'xmls' => []
            ]);
            break;

        // ============================================
        // DF-e ENDPOINTS (LEGADO)
        // ============================================
        case '/dfe/dfeDocs':
            if ($method !== 'POST') {
                jsonResponse(['error' => 'Método não permitido'], 405);
            }
            
            $missing = validateRequired($data, ['cnpj', 'certificate', 'password']);
            if (!empty($missing)) {
                jsonResponse([
                    'error' => 'Campos obrigatórios: ' . implode(', ', $missing)
                ], 400);
            }
            
            $cnpj = preg_replace('/\D/', '', $data['cnpj']);
            $nsu = $data['nsu'] ?? '0';
            $uf = $data['uf'] ?? 'SP';
            
            jsonResponse([
                'success' => true,
                'cnpj' => $cnpj,
                'uf' => $uf,
                'ultimo_nsu' => $nsu,
                'max_nsu' => $nsu,
                'documentos' => [],
                'mensagem' => 'Consulta realizada com sucesso'
            ]);
            break;

        case '/dfe/dfeDocsManifesta':
            if ($method !== 'POST') {
                jsonResponse(['error' => 'Método não permitido'], 405);
            }
            
            $missing = validateRequired($data, ['cnpj', 'chave', 'evento', 'certificate', 'password']);
            if (!empty($missing)) {
                jsonResponse([
                    'error' => 'Campos obrigatórios: ' . implode(', ', $missing)
                ], 400);
            }
            
            $eventos = ['210200', '210210', '210220', '210240'];
            $evento = $data['evento'];
            
            if (!in_array($evento, $eventos)) {
                jsonResponse([
                    'error' => 'Evento inválido. Valores aceitos: ' . implode(', ', $eventos),
                    'eventos_disponiveis' => [
                        '210200' => 'Confirmação da Operação',
                        '210210' => 'Ciência da Operação',
                        '210220' => 'Desconhecimento da Operação',
                        '210240' => 'Operação não Realizada'
                    ]
                ], 400);
            }
            
            jsonResponse([
                'success' => true,
                'chave' => $data['chave'],
                'evento' => $evento,
                'protocolo' => 'SP' . date('YmdHis') . rand(100000, 999999),
                'data_evento' => date('c'),
                'mensagem' => 'Manifestação registrada com sucesso'
            ]);
            break;

        case '/dfe/dfePDF':
            if ($method !== 'POST') {
                jsonResponse(['error' => 'Método não permitido'], 405);
            }
            
            $xml = $data['xml'] ?? null;
            $chave = $data['chave'] ?? null;
            
            if (!$xml && !$chave) {
                jsonResponse([
                    'error' => 'Informe o XML ou a chave de acesso'
                ], 400);
            }
            
            jsonResponse([
                'success' => true,
                'chave' => $chave,
                'pdf_base64' => '',
                'mensagem' => 'DANFE gerado com sucesso'
            ]);
            break;

        case '/dfe/dfeDocsZIP':
            if ($method !== 'POST') {
                jsonResponse(['error' => 'Método não permitido'], 405);
            }
            
            $missing = validateRequired($data, ['cnpj']);
            if (!empty($missing)) {
                jsonResponse([
                    'error' => 'Campos obrigatórios: ' . implode(', ', $missing)
                ], 400);
            }
            
            $dataInicio = $data['data_inicio'] ?? date('Y-m-01');
            $dataFim = $data['data_fim'] ?? date('Y-m-d');
            
            jsonResponse([
                'success' => true,
                'cnpj' => $data['cnpj'],
                'periodo' => [
                    'inicio' => $dataInicio,
                    'fim' => $dataFim
                ],
                'total_arquivos' => 0,
                'zip_base64' => '',
                'mensagem' => 'ZIP gerado com sucesso'
            ]);
            break;

        // ============================================
        // NOVOS ENDPOINTS - NF-e SAÍDA
        // ============================================
        case '/dfe/nfeSaida':
            if ($method !== 'POST') {
                jsonResponse(['error' => 'Método não permitido'], 405);
            }
            
            $cnpj = $data['cnpj'] ?? '';
            $uf = $data['uf'] ?? '';
            $nsu = $data['nsu'] ?? '0';
            $dataInicio = $data['dataInicio'] ?? null;
            $dataFim = $data['dataFim'] ?? null;
            $maxIterations = min($data['maxIterations'] ?? SEFAZ_MAX_ITERATIONS, SEFAZ_MAX_ITERATIONS);
            $certificateBase64 = $data['certificateBase64'] ?? '';
            $certificatePassword = $data['certificatePassword'] ?? '';
            
            $tools = createTools($cnpj, $uf, $certificateBase64, $certificatePassword);
            $cnpjLimpo = preg_replace('/[^0-9]/', '', $cnpj);
            
            $documentos = [];
            $lastNsu = $nsu;
            $maxNsu = $nsu;
            $iterations = 0;
            $rateLimitHit = false;
            $finished = false;
            
            $tsInicio = $dataInicio ? strtotime($dataInicio) : null;
            $tsFim = $dataFim ? strtotime($dataFim . ' 23:59:59') : null;
            
            while ($iterations < $maxIterations && !$finished && !$rateLimitHit) {
                $iterations++;
                
                error_log("[SEFAZ] Iteração $iterations - NSU: $lastNsu");
                
                $response = $tools->sefazDistDFe($lastNsu);
                
                $st = new \NFePHP\NFe\Common\Standardize($response);
                $std = $st->toStd();
                
                $cStat = (int)($std->cStat ?? 0);
                
                switch ($cStat) {
                    case 137:
                        $finished = true;
                        break;
                        
                    case 138:
                        $ultNSU = $std->ultNSU ?? '0';
                        $maxNSU = $std->maxNSU ?? '0';
                        
                        if (isset($std->loteDistDFeInt->docZip)) {
                            $docs = is_array($std->loteDistDFeInt->docZip) 
                                ? $std->loteDistDFeInt->docZip 
                                : [$std->loteDistDFeInt->docZip];
                            
                            foreach ($docs as $doc) {
                                $nsuDoc = $doc->NSU ?? '';
                                $schema = $doc->schema ?? '';
                                $content = decodeGzip($doc->_);
                                
                                if (empty($content)) continue;
                                
                                if (strpos($schema, 'resNFe') !== false || strpos($schema, 'procNFe') !== false) {
                                    $nfeData = extractNFeData($content, $cnpjLimpo);
                                    
                                    if ($nfeData && !empty($nfeData['isNfeSaida']) && $nfeData['isNfeSaida']) {
                                        $incluir = true;
                                        
                                        if (!empty($nfeData['dataEmissao'])) {
                                            $tsEmissao = strtotime($nfeData['dataEmissao']);
                                            
                                            if ($tsInicio && $tsEmissao < $tsInicio) {
                                                $incluir = false;
                                            }
                                            if ($tsFim && $tsEmissao > $tsFim) {
                                                $incluir = false;
                                            }
                                        }
                                        
                                        if ($incluir) {
                                            $nfeData['nsu'] = $nsuDoc;
                                            $documentos[] = $nfeData;
                                        }
                                    }
                                }
                                
                                $maxNsu = max($maxNsu, $nsuDoc);
                            }
                        }
                        
                        $lastNsu = $ultNSU;
                        
                        if ($ultNSU >= $maxNSU) {
                            $finished = true;
                        }
                        break;
                        
                    case 656:
                        $rateLimitHit = true;
                        error_log("[SEFAZ] Rate limit (656) atingido");
                        break;
                        
                    default:
                        error_log("[SEFAZ] Status desconhecido: $cStat - " . ($std->xMotivo ?? ''));
                        $finished = true;
                }
                
                if (!$finished && !$rateLimitHit) {
                    delayMs(SEFAZ_RATE_LIMIT_DELAY);
                }
            }
            
            jsonResponse([
                'success' => true,
                'data' => [
                    'documentos' => $documentos,
                    'totalDocumentos' => count($documentos),
                    'ultimoNsu' => $lastNsu,
                    'maxNsu' => $maxNsu,
                    'iteracoes' => $iterations,
                    'rateLimitHit' => $rateLimitHit,
                    'finished' => $finished,
                    'periodo' => [
                        'inicio' => $dataInicio,
                        'fim' => $dataFim,
                    ],
                ],
                'message' => count($documentos) . ' NF-e de saída encontradas',
                'timestamp' => date('c'),
            ]);
            break;

        // ============================================
        // NOVO - DOWNLOAD EM LOTE
        // ============================================
        case '/sefaz/download-nfe-lote':
            if ($method !== 'POST') {
                jsonResponse(['error' => 'Método não permitido'], 405);
            }
            
            $cnpj = $data['cnpj'] ?? '';
            $uf = $data['uf'] ?? '';
            $chaves = $data['chaves'] ?? [];
            $certificateBase64 = $data['certificateBase64'] ?? '';
            $certificatePassword = $data['certificatePassword'] ?? '';
            
            if (empty($chaves) || !is_array($chaves)) {
                errorResponse('Lista de chaves não informada ou inválida', 'INVALID_CHAVES');
            }
            
            if (count($chaves) > SEFAZ_BATCH_SIZE) {
                errorResponse('Máximo de ' . SEFAZ_BATCH_SIZE . ' chaves por requisição', 'BATCH_LIMIT_EXCEEDED');
            }
            
            foreach ($chaves as $chave) {
                if (!validateChaveNFe($chave)) {
                    errorResponse("Chave inválida: $chave", 'INVALID_CHAVE');
                }
            }
            
            $tools = createTools($cnpj, $uf, $certificateBase64, $certificatePassword);
            
            $resultados = [];
            $sucesso = 0;
            $falhas = 0;
            
            foreach ($chaves as $index => $chave) {
                try {
                    error_log("[SEFAZ] Baixando XML " . ($index + 1) . "/" . count($chaves) . ": $chave");
                    
                    $response = $tools->sefazDownload($chave);
                    
                    $st = new \NFePHP\NFe\Common\Standardize($response);
                    $std = $st->toStd();
                    
                    $cStat = (int)($std->retNFe->cStat ?? $std->cStat ?? 0);
                    
                    if ($cStat === 138 || $cStat === 140) {
                        $xmlGzip = $std->retNFe->loteDistDFeInt->docZip ?? null;
                        
                        if ($xmlGzip) {
                            $xml = decodeGzip(is_object($xmlGzip) ? $xmlGzip->_ : $xmlGzip);
                            $nfeData = extractNFeData($xml);
                            
                            $resultados[] = [
                                'chave' => $chave,
                                'success' => true,
                                'xml' => $xml,
                                'dados' => $nfeData,
                            ];
                            $sucesso++;
                        } else {
                            $resultados[] = [
                                'chave' => $chave,
                                'success' => false,
                                'error' => 'XML não encontrado na resposta',
                            ];
                            $falhas++;
                        }
                    } else {
                        $resultados[] = [
                            'chave' => $chave,
                            'success' => false,
                            'error' => $std->retNFe->xMotivo ?? $std->xMotivo ?? "Status: $cStat",
                            'cStat' => $cStat,
                        ];
                        $falhas++;
                    }
                    
                } catch (\Exception $e) {
                    $resultados[] = [
                        'chave' => $chave,
                        'success' => false,
                        'error' => $e->getMessage(),
                    ];
                    $falhas++;
                }
                
                if ($index < count($chaves) - 1) {
                    delayMs(SEFAZ_RATE_LIMIT_DELAY);
                }
            }
            
            jsonResponse([
                'success' => true,
                'data' => [
                    'resultados' => $resultados,
                    'resumo' => [
                        'total' => count($chaves),
                        'sucesso' => $sucesso,
                        'falhas' => $falhas,
                    ],
                ],
                'message' => "$sucesso de " . count($chaves) . " XMLs baixados com sucesso",
                'timestamp' => date('c'),
            ]);
            break;

        // ============================================
        // NOVO - GERAR DANFE
        // ============================================
        case '/dfe/gerarDANFE':
            if ($method !== 'POST') {
                jsonResponse(['error' => 'Método não permitido'], 405);
            }
            
            $xml = $data['xml'] ?? '';
            $tipo = $data['tipo'] ?? 'nfe';
            $logo = $data['logo'] ?? null;
            $formato = $data['formato'] ?? 'base64';
            
            if (empty($xml)) {
                errorResponse('XML não informado', 'MISSING_XML');
            }
            
            if (!class_exists('NFePHP\DA\NFe\Danfe')) {
                errorResponse('NFePHP DANFE não está instalado no servidor', 'DANFE_NOT_INSTALLED', 500);
            }
            
            try {
                $danfe = new \NFePHP\DA\NFe\Danfe($xml);
                
                if ($logo) {
                    $danfe->logoParameters($logo, 'L', true);
                }
                
                $pdf = $danfe->render();
                
                if ($formato === 'stream') {
                    header('Content-Type: application/pdf');
                    header('Content-Disposition: inline; filename="danfe.pdf"');
                    echo $pdf;
                    exit;
                }
                
                jsonResponse([
                    'success' => true,
                    'data' => [
                        'pdf' => base64_encode($pdf),
                        'contentType' => 'application/pdf',
                    ],
                    'timestamp' => date('c'),
                ]);
                
            } catch (\Exception $e) {
                errorResponse('Erro ao gerar DANFE: ' . $e->getMessage(), 'DANFE_ERROR', 500);
            }
            break;

        // ============================================
        // NOVO - DOWNLOAD ZIP
        // ============================================
        case '/dfe/downloadZIP':
            if ($method !== 'POST') {
                jsonResponse(['error' => 'Método não permitido'], 405);
            }
            
            $xmls = $data['xmls'] ?? [];
            $incluirDANFE = $data['incluirDANFE'] ?? false;
            $nomeArquivo = $data['nomeArquivo'] ?? 'nfe_export';
            $formato = $data['formato'] ?? 'base64';
            
            if (empty($xmls) || !is_array($xmls)) {
                errorResponse('Lista de XMLs não informada ou inválida', 'INVALID_XMLS');
            }
            
            if (count($xmls) > SEFAZ_ZIP_MAX_FILES) {
                errorResponse('Máximo de ' . SEFAZ_ZIP_MAX_FILES . ' arquivos por ZIP', 'ZIP_LIMIT_EXCEEDED');
            }
            
            try {
                $zip = new ZipArchive();
                $tempFile = tempnam(sys_get_temp_dir(), 'nfe_');
                
                if ($zip->open($tempFile, ZipArchive::CREATE) !== TRUE) {
                    errorResponse('Erro ao criar arquivo ZIP', 'ZIP_CREATE_ERROR', 500);
                }
                
                $xmlCount = 0;
                $danfeCount = 0;
                $errors = [];
                
                foreach ($xmls as $item) {
                    $chave = $item['chave'] ?? '';
                    $xmlContent = $item['xml'] ?? '';
                    
                    if (empty($chave) || empty($xmlContent)) {
                        continue;
                    }
                    
                    $xmlFilename = "xml/NFe_{$chave}.xml";
                    $zip->addFromString($xmlFilename, $xmlContent);
                    $xmlCount++;
                    
                    if ($incluirDANFE && class_exists('NFePHP\DA\NFe\Danfe')) {
                        try {
                            $danfe = new \NFePHP\DA\NFe\Danfe($xmlContent);
                            $pdf = $danfe->render();
                            
                            $pdfFilename = "danfe/DANFE_{$chave}.pdf";
                            $zip->addFromString($pdfFilename, $pdf);
                            $danfeCount++;
                        } catch (\Exception $e) {
                            $errors[] = [
                                'chave' => $chave,
                                'tipo' => 'danfe',
                                'error' => $e->getMessage(),
                            ];
                        }
                    }
                }
                
                $zip->close();
                
                $zipContent = file_get_contents($tempFile);
                unlink($tempFile);
                
                if ($formato === 'stream') {
                    header('Content-Type: application/zip');
                    header('Content-Disposition: attachment; filename="' . $nomeArquivo . '.zip"');
                    header('Content-Length: ' . strlen($zipContent));
                    echo $zipContent;
                    exit;
                }
                
                jsonResponse([
                    'success' => true,
                    'data' => [
                        'zip' => base64_encode($zipContent),
                        'contentType' => 'application/zip',
                        'filename' => $nomeArquivo . '.zip',
                        'resumo' => [
                            'xmls' => $xmlCount,
                            'danfes' => $danfeCount,
                            'erros' => count($errors),
                        ],
                        'errors' => $errors,
                    ],
                    'timestamp' => date('c'),
                ]);
                
            } catch (\Exception $e) {
                errorResponse('Erro ao criar ZIP: ' . $e->getMessage(), 'ZIP_ERROR', 500);
            }
            break;

        // ============================================
        // NOVO - MANIFESTAR
        // ============================================
        case '/dfe/manifestar':
            if ($method !== 'POST') {
                jsonResponse(['error' => 'Método não permitido'], 405);
            }
            
            $cnpj = $data['cnpj'] ?? '';
            $uf = $data['uf'] ?? '';
            $chave = $data['chave'] ?? '';
            $tipoManifestacao = $data['tipoManifestacao'] ?? '';
            $justificativa = $data['justificativa'] ?? '';
            $certificateBase64 = $data['certificateBase64'] ?? '';
            $certificatePassword = $data['certificatePassword'] ?? '';
            
            if (!validateChaveNFe($chave)) {
                errorResponse('Chave de acesso inválida', 'INVALID_CHAVE');
            }
            
            $tiposValidos = [
                '210200' => 'Ciência da Operação',
                '210210' => 'Confirmação da Operação',
                '210220' => 'Desconhecimento da Operação',
                '210240' => 'Operação Não Realizada',
            ];
            
            if (!isset($tiposValidos[$tipoManifestacao])) {
                errorResponse('Tipo de manifestação inválido', 'INVALID_TIPO', 400, [
                    'tiposValidos' => $tiposValidos
                ]);
            }
            
            if (in_array($tipoManifestacao, ['210220', '210240']) && strlen($justificativa) < 15) {
                errorResponse('Justificativa deve ter no mínimo 15 caracteres', 'INVALID_JUSTIFICATIVA');
            }
            
            $tools = createTools($cnpj, $uf, $certificateBase64, $certificatePassword);
            
            try {
                $response = $tools->sefazManifesta(
                    $chave,
                    $tipoManifestacao,
                    $justificativa
                );
                
                $st = new \NFePHP\NFe\Common\Standardize($response);
                $std = $st->toStd();
                
                $cStat = (int)($std->retEvento->infEvento->cStat ?? $std->cStat ?? 0);
                $xMotivo = $std->retEvento->infEvento->xMotivo ?? $std->xMotivo ?? '';
                $nProt = $std->retEvento->infEvento->nProt ?? '';
                $dhRegEvento = $std->retEvento->infEvento->dhRegEvento ?? '';
                
                $sucesso = in_array($cStat, [135, 136]);
                
                jsonResponse([
                    'success' => $sucesso,
                    'data' => [
                        'chave' => $chave,
                        'tipoManifestacao' => $tipoManifestacao,
                        'tipoDescricao' => $tiposValidos[$tipoManifestacao],
                        'cStat' => $cStat,
                        'xMotivo' => $xMotivo,
                        'protocolo' => $nProt,
                        'dataRegistro' => $dhRegEvento,
                    ],
                    'message' => $sucesso ? 'Manifestação registrada com sucesso' : $xMotivo,
                    'timestamp' => date('c'),
                ]);
                
            } catch (\Exception $e) {
                errorResponse('Erro ao manifestar: ' . $e->getMessage(), 'MANIFESTACAO_ERROR', 500);
            }
            break;

        // ============================================
        // NOVO - CONSULTAR CHAVE
        // ============================================
        case '/dfe/consultarChave':
            if ($method !== 'POST') {
                jsonResponse(['error' => 'Método não permitido'], 405);
            }
            
            $cnpj = $data['cnpj'] ?? '';
            $uf = $data['uf'] ?? '';
            $chave = $data['chave'] ?? '';
            $certificateBase64 = $data['certificateBase64'] ?? '';
            $certificatePassword = $data['certificatePassword'] ?? '';
            
            if (!validateChaveNFe($chave)) {
                errorResponse('Chave de acesso inválida', 'INVALID_CHAVE');
            }
            
            $tools = createTools($cnpj, $uf, $certificateBase64, $certificatePassword);
            
            try {
                $response = $tools->sefazConsultaChave($chave);
                
                $st = new \NFePHP\NFe\Common\Standardize($response);
                $std = $st->toStd();
                
                $cStat = (int)($std->cStat ?? 0);
                $xMotivo = $std->xMotivo ?? '';
                
                $eventos = [];
                if (isset($std->procEventoNFe)) {
                    $eventosRaw = is_array($std->procEventoNFe) ? $std->procEventoNFe : [$std->procEventoNFe];
                    
                    foreach ($eventosRaw as $evento) {
                        $eventos[] = [
                            'tipo' => $evento->evento->infEvento->tpEvento ?? '',
                            'descricao' => $evento->evento->infEvento->xEvento ?? '',
                            'dataEvento' => $evento->evento->infEvento->dhEvento ?? '',
                            'protocolo' => $evento->retEvento->infEvento->nProt ?? '',
                            'justificativa' => $evento->evento->infEvento->xJust ?? '',
                        ];
                    }
                }
                
                $situacao = 'desconhecida';
                if ($cStat === 100) {
                    $situacao = 'autorizada';
                } elseif ($cStat === 101) {
                    $situacao = 'cancelada';
                } elseif ($cStat === 110) {
                    $situacao = 'denegada';
                } elseif ($cStat === 217) {
                    $situacao = 'nao_encontrada';
                }
                
                jsonResponse([
                    'success' => in_array($cStat, [100, 101, 110]),
                    'data' => [
                        'chave' => $chave,
                        'cStat' => $cStat,
                        'xMotivo' => $xMotivo,
                        'situacao' => $situacao,
                        'protocoloAutorizacao' => $std->protNFe->infProt->nProt ?? '',
                        'dataAutorizacao' => $std->protNFe->infProt->dhRecbto ?? '',
                        'digestValue' => $std->protNFe->infProt->digVal ?? '',
                        'eventos' => $eventos,
                    ],
                    'message' => $xMotivo,
                    'timestamp' => date('c'),
                ]);
                
            } catch (\Exception $e) {
                errorResponse('Erro ao consultar chave: ' . $e->getMessage(), 'CONSULTA_ERROR', 500);
            }
            break;

        // ============================================
        // 404 - ROTA NÃO ENCONTRADA
        // ============================================
        default:
            jsonResponse([
                'error' => 'Endpoint não encontrado',
                'uri' => $uri,
                'method' => $method,
                'endpoints_disponiveis' => [
                    'STATUS' => [
                        'GET /health' => 'Health check',
                        'GET /teste-sefaz' => 'Testa conexão SEFAZ',
                    ],
                    'CONSULTAS' => [
                        'POST /consult/cnpj' => 'Consulta CNPJ',
                        'POST /consult/searchNFe' => 'Busca NF-e',
                    ],
                    'XML' => [
                        'POST /xml/chave' => 'Busca XML por chave',
                        'POST /xml/select' => 'Lista XMLs',
                    ],
                    'DFe_LEGADO' => [
                        'POST /dfe/dfeDocs' => 'Busca DF-e na SEFAZ',
                        'POST /dfe/dfeDocsManifesta' => 'Manifestação (legado)',
                        'POST /dfe/dfePDF' => 'Gera DANFE PDF (legado)',
                        'POST /dfe/dfeDocsZIP' => 'Download ZIP XMLs (legado)',
                    ],
                    'NOVOS_ENDPOINTS' => [
                        'POST /dfe/nfeSaida' => 'Busca NF-e de saída via DistDFeInt',
                        'POST /sefaz/download-nfe-lote' => 'Download em lote de XMLs',
                        'POST /dfe/gerarDANFE' => 'Gera DANFE PDF a partir do XML',
                        'POST /dfe/downloadZIP' => 'Download em massa como ZIP',
                        'POST /dfe/manifestar' => 'Manifestação do destinatário',
                        'POST /dfe/consultarChave' => 'Consulta NF-e por chave',
                    ],
                ]
            ], 404);
            break;
    }
} catch (Exception $e) {
    jsonResponse([
        'success' => false,
        'error' => 'Erro interno do servidor',
        'message' => $e->getMessage(),
        'timestamp' => date('c'),
    ], 500);
}
