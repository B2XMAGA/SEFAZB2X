<?php
date_default_timezone_set('America/Sao_Paulo');
ini_set('display_errors', 'On');
ini_set('error_reporting', 'E_ALL');
echo "<meta HTTP-EQUIV='refresh' CONTENT='10;URL=dfeMigrate.php'>";
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

$resultCron = $db->db()->from('dfe')
    ->where('dfe_status')->isNot(0)
    ->limit(50)
    ->select(['dfe_id'])
    ->all();

if ($resultCron) {
    foreach ($resultCron as $rCron) {
        $result = $db->db()->from('dfe')
			->where('dfe_id')->is($rCron->dfe_id)
			->limit(1)
			->select()
			->all();
		if($result){
			foreach($result as $r){
				$createDfe['dfe_id'] = $r->dfe_id;
				$createDfe['dfe_ide_client'] = $r->dfe_ide_client;
				$createDfe['dfe_nsu'] = $r->dfe_nsu;
				$createDfe['dfe_doc'] = $r->dfe_doc;
				$createDfe['dfe_status'] = $r->dfe_status;
				$createDfe['dfe_schema'] = $r->dfe_schema;
				if($db->db()->insert($createDfe)->into('dfe_migrate')){
					$db->db()->from('dfe')->where('dfe_id')->is($r->dfe_id)->delete();
				}
			}
		}	
    }
}
