<?php
/**
 * API SEFAZ B2X
 * Servidor PHP para comunicação com SEFAZ
 * 
 * Endpoints disponíveis:
 * - GET  /health          - Health check
 * - GET  /teste-sefaz     - Testa conexão SEFAZ
 * - POST /consult/cnpj    - Consulta CNPJ
 * - POST /xml/chave       - Busca XML por chave
 * - POST /dfe/dfeDocs     - Busca DF-e na SEFAZ
 * - POST /dfe/dfeDocsManifesta - Manifestação
 * - POST /dfe/dfePDF      - Gera DANFE PDF
 * - POST /dfe/dfeDocsZIP  - Download ZIP XMLs
 * - POST /consult/searchNFe - Busca NF-e
 * - POST /xml/select      - Lista XMLs
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

// ============================================
// Helpers
// ============================================
function jsonResponse($data, $status = 200) {
    http_response_code($status);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit();
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

// ============================================
// Routing
// ============================================
$method = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Remove base path if exists
$basePath = '/api'; // Ajuste conforme seu servidor
$uri = str_replace($basePath, '', $uri);
$uri = rtrim($uri, '/');

// Se URI vazia, assume /health
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
                'version' => '2.0.0'
            ]);
            break;

        case '/teste-sefaz':
            if ($method !== 'GET') {
                jsonResponse(['error' => 'Método não permitido'], 405);
            }
            
            // Simula teste de conexão com SEFAZ
            // Em produção, você faria um teste real com a API da SEFAZ
            $sefazOnline = true; // Substitua por teste real
            
            if ($sefazOnline) {
                jsonResponse([
                    'success' => true,
                    'message' => 'Conexão com SEFAZ disponível',
                    'ambiente' => 'producao',
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
            
            // Em produção: consultar ReceitaWS, SINTEGRA, ou API similar
            // Exemplo de resposta simulada:
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
            
            // Parâmetros de busca
            $cnpj = $data['cnpj'] ?? null;
            $dataInicio = $data['data_inicio'] ?? null;
            $dataFim = $data['data_fim'] ?? null;
            $nsu = $data['nsu'] ?? null;
            
            // Em produção: buscar no banco de dados ou SEFAZ
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
            
            // Em produção: buscar XML no banco ou fazer consulta na SEFAZ
            // Por enquanto retorna resposta simulada
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
            $tipo = $data['tipo'] ?? 'nfe'; // nfe, cte, nfse
            $limit = min($data['limit'] ?? 100, 1000);
            $offset = $data['offset'] ?? 0;
            
            // Em produção: buscar XMLs no banco de dados
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
        // DF-e ENDPOINTS
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
            
            // Em produção: usar biblioteca como sped-nfe para consultar SEFAZ
            // Aqui retornamos resposta de exemplo
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
            
            // Em produção: enviar manifestação para SEFAZ
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
            
            // Em produção: usar biblioteca como FPDF ou mPDF para gerar o DANFE
            // Retornar PDF em base64
            jsonResponse([
                'success' => true,
                'chave' => $chave,
                'pdf_base64' => '', // Base64 do PDF
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
            
            // Em produção: criar arquivo ZIP com XMLs do período
            jsonResponse([
                'success' => true,
                'cnpj' => $data['cnpj'],
                'periodo' => [
                    'inicio' => $dataInicio,
                    'fim' => $dataFim
                ],
                'total_arquivos' => 0,
                'zip_base64' => '', // Base64 do ZIP
                'mensagem' => 'ZIP gerado com sucesso'
            ]);
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
                    'GET /health' => 'Health check',
                    'GET /teste-sefaz' => 'Testa conexão SEFAZ',
                    'POST /consult/cnpj' => 'Consulta CNPJ',
                    'POST /consult/searchNFe' => 'Busca NF-e',
                    'POST /xml/chave' => 'Busca XML por chave',
                    'POST /xml/select' => 'Lista XMLs',
                    'POST /dfe/dfeDocs' => 'Busca DF-e na SEFAZ',
                    'POST /dfe/dfeDocsManifesta' => 'Manifestação',
                    'POST /dfe/dfePDF' => 'Gera DANFE PDF',
                    'POST /dfe/dfeDocsZIP' => 'Download ZIP XMLs'
                ]
            ], 404);
            break;
    }
} catch (Exception $e) {
    jsonResponse([
        'error' => 'Erro interno do servidor',
        'message' => $e->getMessage()
    ], 500);
}
