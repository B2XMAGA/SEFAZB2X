<?php
ob_start();

/**
 * B2X SEFAZ API
 * Sistema de integração com SEFAZ para NF-e
 */

// ============================================
// AUTOLOAD E CONFIGURAÇÕES
// ============================================
require __DIR__ . "/vendor/autoload.php";
require_once __DIR__ . "/source/Config.php";  // ← ALTERADO: require para require_once

use CoffeeCode\Router\Router;

// ============================================
// INICIALIZAÇÃO DO ROUTER
// ============================================
$router = new Router(URL_BASE);

// ============================================
// ROTA DE TESTE / HEALTH CHECK
// ============================================
$router->namespace("Source\Controllers");

$router->get("/", function() {
    header('Content-Type: application/json');
    echo json_encode([
        "status" => "online",
        "service" => "B2X SEFAZ API",
        "version" => "1.0.0",
        "timestamp" => date('Y-m-d H:i:s')
    ]);
});

$router->get("/health", function() {
    header('Content-Type: application/json');
    echo json_encode([
        "status" => "healthy",
        "timestamp" => date('Y-m-d H:i:s')
    ]);
});

// ============================================
// ROTAS DE MANIFESTAÇÃO NFE
// ============================================
$router->group("/nfe");

$router->post("/manifestar", "NfeController:manifestar");
$router->post("/consultar", "NfeController:consultar");
$router->post("/download", "NfeController:download");
$router->get("/status", "NfeController:status");

// ============================================
// ROTAS DE CONSULTA SEFAZ
// ============================================
$router->group("/sefaz");

$router->post("/consulta-nfe", "SefazController:consultaNfe");
$router->post("/consulta-destinadas", "SefazController:consultaDestinatario");
$router->post("/manifestacao", "SefazController:manifestacao");
$router->get("/status-servico", "SefazController:statusServico");

// ============================================
// ROTAS DE CERTIFICADO
// ============================================
$router->group("/certificado");

$router->post("/upload", "CertificadoController:upload");
$router->post("/validar", "CertificadoController:validar");
$router->get("/info/{cnpj}", "CertificadoController:info");

// ============================================
// ROTAS DE NOTAS FISCAIS
// ============================================
$router->group("/notas");

$router->get("/pendentes/{cnpj}", "NotasController:pendentes");
$router->get("/manifestadas/{cnpj}", "NotasController:manifestadas");
$router->post("/sincronizar", "NotasController:sincronizar");

// ============================================
// ROTAS DE CTE
// ============================================
$router->group("/cte");

$router->post("/consultar", "CteController:consultar");
$router->get("/pendentes/{cnpj}", "CteController:pendentes");

// ============================================
// ROTAS DE WEBHOOK/CALLBACK
// ============================================
$router->group("/webhook");

$router->post("/sefaz", "WebhookController:sefaz");
$router->post("/notificacao", "WebhookController:notificacao");

// ============================================
// API GENÉRICA
// ============================================
$router->group("/api");

$router->get("/ping", function() {
    header('Content-Type: application/json');
    echo json_encode(["pong" => true, "time" => time()]);
});

$router->post("/test", function() {
    header('Content-Type: application/json');
    $data = json_decode(file_get_contents('php://input'), true);
    echo json_encode([
        "received" => $data,
        "status" => "ok"
    ]);
});

// ============================================
// EXECUÇÃO DO ROUTER
// ============================================
$router->dispatch();

// ============================================
// TRATAMENTO DE ERROS
// ============================================
if ($router->error()) {
    $error = $router->error();
    
    header('Content-Type: application/json');
    http_response_code($error);
    
    $messages = [
        400 => "Requisição inválida",
        401 => "Não autorizado",
        403 => "Acesso negado",
        404 => "Rota não encontrada",
        405 => "Método não permitido",
        500 => "Erro interno do servidor",
        501 => "Não implementado"
    ];
    
    echo json_encode([
        "error" => true,
        "code" => $error,
        "message" => $messages[$error] ?? "Erro desconhecido",
        "path" => $_SERVER['REQUEST_URI'] ?? "/"
    ]);
}

ob_end_flush();
