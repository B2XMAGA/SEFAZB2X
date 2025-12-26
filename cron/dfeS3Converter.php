<?php
date_default_timezone_set('America/Sao_Paulo');
ini_set('display_errors', 'On');
ini_set('error_reporting', 'E_ALL');
//echo "<meta HTTP-EQUIV='refresh' CONTENT='2;URL=dfeMigrate.php'>";
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

$resultCron = $db->db()->from('doc')
    ->where('doc_status_s3')->isNull()
	->andWhere('doc_ide')->isNull()
    ->limit(500)
    ->select(['doc_id'])
    ->all();

if ($resultCron) {
    foreach ($resultCron as $rCron) {
		$md5XML = md5(date('Y-m-d H:i:s').rand(100000,999999));
		$form_doc['doc_ide'] = $md5XML;
		
		$db->db()->update('doc')->where('doc_id')->is($rCron->doc_id)->set($form_doc);
		echo 'ok';	
    }
}
