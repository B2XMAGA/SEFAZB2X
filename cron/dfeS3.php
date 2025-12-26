<?php
date_default_timezone_set('America/Sao_Paulo');
ini_set('display_errors', 'On');
ini_set('error_reporting', 'E_ALL');
//echo "<meta HTTP-EQUIV='refresh' CONTENT='20;URL=dfeS3.php'>";
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
    ->where('dfe_status')->is(0)
	->andWhere('dfe_md5')->isNull()
    ->limit(50)
    ->select(['dfe_id'])
    ->all();

if ($resultCron) {
    foreach ($resultCron as $rCron) {
		$md5XML = md5(date('Y-m-d H:i:s').rand(100000,999999));
		$form_doc['dfe_md5'] = $md5XML;
		
		$db->db()->update('dfe')->where('dfe_id')->is($rCron->dfe_id)->set($form_doc);
		echo 'ok';
        /*$result = $db->db()->from('dfe')
			->where('dfe_id')->is($rCron->dfe_id)
			->limit(1)
			->select()
			->all();*/
		/*if($result){
			foreach($result as $r){
				
				
			}
		}*/	
    }
}
