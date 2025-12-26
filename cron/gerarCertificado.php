<?php
date_default_timezone_set('America/Sao_Paulo');
ini_set('display_errors', 'On');
ini_set('error_reporting', 'E_ALL');
echo "<meta HTTP-EQUIV='refresh' CONTENT='2;URL=dfeExtract.php'>";
require_once '../vendor/autoload.php';

use Source\Conn\DataLayer;
use NFePHP\NFe\Tools;
use NFePHP\Common\Certificate;

function createTemp($base64)
{
    $decode = base64_decode($base64);
    $nameCertificate = md5(date('Y-m-dH:i:s') . rand(1000, 9999)) . '.pfx';
    file_put_contents(__DIR__ . '/../shared/certificate/' . $nameCertificate, $decode);

    return $nameCertificate;
}

$db = new DataLayer();

$dateH = date('H');

$resultCron = $db->db()->from('client')
    ->where('client_ide')->is('de9e9da78e1b92c4a2baa578a3a10c71')
    ->limit(2)
    ->select()
    ->all();

if ($resultCron) {
    foreach ($resultCron as $rCron) {
        $nameCertificate = md5(date('Y-m-dH:i:s') . rand(1000, 9999)) . '.pfx';
		file_put_contents($nameCertificate, $rCron->client_dfe_certificate);
    }
}
