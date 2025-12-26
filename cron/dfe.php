<?php
date_default_timezone_set('America/Sao_Paulo');
ini_set('display_errors', 'On');
ini_set('error_reporting', 'E_ALL');
require_once '../vendor/autoload.php';

use Source\Conn\DataLayer;
use NFePHP\NFe\Tools;
use NFePHP\Common\Certificate;

function createTemp($base64)
{
    $decode = base64_decode($base64);
    $nameCertificate = md5(date('Y-m-dH:i:s').rand(1000,9999)).'.pfx';
    file_put_contents(__DIR__.'/../shared/certificate/'.$nameCertificate,$decode);

    return $nameCertificate;
}

$db = new DataLayer();

$dateH = date('H');

$resultCron = $db->db()->from('client')
    ->where('client_dfe_use')->is(1)
    ->andWhere('client_status')->is(0)
    ->andWhere('client_forcar_download')->is(1)
    ->orderBy('client_id')
    ->limit(1)
    ->select()
    ->all();

if($resultCron){
    foreach($resultCron as $rCron){
        $url = 'https://api.apisimples.com.br/v1/cron/dfeSearchId.php?n='.$rCron->client_id; // Substitua pela URL correta

        $ch = curl_init(); // Inicializa a sessão curl
        curl_setopt($ch, CURLOPT_URL, $url); // Define a URL da requisição
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Configura para retornar o resultado em vez de exibir

        $response = curl_exec($ch); // Executa a requisição
        if (curl_errno($ch)) {
            echo 'Erro no cURL: ' . curl_error($ch);
        } else {
            echo 'Resposta: ' . $response; // Exibe a resposta da requisição
        }

        curl_close($ch);
    }
}