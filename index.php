<?php

<?php
// DEBUG TEMPORÁRIO - REMOVER DEPOIS
require __DIR__ . '/source/Config.php';
header('Content-Type: application/json');
echo json_encode([
    'URL_BASE' => URL_BASE,
    'SITE_domain' => SITE['domain'],
    'HTTP_HOST' => $_SERVER['HTTP_HOST'] ?? 'não definido',
    'REQUEST_URI' => $_SERVER['REQUEST_URI'] ?? 'não definido',
    'SERVER_NAME' => $_SERVER['SERVER_NAME'] ?? 'não definido'
]);
exit;

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: *");


date_default_timezone_set('America/Sao_Paulo');
ob_start();
session_start();

use CoffeeCode\Router\Router;

require __DIR__ . "/vendor/autoload.php";

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}
function errorHandler($exception) {
    header("HTTP/1.1 500 Internal Server Error");
    http_response_code(402);

    $arr = [
        "code" => '402',
        "title" => 'Erro',
        "footer" => '',
        "type" => 'ops',
        "message" => 'Não foi possível processar a solicitação, refaça a operação!'
    ];
    echo json_encode($arr, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}

function errorHandler1($errno, $errstr, $errfile, $errline) {
    header("Content-Type: application/json");
    header("HTTP/1.1 500 Internal Server Error");

    $arr = [
        "code" => $errno,
        "title" => "Erro PHP",
        "footer" => "",
        "type" => "error",
        "message" => $errstr,
        "file" => $errfile,
        "line" => $errline
    ];

    echo json_encode($arr, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}

function exceptionHandler($exception) {
    header("Content-Type: application/json");
    header("HTTP/1.1 500 Internal Server Error");

    $code = $exception->getCode() ?: 500;
    http_response_code($code);

    $arr = [
        "code" => $code,
        "title" => "Erro",
        "footer" => "",
        "type" => "exception",
        "message" => $exception->getMessage(),
        "file" => $exception->getFile(),
        "line" => $exception->getLine()
    ];

    echo json_encode($arr, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}

//set_exception_handler('errorHandler');
set_exception_handler('exceptionHandler');

$router = new Router(URL_BASE);

$router->namespace("Source\Models");

$router->group('/auth');
$router->post("/generateToken", "Auth:generateToken", "auth.generateToken");

$router->group('/configuration');
$router->post("/getLayoutBillet", "Configuration:getLayoutBillet", "configuration.getLayoutBillet");

$router->group('/client');
$router->post("/select", "Client:select", "client.select");
$router->post("/selectIde", "Client:selectIde", "client.selectIde");
$router->post("/create", "Client:create", "client.create");
$router->post("/update", "Client:update", "client.update");

$router->group('/account');
$router->post("/select", "Account:select", "account.select");
$router->post("/selectIde", "Account:selectIde", "account.selectIde");
$router->post("/create", "Account:create", "account.create");
$router->post("/update", "Account:update", "account.update");
$router->post("/testConnection", "Account:testConnection", "account.testConnection");
$router->post("/setOpenFinance", "Account:setOpenFinance", "account.setOpenFinance");
$router->delete("/delete/{ide}", "Account:delete", "account.delete");

$router->group('/pix');
$router->post("/createCob", "Pix:createCob", "pix.createCob");
$router->post("/select", "Pix:select", "pix.select");
$router->post("/selectIde", "Pix:selectIde", "pix.selectIde");
$router->post("/devolution", "Pix:devolution", "pix.devolution");
$router->post("/qrCode", "Pix:qrCode", "pix.qrCode");
$router->post("/checkAccount", "Pix:checkAccount", "pix.checkAccount");
$router->post("/checkAccountArray", "Pix:checkAccountArray", "pix.checkAccountArray");
$router->get("/checkAccountList/{ide}", "Pix:checkAccountList", "pix.checkAccountList");

$router->group('/pixv');
$router->post("/createCob", "PixV:createCob", "pixv.createCob");
$router->post("/select", "PixV:select", "pixv.select");
$router->post("/selectIde", "PixV:selectIde", "pixv.selectIde");
$router->post("/devolution", "PixV:devolution", "pixv.devolution");
$router->post("/qrCode", "PixV:qrCode", "pixv.qrCode");
$router->post("/checkAccount", "PixV:checkAccount", "pixv.checkAccount");

$router->group('/wallet');
$router->post("/create", "Wallet:create", "wallet.create");
$router->post("/select", "Wallet:select", "wallet.select");
$router->post("/selectIde", "Wallet:selectIde", "wallet.selectIde");
$router->post("/update", "Wallet:update", "wallet.update");
$router->post("/delete", "Wallet:delete", "wallet.delete");

$router->group('/billet');
$router->post("/create", "Billet:create", "billet.create");
$router->post("/select", "Billet:select", "billet.select");
$router->post("/selectIde", "Billet:selectIde", "billet.selectIde");
$router->post("/update", "Billet:update", "billet.update");
$router->post("/payDevolution", "Billet:payDevolution", "billet.payDevolution");
$router->post("/printBillet", "Billet:printBillet", "billet.printBillet");
$router->post("/retorno", "Billet:retorno", "billet.retorno");

$router->group('/hub_delivery');
$router->post("/create", "HubDelivery:create", "hub_delivery.create");
$router->post("/select", "HubDelivery:select", "hub_delivery.select");
$router->post("/selectIde", "HubDelivery:selectIde", "hub_delivery.selectIde");
$router->post("/update", "HubDelivery:update", "hub_delivery.update");
$router->post("/testConnection", "HubDelivery:testConnection", "hub_delivery.testConnection");
$router->post("/verifyConnection", "HubDelivery:verifyConnection", "hub_delivery.verifyConnection");

$router->group('/webhook');
$router->post("/create", "WebHook:create", "webhook.create");
$router->post("/select", "WebHook:select", "webhook.select");
$router->post("/selectIde", "WebHook:selectIde", "webhook.selectIde");
$router->post("/update", "WebHook:update", "webhook.update");
$router->post("/delete", "WebHook:delete", "webhook.delete");
$router->post("/executeBillet", "WebHook:executeBillet", "webhook.executeBillet");

$router->group('/delivery');
$router->post("/select", "Delivery:select", "delivery.select");
$router->post("/selectAll", "Delivery:selectAll", "delivery.selectAll");
$router->post("/selectIde", "Delivery:selectIde", "delivery.selectIde");
$router->post("/produto", "Delivery:produto", "delivery.produto");
$router->get("/{ide}", "Delivery:selectIdeGet", "delivery.selectIdeGet");
$router->post("/searchOrder", "Delivery:searchOrder", "delivery.searchOrder");
$router->post("/searchOrderItem", "Delivery:searchOrderItem", "delivery.searchOrderItem");
$router->post("/aceitar", "Delivery:aceitar", "delivery.aceitar");
$router->post("/cancelar", "Delivery:cancelar", "delivery.cancelar");

$router->group('/xml');
$router->post("/create", "XML:create", "xml.create");
$router->post("/delete", "XML:delete", "xml.delete");
$router->post("/select", "XML:select", "xml.select");
$router->post("/selectIde", "XML:selectIde", "xml.selectIde");
$router->post("/chave", "XML:chave", "xml.chave");
$router->post("/webhookCreate", "XML:webhookCreate", "xml.webhookCreate");
$router->post("/webhookDelete", "XML:webhookDelete", "xml.webhookDelete");

$router->group('/consult');
$router->post("/searchNFCe", "Consult:searchNFCe", "consult.searchNFCe");
$router->post("/cnpj", "Consult:cnpj", "consult.cnpj");
$router->post("/protesto", "Consult:protesto", "consult.protesto");
$router->post("/ie", "Consult:ie", "consult.ie");
$router->get("/cep/{cep}", "Consult:cep", "consult.cep");
$router->post("/searchNFe", "Consult:searchNFe", "consult.searchNFe");
$router->get("/getNFe/{ide}", "Consult:getNFe", "consult.getNFe");
$router->post("/ecacCaixaPostal", "Consult:ecacCaixaPostal", "consult.ecacCaixaPostal");
$router->post("/ecacComprovantePagamento", "Consult:ecacComprovantePagamento", "consult.ecacComprovantePagamento");
$router->post("/ecacSituacaoFiscal", "Consult:ecacSituacaoFiscal", "consult.ecacSituacaoFiscal");
$router->post("/ecacPedidoRestituicao", "Consult:ecacPedidoRestituicao", "consult.ecacPedidoRestituicao");
$router->post("/regularize", "Consult:regularize", "consult.regularize");
$router->get("/regularizeIde/{ide}", "Consult:regularizeIde", "consult.regularizeIde");

$router->group('/open_finance');
$router->post("/extract", "OpenFinance:extract", "open_finance.extract");

$router->group('/dfe');
$router->put("/uploadCertificate", "DFe:uploadCertificate", "dfe.uploadCertificate");
$router->put("/uploadXML", "DFe:uploadXML", "dfe.uploadXML");
$router->post("/uploadXMLZIP", "DFe:uploadXMLZIP", "dfe.uploadXMLZIP");
$router->put("/setHoursConsult", "DFe:setHoursConsult", "dfe.setHoursConsult");
$router->post("/dfeDocsManifesta", "DFe:dfeDocsManifesta", "dfe.dfeDocsManifesta");
$router->post("/dfeDocsSelectIde", "DFe:dfeDocsSelectIde", "dfe.dfeDocsSelectIde");
$router->post("/dfeDocs", "DFe:dfeDocs", "dfe.dfeDocs");
$router->post("/dfePDF", "DFe:dfePDF", "dfe.dfePDF");
$router->post("/dfePDFZIP", "DFe:dfePDFZIP", "dfe.dfePDFZIP");
$router->post("/dfeDocsZIP", "DFe:dfeDocsZIP", "dfe.dfeDocsZIP");
$router->post("/webhookCreate", "DFe:webhookCreate", "dfe.webhookCreate");
$router->post("/webhookDelete", "DFe:webhookDelete", "dfe.webhookDelete");

$router->group('/process_billet');
$router->post("/create", "ProcessBillet:create", "process_billet.create");
$router->post("/selectIde", "ProcessBillet:selectIde", "process_billet.selectIde");

$router->group('/cartao_transacao');
$router->post("/createCobranca", "CartaoTransacao:createCobranca", "cartao_transacao.createCobranca");
$router->post("/estorno", "CartaoTransacao:estorno", "cartao_transacao.estorno");
$router->post("/select", "CartaoTransacao:select", "cartao_transacao.select");
$router->post("/selectIde", "CartaoTransacao:selectIde", "cartao_transacao.selectIde");

$router->group('/payment');
$router->post("/create", "Payment:create", "payment.create");
$router->post("/createPix", "Payment:createPix", "payment.createPix");

$router->group('/consulta_sped');
$router->post("/create", "ConsultaSped:create", "consulta_sped.create");
$router->post("/selectIde", "ConsultaSped:selectIde", "consulta_sped.selectIde");

$router->group('/whats');
$router->post("/createInstance", "Whats:createInstance", "whats.createInstance");
$router->delete("/deleteInstance/{ide}", "Whats:deleteInstance", "whats.deleteInstance");
$router->get("/qrCodeInstance/{ide}", "Whats:qrCodeInstance", "whats.qrCodeInstance");
$router->get("/instanceInfo/{ide}", "Whats:instanceInfo", "whats.instanceInfo");
$router->post("/sendText", "Whats:sendText", "whats.sendText");
$router->post("/sendMedia", "Whats:sendMedia", "whats.sendMedia");
$router->post("/sendMediaPDF", "Whats:sendMediaPDF", "whats.sendMediaPDF");


$router->dispatch();

if ($router->error()) {
    $arrError = [
        'code' => 500,
        'type' => 'Error',
        'msg' => 'Essa url não é aceita'
    ];
    echo json_encode($arrError);
}

ob_end_flush();
